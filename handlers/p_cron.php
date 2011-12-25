<?php

class p_cron extends a_handler_db_ra {

  /**
   *
   * @var p_cron
   */
  public $handler;

  function remove_uid($uid){
  	$s_uid=tosql($uid);

  	$sql="DELETE FROM users WHERE uid=$s_uid";
  	$this->db->db_query($sql);
  	$sql="DELETE FROM friends WHERE uid=$s_uid";
  	$this->db->db_query($sql);
  	$sql="DELETE FROM messages WHERE uid=$s_uid";
  	$this->db->db_query($sql);
  }

  function answer_error($uid, $o_juick, $res_juick, $xml_query, $is_autodel=true){
  	if ($uid>0) {
  		echo '  error on pak_id. We need "'.$uid.'" but we have "'.$res_juick[0]['pak_id'].'"'."\n<BR/>";
  	}
  	echo '  result:'; print_r($o_juick->res);
  	echo "\n<BR/>";
  	echo '  last query:'; print_r($xml_query);
  	echo "\n<BR/>";
  	echo '  last ansver:'; print_r($o_juick->con->last_input_xml);
  	echo "\n<BR/>";
  	echo "\n<BR/>";

  	if ($o_juick->con->last_input_xml->subs[0]->attrs['code']==404){
  		//нет такого. Значит удалим его совсем
  		echo " !404! \n<BR/>";
  		if ($is_autodel){
	  		$this->remove_uid($uid);
	  		return true;//ошибка обработана
  		}
  	}

  	return false;
  }

  function ajax_process(){
    switch ($this->params[1]) {
      case 'read_friends':
        a_sub_handler_ra::load($this->handler, 'cron', 'read_friends');
      break;
      case 'read_uids':
        a_sub_handler_ra::load($this->handler, 'cron', 'read_uids');
      break;
      case 'read_messages':
        a_sub_handler_ra::load($this->handler, 'cron', 'read_messages');
      break;
      default:
        $this->h_result='request error';
      break;
    }
  }

}
