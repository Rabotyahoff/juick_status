<?php

load_class('c_handler');

class a_handler_ra extends a_handler {

  /**
   *
   * @var a_handler_ra
   */
  public $handler;

}

class a_handler_db_ra extends a_handler {

  /**
   *
   * @var a_handler_db_ra
   */
  public $handler;

  function __construct(){
    parent::__construct('main');
  }

}

abstract class a_sub_handler_ra extends a_sub_handler{

  /**
   *
   * @var a_handler_ra
   */
  public $handler;

}

