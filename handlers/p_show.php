<?php

load_class_local('models/posts');
load_class('c_graph');

class p_show extends a_handler_ra {

  /**
   *
   * @var p_show
   */
  public $handler;

  public $def_image_width=400;
  public $def_image_height=60;

  public $min_image_width=90;
  public $min_image_height=50;
  public $max_image_width=800;
  public $max_image_height=500;

  public $max_ava_width=32;
  public $max_ava_height=32;
  public $max_attach_width=45;
  public $max_attach_height=45;

  public $exts=array('jpg','jpeg','gif','png');

  function get_str_len_on_picture($str, $size=10){
    $tmp=c_graph::get_text_size($size, 'arial.ttf', c_graph::utf8_to_uni($str));
    return $tmp['width'];
  }


  function get_small_text($str, $limit_px, $add_toend='', &$len_str) {
    $dif_px=0;//px
    $len_px=$this->get_str_len_on_picture($str);

    if ($len_px<=($limit_px+$dif_px)) {
      $len_str=mb_strlen($str);
      return $str;
    }

    $len_px=min($len_px, $limit_px+$dif_px);
    $delims=array(' ', '.', ',', '?', '!', /*';',*/ '=', '+', '-', /*'#',*/ '\\', '/', ':');
    $res='';
    $last_full='';
    $prev_full='';
    $cur_len_px=0;
    $cur_len=0;
    $last_ch_is_delim=FALSE;
    while ( $cur_len_px<$len_px ) {
      $ch=mb_substr($str,$cur_len,1);
      $res.=$ch;
      $cur_len++;
      $cur_len_px=$this->get_str_len_on_picture($res);
      if ((in_array($ch, $delims))&&(!$last_ch_is_delim)) {
        $last_ch_is_delim=TRUE;
        if ($cur_len_px<$limit_px) $prev_full=$res;
        $last_full=$res;
      }
      else {
        $last_ch_is_delim=FALSE;
      }
    }

    $len_prev_ful_px=$this->get_str_len_on_picture($prev_full);
    $len_last_ful_px=$this->get_str_len_on_picture($last_full);
    $dif1_px=abs($limit_px-$len_prev_ful_px);
    $dif2_px=abs($limit_px-$len_last_ful_px);
    if ($len_prev_ful_px>$limit_px/2 && $dif1_px<=$dif2_px) $res=$prev_full.$add_toend;
    elseif ($len_last_ful_px>$limit_px/2) $res=$last_full.$add_toend;

    $len_str=mb_strlen($res);
    return $res;
  }

