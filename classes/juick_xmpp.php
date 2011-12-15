<?php
load_class_local('xmpp/XMPP');

class Juick_XMPP extends XMPPHP_XMPP{

  protected $is_setted_handler=false;
  public $last_input_xml;

  public function __construct($host, $port, $user, $password, $resource, $server = null, $printlog = false, $loglevel = null) {
		parent::__construct($host, $port, $user, $password, $resource, $server, $printlog, $loglevel);
	}

	/**
	 * установка обработчика
	 */
	function set_handler(){
	  if (!$this->is_setted_handler){
	    $this->addXPathHandler('{jabber:client}iq', 'iq_handler');
	  }
	}

	/**
	 *
	 * @param XMPPHP_XMLObj $subs_main
	 */
	private function iq_handler_users($subs_main, $pak_id){
	  $payload=array();

    $subs=$subs_main->subs;
	  $unames=array();
	  $uids=array();
	  if (is_array($subs)){
	  	foreach ($subs as $sub){
  	    $uids[]=$sub->attrs['uid'];
  	    $unames[]=$sub->attrs['uname'];
  	  }
	  }

	  $payload['pak_id'] = $pak_id;
		$payload['uids'] = $uids;
		$payload['unames'] = $unames;

		return $payload;
	}

	/**
	 *
	 * @param XMPPHP_XMLObj $subs_main
	 */
	private function iq_handler_messages($subs_main, $pak_id){
	  $payload=array();

    $subs=$subs_main->subs;

    $messages=array();
    foreach ($subs as $sub){
      $message=array();
      $message['pak_id'] = $pak_id;
      $message['mid']=$sub->attrs['mid'];
      $message['date']=$sub->attrs['ts'];
      $mes_subs=$sub->subs;
      foreach ($mes_subs as $mes_sub){
        switch ($mes_sub->name){
          case 'body':
            $message['body']=$mes_sub->data;
          break;
          case 'tag':
            $message['tags'][]=$mes_sub->data;
          break;
        }
      }
      if (!empty($message['body']) || !empty($message['tags'])) $messages[]=$message;
    }

		return $messages;
	}

	/**
	 * Iq handler
	 *
	 * @param XMPPHP_XMLObj $xml
	 */
	public function iq_handler($xml) {
	  //print_r($xml);
	  $this->last_input_xml=$xml;
	  $pak_id=$xml->attrs['id'];
	  $payload=array();
	  $subs_main=$xml->subs[0];
	  switch ($subs_main->ns){
	    case 'http://juick.com/query#users':
	      $payload=$this->iq_handler_users($subs_main,$pak_id);
	    break;
	    case 'http://juick.com/query#messages':
	      $payload=$this->iq_handler_messages($subs_main,$pak_id);
	    break;
	  }

		$this->event('iq', $payload);
	}
}
