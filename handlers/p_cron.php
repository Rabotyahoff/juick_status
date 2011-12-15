<?php

class p_cron extends a_handler_db_ra {

  /**
   *
   * @var p_cron
   */
  public $handler;

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
