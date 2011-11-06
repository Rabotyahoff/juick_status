<?php

class p_api extends a_handler_db_ra {

  /**
   *
   * @var p_api
   */
  public $handler;

  function ajax_process(){
    switch ($this->params[1]) {
      case 'stats':
        a_sub_handler_ra::load($this->handler, 'api', 'stats');
      break;
      default:
        $this->h_result='request error';
      break;
    }
  }

}
