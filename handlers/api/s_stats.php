<?php

/*
подписчики (subscribers) - кто подписан на текущего юзера
отписавшиеся (unsubscribers) - кто отписался от текущего юзера

подписки (friends) - на кого подписан текущий юзер
отписки (unfriends) - от кого юзер отписался

Входные данные - параметры get или post.
1) type=uid&login=user_login
   возвращает uid пользователя user_login

2) type=periods&uid=user_uid
   возвращает периоды статистики для юзера с uid

3) type=stats&uid=user_uid&period=MM.YYYY
   возвращает краткую статистику за месяц MM.YYYY для юзера с uid по дням
   Краткая статистика:
     количество подписчиков (cnt_subscribers), количество новых подписчиков (cnt_new_subscribers), количество отписавшихся (cnt_unsubscribers)
     количество подписок (cnt_friends), кол-во новых подписок (cnt_new_friends), количество отписок (cnt_unfriends)
     количество сообщений.

4) type=details&uid=user_id&period=DD.MM.YYYY
   возвращает подробную статистику на дату DD.MM.YYYY
   Подробная статистика:
     список подписчиков (subscribers), список новых подписчиков (new_subscribers), список отписавшихся (unsubscribers)
     список подписок (friends), список новых подписок (new_friends), список отписок (unfriends)
     список сообщений.

К каждому запросу можно добавить формат выходных данных
1) f=json (по-умолчанию) - данные вернутся в json
2) f=xml - данные вернутся в xml
 */

class s_stats extends a_sub_handler_ra {

  /**
   *
   * @var p_api
   */
  public $handler;

  /**
   * type=uid&login=user_login
   * возвращает uid пользователя user_login
   */
  function get_uid(){
    $login=$_REQUEST['login'];
    $s_login=tosql($login);
    $sql="SELECT uid
          FROM users
          WHERE uname=$s_login";
    $res=$this->db->get_array_first_record($sql);
    if (empty($res['uid'])){
      $sql="INSERT IGNORE INTO users
            (uname, first_date, last_date, count_show)
          VALUES
            ($s_login, NOW(), NOW(), 0)";
      $this->db->db_query($sql);
    }
    return $res;
  }

  /**
   * type=periods&uid=user_uid
   * возвращает периоды статистики для юзера с uid
   */
  function get_periods(){
    $uid=$_REQUEST['uid'];
    $sql="SELECT
            min(date) min_date, max(date) max_date
          FROM friends
          WHERE uid=".tosql($uid);
    $res=$this->db->get_array_first_record($sql);
    $min_date=$res['min_date'];
    $max_date=$res['max_date'];

    $cur_time=strtotime($min_date);
    $end_time=strtotime($max_date);
    $end_time=mktime(0,0,0,date('m',$end_time)+1,1,date('Y',$end_time));

    $res=array();
    while ($cur_time<$end_time){
      $tmp['period']=date('m.Y',$cur_time);
      $tmp['str']=$this->date_strmonth(date('n',$cur_time)).' '.date('Y',$cur_time);
      $res[]=$tmp;
      $cur_time=mktime(0,0,0,date('m',$cur_time)+1,1,date('Y',$cur_time));
    }
    return $res;
  }

  private function date_strmonth($month) {
    $monts=array(1=>"январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь");
    $month += 0;
    return $monts[($month)];
  }

  private function make_new_friends($in_arr){
    $prev_friends=array();
    $prev_subscribers=array();
    foreach ($in_arr as $k=>$v){
      $in_arr[$k]['friends']=unserialize($v['friends']);
      $in_arr[$k]['friends']=$in_arr[$k]['friends'][0]['unames'];
      $in_arr[$k]['new_friends']=array_diff($in_arr[$k]['friends'], $prev_friends);
      $in_arr[$k]['unfriends']=array_diff($prev_friends, $in_arr[$k]['friends']);


      $in_arr[$k]['subscribers']=unserialize($v['subscribers']);
      $in_arr[$k]['subscribers']=$in_arr[$k]['subscribers'][0]['unames'];
      $in_arr[$k]['new_subscribers']=array_diff($in_arr[$k]['subscribers'], $prev_subscribers);
      $in_arr[$k]['unsubscribers']=array_diff($prev_subscribers, $in_arr[$k]['subscribers']);

      $prev_friends=$in_arr[$k]['friends'];
      $prev_subscribers=$in_arr[$k]['subscribers'];
    }

    return $in_arr;
  }

