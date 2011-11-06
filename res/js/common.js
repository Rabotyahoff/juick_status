var cur_page = {
  /*Begin properties*/
  rr: '',
  gg: '',
  bb: '',
  
  width0: '',
  height0: '',
  /*End properties*/
  
  get_login: function(is_real){
    var login=$.trim( $("#id_inp_login").val() );
    if (!is_real && login == '') {
      login = '_login_';
    }
    return login; 
  },  
  
  get_bg_color: function(prefix){
    var res_color=prefix+this.rr+this.gg+this.bb;
    if ($('#id_ch_transparent').attr('checked')) res_color = 'transparent';
    return res_color;
  },
  
  /*Begin methods*/ 
  key_up_on_login : function(obj,e) {    
    this.setColor(null,null,null,null,null,false);
    var e = (!e) ? window.event : e;
    var code = (e.charCode) ? e.charCode : ((e.keyCode) ? e.keyCode : ((e.which) ? e.which : 0));
    if (code==13){
      this.get_juick_data();
    }    
  },
  
  show_result: function(){
    var login=this.get_login(true);
    if (login != '') {
      var jobj_img = $("#id_img_result");
      jobj_img.attr('width', this.width0);
      jobj_img.attr('height', this.height0);
      //jobj_img.src='/res/img/load.gif';
      jobj_img.src='/res/img/empty.png';
  
      var res_src=this.get_bg_color('')+'_'+this.width0+'x'+this.height0 + '/' + login + '.png';
      jobj_img.attr('src',res_src);
      jobj_img.css('display','block');
  
      $("#id_div_result").css('display', 'none');
      $("#id_btn_result").css('visibility', 'hidden');
    }    
  },
  
  get_juick_data: function (){
    var login = this.get_login();
    if (login != '') {
      $("#id_wait_data").show();
      $("#id_btn_result").css('visibility', 'visible');
      
      var this_obj=this;
  
      $.post('?ajax=1','login='+encodeURI(login), function(data){
        $("#id_wait_data").hide();
        if (data!='error' && data!=''){
          $('#id_div_result').html(data);
          this_obj.setColor();
        }
      });
    }
  },
  
  setColor: function (r, g, b, width_, height_, is_change_color){
    if (r != null) { 
      rr0=r;
      this.rr = this.decToHexColor(r);
      this.uncheck();
    }
    if (g != null) {
      gg0=g;
      this.gg = this.decToHexColor(g);
      this.uncheck();
    }
    if (b != null) {
      bb0=b;
      this.bb = this.decToHexColor(b);
      this.uncheck();
    }
    if (width_ != null) {
      this.width0=width_;
      this.uncheck();
    }
    if (height_ != null) {
      this.height0=height_;
      this.uncheck();
    }
  
    $("#id_img_result").css('display', 'none');
    
    $("#id_div_result")
      .css('background-color', this.get_bg_color('#'))
      .css('display', 'block')
      .css('width', this.width0+'px')
      .css('height', this.height0+'px');
      
    var tmp_h1=this.height0-15;
    var tmp_h=Math.floor(tmp_h1/10)*10+1;
    var tmp_p=tmp_h1-tmp_h;
    $('#id_div_post').css('height', tmp_h+'px').css('margin-bottom', tmp_p+'px'); 
    
    var jobj = $("#id_user_text");
    var color_="#000000";
    if (rr0 + gg0 + bb0 <= 300) color_="#FFFFFF";
    jobj.css('color', color_).css('width', (this.width0-32-5-4)+'px').css('height', (this.height0-7-12)+'px');    
  
    var login=this.get_login(true);  
    $("#id_lnk_stats").attr('href', '/stats#'+login).css('display', '');
    
    if (login == '') {
      $("#id_btn_result").css('visibility', 'hidden');
    }
    else {
      if (is_change_color) $("#id_btn_result").css('visibility', 'visible');
    }
    var login=this.get_login(false);
  
    res_color=this.get_bg_color('');
    $("#id_inp_bb").val('[url=http://juick.com/'+login+'/][img]http://juick.ra-project.net/'+res_color+'_'+this.width0+'x'+this.height0+'/'+login+'.png[/img][/url]');
    $("#id_inp_html").val('<a href="http://juick.com/'+login+'/"><img src="http://juick.ra-project.net/'+res_color+'_'+this.width0+'x'+this.height0+'/'+login+'.png"></a>');
  },

  uncheck: function (){
    $("#id_ch_transparent").removeAttr('checked');
  },
  
  decToHexColor: function (dec){
    var hex = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'];
    dec = parseInt(dec);
    return hex[parseInt(dec / 16)] + hex[dec % 16];
  },
  
  set_color_tracbars: function (r,g,b){
    trackbar.getObject('color_R').setLeftWidth(r);
    trackbar.getObject('color_G').setLeftWidth(g);
    trackbar.getObject('color_B').setLeftWidth(b);
  },
  
  set_size_tracbars: function (x,y){
    trackbar.getObject('size_X').setLeftWidth(x);
    trackbar.getObject('size_Y').setLeftWidth(y);
  }, 
  /*End methods*/
};




