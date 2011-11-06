<?php

load_class_local('models/posts');

class p_status extends a_handler_ra {

  /**
   *
   * @var p_status
   */
  public $handler;

  function show(){
    $m_posts=new m_posts();
    $this->handler->h_data['post']=$m_posts->default_post();
    $this->xsl='status/main.xsl';
  }

  function ajax_process(){
    $login=trim($_POST['login']);
    if (empty($login)) {
      $this->handler->h_result='error';
      return;
    }

    //$_REQUEST['debug_cache']=1;
    //$this->is_debug=true;

    $this->xsl='status/main.xsl';
    $this->root_node='ajax_post';

    $m_posts=new m_posts();
    $this->handler->h_data['post']=$m_posts->user_posts($login);
  }

  function process(){
    $this->show();
  }

}
