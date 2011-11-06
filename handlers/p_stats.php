<?php

class p_stats extends a_handler_ra {

  /**
   *
   * @var p_stats
   */
  public $handler;

  function show(){
    $this->xsl='stats/main.xsl';
  }

  function ajax_process(){
  }

  function process(){
    $this->show();
  }

}
