<?php
  function tosql($var){
    global $o_db_man;
    $db=$o_db_man->get_db('main');
    return $db->tosql($var);
  }

  function tosql_array($var){
    global $o_db_man;
    $db=$o_db_man->get_db('main');
    return $db->tosql_array($var);
  }

  function tosql_set_update($vars=array()){
    $ress=array();
    foreach ($vars as $k=>$v){
      $ress[]=$k.'='.tosql($v);
    }
    return implode(', ',$ress);
  }