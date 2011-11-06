//var servlet='http://juick.ra-project.net/servlet.php?';
var servlet='/api/stats?';
var uid=null;

/*Begin читаем и анализируем #параметры*/
$().ready(function() {  
  var params=get_ajax_url_params();
  if (params[0]!=null){
    $('#id_login').val(params[0]);
    show_login_stats();
  }
});
/*End читаем и анализируем #параметры*/

function get_loading(){
  return '<B>Loading...</B>';
}

function show_login_stats(){
  jQuery('#btn_get_data').css('display','');
  jQuery('#id_periods').html('');
  jQuery('#id_period_stat').html('');
  jQuery('#id_day_stats').html('');
  
	var login=$.trim($('#id_login').val());
  set_ajax_url(login);
	if (login!=''){
    jQuery('#btn_get_data').css('display','none');
    jQuery('#id_period_stat').html(get_loading());
		$.get(
		  servlet+'f=json&type=uid&login='+login, 
			function(data) {
        if (data==null || data=='null'){
          jQuery('#id_period_stat').html('Логин "'+login+'" добавлен в базу для индексации нашими роботами.<BR/>Сейчас роботы охлаждаются, поэтому первые результаты будут уже через 2 часа.');
        }
        else {
          var res=eval('('+data+')');
          uid=res['uid'];
          if (uid==null){
            jQuery('#id_period_stat').html('Статистики для логина "'+login+'" нет.<BR/>Возможно наши ленивые роботы ещё не собрали нужную информацию.<BR>А возможно мы их зря гоняли и такого логина вообще не существует.');  
          }
          else show_periods();          
        }
		  }
	  );
	}	
}

function show_periods(){
  if (uid==null) return;
  jQuery('#id_periods').html(get_loading());
  jQuery('#id_period_stat').html('');
  jQuery('#id_day_stats').html('');
  
  jQuery('#id_periods').transform( 
    { cacheXsl: true,//кешировать ответы xsl
      cacheXml: true,//кешировать ответы xml
      xml:servlet+'f=xml&type=periods&uid='+uid,
      xsl:'/themes/api/stats/periods.xsl'
    });	
}

function show_some_period(obj, period){
	if (uid==null) return;
  jQuery('#id_period_stat').html(get_loading());
  jQuery('#id_day_stats').html('');  
	
	$('#id_periods span').removeClass('selected').addClass('lnk');
	$(obj).removeClass('lnk').addClass('selected');
	
  jQuery('#id_period_stat').transform( 
    { cacheXsl: true,//кешировать ответы xsl
      cacheXml: true,//кешировать ответы xml
      xml:servlet+'f=xml&type=stats&uid='+uid+'&period='+period,
      xsl:'/themes/api/stats/some_period.xsl'
    });		
}

function show_day_stats(obj, date_){
	if (uid == null) return;
  jQuery('#id_day_stats').html(get_loading());
  
  $('#id_period_stat div.line').removeClass('selected');
  $(obj).addClass('selected');  
	
	jQuery('#id_day_stats').transform({
		cacheXsl: true,//кешировать ответы xsl
		cacheXml: true,//кешировать ответы xml
		xml: servlet + 'f=xml&type=details&uid=' + uid + '&period=' + date_,
		xsl: '/themes/api/stats/day_stats.xsl'
	});
}

function key_up_on_login(obj,e){
  jQuery('#btn_get_data').css('display','');
  
  e = (!e) ? window.event : e;
  code = (e.charCode) ? e.charCode : ((e.keyCode) ? e.keyCode : ((e.which) ? e.which : 0));
  if (code==13){
    show_login_stats();
  }
}