  function make_pic($login, $bg_color, $image_width, $image_height, $is_transparent, $is_whith_link){
    $m_posts=new m_posts();
    $rss=$m_posts->user_posts($login);

    $r=hexdec(substr($bg_color,0,2));
    $g=hexdec(substr($bg_color,2,2));
    $b=hexdec(substr($bg_color,4,2));

    if (empty($rss)) {
      $im=imagecreatetruecolor($image_width, $image_height);
      imagealphablending($im,true);
      $background_color=imagecolorallocate($im, $r, $g, $b);
      imagefill($im,0,0,$background_color);

      return $im;
    }

    $post=$rss[0];
    $media_thumbnail=$post['media_thumbnail']['@url'];
    $ava_src=$rss['image_small']['url'];

    $txt=$post['description']['.'];
    $txt=str_replace("\n", ' ', $txt);
    $txt=str_replace('  ', ' ', $txt);
    $txt=str_replace('&mdash;', '-', $txt);
    $txt=str_replace('&ndash;', '-', $txt);
    $txt=str_replace('&nbsp;', ' ', $txt);
    $txt=str_replace('&#160;', ' ', $txt);
    $txt=strip_tags(trim($txt));
    $txt=html_entity_decode($txt, ENT_QUOTES);

    $login_real=$rss['link']['.'];
    $login_real=mb_str_replace('http://juick.com/','',$login_real);
    $login_real=mb_str_replace('/','',$login_real);
    $date=$post['pubDate_format']['count_time']['.'];

    $tag='';
    foreach ($post as $k=>$v){
      if (!is_array($v)) continue;
      if ($v['*']=='category'){
        if (!empty($tag)) $tag.=' ';
        $tag.='*'.$v['.'];
      }
    }

    $is_show_attach=false;
    if (!empty($media_thumbnail)){
      $im_attach=c_graph::get_im($media_thumbnail);
      if (!empty($im_attach)){
        $im_attach=c_graph::resize_im($im_attach, $this->max_attach_width, $this->max_attach_height);
        $is_show_attach=true;
      }
    }
    else {
      $im_ava=c_graph::get_im($ava_src);
    }

    if ($r+$g+$b <= 300) $txt_c=255;
    else $txt_c=0;

    $border_lr=2;
    $border_tb=2;
    if ($is_transparent) {
      $im=imagecreatetruecolor($image_width, $image_height);
      //imagealphablending($im, true); // setting alpha blending on
      //imagesavealpha($im, true); // save alphablending setting (important)
      $white = ImageColorAllocate($im, 255, 255, 255);
      imagefill($im,0,0,$white);
      ImageColorTransparent($im, $white);
    }
    else {
      $im=imagecreatetruecolor($image_width, $image_height);
      imagealphablending($im,true);
      $background_color=imagecolorallocate($im, $r, $g, $b);
      imagefill($im,0,0,$background_color);
    }
    //imagesettile


    $txt_color=imagecolorallocate($im, $txt_c, $txt_c, $txt_c);

    $zero_h=7;
    $h_step=12;
    $zero_w=5;
    if ($is_show_attach) $zero_w+=13;

    /*$text_len=46;$res_len=0;$from=0;
    if ($is_show_attach) $text_len=42;*/
    $res_len=0;
    $from=0;

    $text_left=$this->max_ava_width+$zero_w+$border_lr;
    $text_len_px=$image_width-$text_left-2*$border_lr;

    $max_text_top=$image_height-3*$border_tb-8;
    $key=0;
    $cur_text_top=$zero_h+$h_step*$key+$border_tb*2;

    while ($cur_text_top<=$max_text_top){
      //$text_len-1, '...'
      $next_text_top=$zero_h+$h_step*($key+1)+$border_tb*2;
      if ($next_text_top>$max_text_top){
        //это последняя строка
        $show_text=trim($this->get_small_text(mb_substr($txt, $from), $text_len_px-$this->get_str_len_on_picture('...'), '...',$res_len));
      }
      else {
        $show_text=trim($this->get_small_text(mb_substr($txt, $from), $text_len_px, '',$res_len));
      }

      $from=$from+$res_len;

      ImageTTFText($im, 10, 0, $text_left, $zero_h+$h_step*$key+$border_tb*2, $txt_color, 'arial.ttf', c_graph::utf8_to_uni($show_text));
      $key++;
      if ($key==3 && !$is_show_attach) {
        $text_left=$text_left - 32;
        $text_len_px+=32;
      }
      if ($key==4 && $is_show_attach) {
        $text_left=$text_left - $this->max_attach_width;
        $text_len_px += $this->max_attach_width;
      }
      $cur_text_top=$next_text_top;
    }
    //echo $max_text_top.'..'.$cur_text_top;die;

    //$date_color=imagecolorallocate($im, 0, 0, 150);
    $date_color=imagecolorallocate($im, 180, 115, 41);
    $text=$login_real.' - '.$date.'   '.$tag;
    $bottom_text_length=$this->get_str_len_on_picture($text, 8);
    $bottom_text_right=$bottom_text_length+2*$border_lr;
    ImageTTFText($im, 8, 0, $border_lr, $image_height-$border_tb, $date_color, 'arial.ttf', c_graph::utf8_to_uni($text));

    if ($is_whith_link){
    	global $o_global;
	    $gray=imagecolorallocate($im, 200, 200, 200);
	    $my_txt=$o_global->site_root_url;//"http://juick.ra-project.net";
	    $tmp_width=$this->get_str_len_on_picture($my_txt, 7);
	    $tmp_left=$image_width-$tmp_width-$border_lr;
	    //echo $tmp_left;die;
	    //выводить не будем, если не помещается
	    if ($tmp_left>=$bottom_text_right){
	      ImageTTFText($im, 7, 0, $tmp_left, $image_height-$border_tb-1, $gray, 'arial.ttf', c_graph::utf8_to_uni($my_txt));
	    }
    }

    if (!$is_show_attach){
      /*imagealphablending($im_ava, true); // setting alpha blending on
      imagesavealpha($im_ava, true); // save alphablending setting (important)
      imagecopymerge_alpha($im, $im_ava, $border_lr, $border_tb, 0, 0, $this->max_ava_width, $this->max_ava_height,100);*/

      @imagecopy($im, $im_ava, $border_lr, $border_tb, 0, 0, $this->max_ava_width, $this->max_ava_height);
      @imagedestroy($im_ava);
    }
    else {
      @imagecopy($im, $im_attach, $border_lr, $border_tb, 0, 0, $this->max_attach_width, $this->max_attach_height);
      @imagedestroy($im_attach);
    }

    return $im;
  }

  function show(){
    $login= mb_strtolower(trim($_REQUEST['login']));
    $ext=mb_strtolower(trim($_REQUEST['ext']));
    $bg=mb_strtolower(trim($_REQUEST['bg']));
    if (empty($login) || !in_array($ext, $this->exts)) {
      $this->handler->h_result='';
      return;
    }

    $tmp=explode('_',$bg);
    $bg_color=$tmp[0];
    $size=$tmp[1];
    $is_transparent=FALSE;
    if ($bg_color=='transparent'){
      $bg_color='FFFFFF';//for normal rgb and color text
      $is_transparent=true;
    }
    if ($bg_color=='') $bg_color='EEEEDF';

    $tmp=explode('x',$size);
    $image_width=$tmp[0];
    $image_height=$tmp[1];
    if (empty($image_width)) $image_width=$this->def_image_width;
    if (empty($image_height)) $image_height=$this->def_image_height;
    $is_whith_link=!isset($tmp[2]);

    if ($image_width<$this->min_image_width) $image_width=$this->min_image_width;
    if ($image_height<$this->min_image_height) $image_height=$this->min_image_height;

    if ($image_width>$this->max_image_width) $image_width=$this->max_image_width;
    if ($image_height>$this->max_image_height) $image_height=$this->max_image_height;

    $o_cache=new c_filecache(60*5);
    $hash=$o_cache->make_hash(array('pic',$login,$bg));
    $res=$o_cache->get($hash);
    if ($res===false){
      $im=$this->make_pic($login, $bg_color, $image_width, $image_height, $is_transparent, $is_whith_link);
      $o_cache->set_img($hash, $im, $ext);
      @imagedestroy($im);
      $res=$o_cache->get($hash);
    }

    if ($ext=='jpg') $ext='jpeg';
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', FALSE);
    header('Pragma: no-cache');
    header("Content-type: image/".$ext);
    echo $res;
    exit;
  }

  function ajax_process(){
    $this->show();
  }

}
