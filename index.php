<?php
  define('_GL_DEBUG',true);
  include_once './_load_engine.php';

  load_lib_local('common');
  load_class_local('c_handler_ra');
//print_r($_REQUEST);die;
  run_site();