  /**
   * type=stats&uid=user_uid&period=MM.YYYY
     возвращает краткую статистику за месяц MM.YYYY для юзера с uid по дням
     Краткая статистика:
       количество подписчиков, количество новых подписчиков,
       количество подписок, кол-во новых подписок,
       количество сообщений.
   */
  function get_stats(){
    $uid=$_REQUEST['uid'];
    $period='01.'.$_REQUEST['period'];

    $cur_time=strtotime($period);
    $sql_period_0=date('Y-m-01',$cur_time);
    $s_sql_period_0=tosql($sql_period_0);

    //захватим предыдущий день, чтобы можно было узнать новых подписчиков
    $sql_period_1=date('Y-m-d',mktime(0,0,0,date('m',$cur_time),date('d',$cur_time)-1,date('Y',$cur_time)));
    $s_sql_period_1=tosql($sql_period_1);

    $sql_period_2=date('Y-m-d',mktime(0,0,0,date('m',$cur_time)+1,1,date('Y',$cur_time)));
    $s_sql_period_2=tosql($sql_period_2);

    $sql="SELECT
            date, friends, subscribers
          FROM friends
          WHERE date>=$s_sql_period_1 and date<$s_sql_period_2 and uid=".tosql($uid);
    $res=$this->db->get_array($sql);
    //echo $sql.'\n';print_r($res);
    if (empty($res)) return array();
    $res=$this->make_new_friends($res);
    array_shift($res);

    $sql="SELECT
            mid, date as date_message
          FROM messages
          WHERE date>=$s_sql_period_0 and date<$s_sql_period_2 and uid=".tosql($uid);
    $res_message0=$this->db->get_array($sql);
    $res_message=array();
    foreach ($res_message0 as $v) {
      $key=date('Y-m-d', strtotime($v['date_message']));
      $res_message[$key]=$res_message[$key]+1;
    }

    foreach ($res as $v){
      $tmp['cnt_subscribers']=count($v['subscribers']);
      $tmp['cnt_new_subscribers']=count($v['new_subscribers']);
      $tmp['cnt_unsubscribers']=count($v['unsubscribers']);

      $tmp['cnt_friends']=count($v['friends']);
      $tmp['cnt_new_friends']=count($v['new_friends']);
      $tmp['cnt_unfriends']=count($v['unfriends']);

      $tmp['cnt_messages']=$res_message[$v['date']];
      $tmp['date']=date('d.m.Y',strtotime($v['date']));

      $out_res[]=$tmp;
    }

    return $out_res;
  }

  /**
   * type=details&uid=user_id&period=DD.MM.YYYY
   * возвращает подробную статистику на дату DD.MM.YYYY
      Подробная статистика:
        список подписчиков, список новых подписчиков,
        список подписок, список новых подписок,
        список сообщений
   */
  function get_details(){
    $uid=$_REQUEST['uid'];
    $period=$_REQUEST['period'];

    $cur_time=strtotime($period);
    $sql_period_0=date('Y-m-d',$cur_time);
    $s_sql_period_0=tosql($sql_period_0);

    //захватим предыдущий день, чтобы можно было узнать новых подписчиков
    $sql_period_1=date('Y-m-d',mktime(0,0,0,date('m',$cur_time),date('d',$cur_time)-1,date('Y',$cur_time)));
    $s_sql_period_1=tosql($sql_period_1);

    //захватим следующий день, чтобы можно было узнать сообщения
    $sql_period_2=date('Y-m-d',mktime(0,0,0,date('m',$cur_time),date('d',$cur_time)+1,date('Y',$cur_time)));
    $s_sql_period_2=tosql($sql_period_2);

    $sql="SELECT
            date, friends, subscribers
          FROM friends
          WHERE date>=$s_sql_period_1 and date<=$s_sql_period_0 and uid=".tosql($uid);
    $res=$this->db->get_array($sql);

    if (empty($res)) return array();
    $res=$this->make_new_friends($res);
    array_shift($res);

    $sql="SELECT
            mid, DATE_FORMAT(date, '%d.%m.%Y %T') as date_message, tags, body
          FROM messages
          WHERE date>=$s_sql_period_0 and date<$s_sql_period_2 and uid=".tosql($uid);
    $res_message=$this->db->get_array($sql);



    $res=$res[0];
    $res['messages']=$res_message;
    $res['date']=date('d.m.Y',strtotime($res['date']));

    return $res;
  }

  function answer(){
    $res='';
    switch ($_REQUEST['type']){
      case 'uid':
        $res=$this->get_uid();
      break;
      case 'periods':
        $res=$this->get_periods();
      break;
      case 'stats':
        $res=$this->get_stats();
      break;
      case 'details':
        $res=$this->get_details();
      break;
    }
    return $res;
  }

  function ajax_process(){
    $res=$this->answer();
    $str='';
    switch ($_REQUEST['f']){
      case 'xml':
        header ("Content-Type: text/xml");
        $o_xml=new c_xml();
        $o_xml->fromArray('dta',$res);
        $str=$o_xml->toXML();
      break;
      default:
        $str=json_encode($res);
      break;
    }
    $this->handler->h_result=$str;
  }

}
