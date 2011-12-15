<?php
load_class_local('juick_xmpp');

class c_juick {

  /**
   *
   * @var Juick_XMPP
   */
  public $con=null;
  public $res=array();

  function __destruct(){
    $this->disconnect();
  }

  function connect(){
    if ($this->con==null){
    	global $o_global;
    	$juick_account=$o_global->settings_array['juick_account'];

      $err_level=XMPPHP_Log::LEVEL_WARNING;
      if ($_REQUEST['debug_xmpp']==1) $err_level=XMPPHP_Log::LEVEL_VERBOSE;

      $this->con = new Juick_XMPP($juick_account['host']['.'], $juick_account['port']['.'],
      		 $juick_account['login']['.'], $juick_account['password']['.'], $juick_account['resource']['.'],
      		 $juick_account['server']['.'], true, $err_level);
      if ($juick_account['ssl']['.']==1) $this->con->useSSL(true);

      try {
        $this->con->connect();
        $this->con->processUntil('session_start');
        $this->con->presence();
      } catch(XMPPHP_Exception $e) {
        die($e->getMessage());
      }
    }
  }

  function disconnect(){
    if ($this->con!=null){
      try {
        $this->con->disconnect();
      } catch(XMPPHP_Exception $e) {
        die($e->getMessage());
      }//try
      $this->con=null;
    }
  }

  function send($xml,$is_empty_res=true){
    if ($is_empty_res) $this->res=array();

    $this->connect();
    $this->con->set_handler();
    try {
      $this->con->send($xml);
      $payloads = $this->con->processUntil(array('iq'));
      $this->res[]=$payloads[0][1];
    } catch(XMPPHP_Exception $e) {
      die($e->getMessage());
    }//try

    return $this->res;
  }

  function send_xmls($xmls=array(),$is_empty_res=true){
    if ($is_empty_res) $this->res=array();

    if (!is_array($xmls)) $xmls=array($xmls);
    foreach ($xmls as $xml){
      $this->send($xml, false);
    }

    return $this->res;
  }

  /**
   * UID по UName
   * Не более 20 пользователей в одном запросе, все запросы внутри IQ должны быть одного типа:
   * @param $unames
   */
  function make_xml_uid_by_uname($unames=array()){
    if (!is_array($unames)) $unames=array($unames);
    $out_xml="<iq to='juick@juick.com' id='id234' type='get'><query xmlns='http://juick.com/query#users'>";
    foreach ($unames as $uname){
      $out_xml.="<user xmlns='http://juick.com/user' uname='$uname'/>";
    }
    $out_xml.="</query></iq>";
    return $out_xml;
  }

  /**
   * Получить список друзей, на которых подписан пользователь с UID=$uid
   * @param $uid
   */
  function make_xml_friends($uid){
    $out_xml="<iq to='juick@juick.com' id='$uid' type='get'><query xmlns='http://juick.com/query#users' friends='$uid'/></iq>";
    return $out_xml;
  }

  /**
   * Получить список людей, которые подписаны на пользователя с UID=$uid
   * @param $uid
   */
  function make_xml_subscribers($uid){
    $out_xml="<iq to='juick@juick.com' id='$uid' type='get'><query xmlns='http://juick.com/query#users' subscribers='$uid'/></iq>";
    return $out_xml;
  }

  /**
   * Получить последние сообщения пользователя с UID=$uid, где $mid - последнее сообщение пользователя
   * @param $uid
   * @param $mid
   */
  function make_xml_messages($uid,$mid=false){
    $add_after_mid='';
    if (!empty($mid) && $mid>0){
      $add_after_mid="aftermid='$mid'";
    }
    $out_xml="<iq to='juick@juick.com' id='$uid' type='get'><query xmlns='http://juick.com/query#messages' uid='$uid' $add_after_mid/></iq>";
    return $out_xml;
  }
}

