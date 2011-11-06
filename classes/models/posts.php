<?php

load_class('c_data');
load_class('c_cache');

class m_posts{

  function get_count_time($date_str) {
    //замочим UT
    $date_str=trim(str_ireplace('ut','',$date_str));
    $date_utm=strtotime($date_str);

    $now=time();

    $time_zone_popr=date("O") / 100 * 60 * 60;//поправка тайм зоны
    $dif=$now-$date_utm-$time_zone_popr;

    $min0=floor($dif/60);
    $hour0=floor($min0/60);
    $day0=floor($hour0/24);
    $month0=floor($day0/30);
    $year0=floor($month0/12);

    $res='now';$ed='';$end='ago';
    //2 months ago 6 minutes ago
    if ($year0>=1){
      $res=$year0;
      $ed='year';
    }
    elseif ($month0>=1) {
      $res=$month0;
      $ed='month';
    }
    elseif ($day0>=1) {
      $res=$day0;
      $ed='day';
    }
    elseif ($hour0>=1) {
      $res=$hour0;
      $ed='hour';
    }
    elseif ($min0>=1) {
      $res=$min0;
      $ed='minute';
    }
    else {
      $res='now';
      $end='';
    }

    if (($res!='now')&&($res>1)){
      $ed.='s';
    }

    return trim($res.' '.$ed.' '.$end);
  }

  function xml_post_to_array($xml){
    if (empty($xml)) return array();

    $o_xml=new c_xml($xml);
    $res=$o_xml->toArray(array('item','category','comments'));
    $res=$res['channel'];

    $image_url=$res['image']['url']['.'];
    $res['image_small']['url']=mb_str_replace('http://i.juick.com/a/','http://i.juick.com/as/',$image_url);

    $link=$res['link']['.'];
    $login=mb_str_replace('http://juick.com/','',$link);
    $login=mb_str_replace('/','',$login);
    $res['login']=$login;

    foreach ($res as $k=>$item){
      if (!is_integer($k) || $item['*']!='item') continue;
      $res[$k]['pubDate_format']['count_time']['.']=$this->get_count_time($item['pubDate']['.']);
    }

    return $res;
  }

  function default_post(){
    $xml='
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:media="http://search.yahoo.com/mrss/" xmlns:wfw="http://wellformedweb.org/CommentAPI/">
  <channel>
    <link>http://juick.com/LP/</link>
    <image><url>/res/img/noava_small.png</url><title>LP - Juick</title><link>http://juick.com/LP/</link></image>
    <item>
      <link>http://juick.com/LP/1</link>
      <guid>http://juick.com/LP/1</guid>
      <title><![CDATA[@LP: *удав *шляпа]]></title>
      <description><![CDATA[Когда мне было шесть лет, в книге под названием "Правдивые истории", где рассказывалось про девственные леса, я увидел однажды удивительную картинку. На картинке огромная змея - удав - глотала хищного зверя.]]></description>
      <pubDate>Mon, 31 Oct 2011 08:54:30 UT</pubDate>
      <slash:comments>0</slash:comments>
      <comments>http://juick.com/1</comments>
      <wfw:commentRss>http://rss.juick.com/1</wfw:commentRss>
      <category>удав</category>
      <category>шляпа</category>
    </item>
  </channel>
</rss>';
    return $this->xml_post_to_array($xml);
  }

  function user_posts($login){
    $o_cache=new c_cache(60*5);
    $hash=$o_cache->make_hash(array('rss',$login));
    $xml=$o_cache->get($hash);
    if ($xml===false){
      $xml=@file_get_contents("http://rss.juick.com/$login/blog");
      $o_cache->set($hash, $xml);
    }

    return $this->xml_post_to_array($xml);
  }


}