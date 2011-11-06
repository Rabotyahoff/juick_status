<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" version="1.0" omit-xml-declaration="no" indent="yes" encoding="utf-8"/>
  <xsl:decimal-format grouping-separator=" " NaN=""/>

	<xsl:param name="res_site_url"></xsl:param>
	<xsl:param name="res_engine_url"></xsl:param>
	<xsl:param name="current_url"></xsl:param>
	<xsl:param name="cur_rights"></xsl:param>

	<xsl:template match="ajax_post">
		<xsl:call-template name="show_post"></xsl:call-template>
	</xsl:template>

	<xsl:template name="show_post">
		<xsl:for-each select="post">
			<div style="margin: 2px">
				<div id="id_wait_data" 
					style="display:none; overflow: hidden;
					       background-image: url('{$res_site_url}img/load.gif');
					       background-position:center; 
					       background-repeat: no-repeat; 
					       width: 220px;height: 19px; 
					       z-index:100; position:absolute"></div>
				
				<xsl:variable name="image_small_url" select="image_small/url"/>
				<xsl:variable name="login" select="login"/>
				
				<xsl:for-each select="item[1]">					
					<div id="id_div_post" style="height:100%; line-height: 10px; overflow: hidden;  font-size: 12px;">
						
							<xsl:variable name="media_thumbnail_url" select="media_thumbnail/@url"/>
							<xsl:choose>
								<xsl:when test="$media_thumbnail_url!=''">
									<img border="0" src="{$media_thumbnail_url}" width="45" height="45" style="float:left; margin-right: 5px; margin-bottom:5px"/>
								</xsl:when>
								<xsl:when test="position()=1">
									<img border="0" src="{$image_small_url}" width="32" height="32" style="float:left; margin-right: 5px; margin-bottom:5px"/>
								</xsl:when>
							</xsl:choose>
						<xsl:value-of select="description" disable-output-escaping="yes"/>
					</div>
					
	  			<div style="overflow: hidden; color:#B47329; font-size: 9px; height: 11px;">
						<xsl:if test="position()=1">
							<xsl:value-of select="$login"/>
							<xsl:text> - </xsl:text>
						</xsl:if>
						<xsl:value-of select="pubDate_format/count_time"/>
						&#160;&#160;&#160;
						<xsl:for-each select="category">
							*<xsl:value-of select="."/><xsl:text> </xsl:text>
						</xsl:for-each>       
					</div>			
				</xsl:for-each>
				
			</div>
		</xsl:for-each>
	</xsl:template>

	<xsl:template match="res">
		<link rel="stylesheet" type="text/css" href="{$res_site_url}css/trackbar.css"/>
		
		<script src="{$res_site_url}js/common.js"/>
		<script src="{$res_site_url}js/trackbar.js"/>
		
		<div style="width: 800px">
			<div style="text-align: center;">
				<b>Juick login:</b>
				&#160;
				<input id="id_inp_login" type="text" class="big_input"  onkeyup="cur_page.key_up_on_login(this,event)"/>
				&#160;
				<button id="btn_get_data" onclick="cur_page.get_juick_data();" class="big_button">Show me</button>
				&#160;
				<a id="id_lnk_stats" href="/stats">Statistic</a>		
			</div>
			
			<div style="margin-top:30px">
				<div style="float:left">
					
					<span>
						<b style="display: inline-block; margin-top: 4px;">Background</b>
						<span style="float:right">
							<button onclick="cur_page.uncheck();cur_page.set_color_tracbars(300,300,300);" style="cursor:pointer;font:12px tahoma; border:1px solid #000; background-color:#FFFFFF; width:65px; height:20px;">white</button>
							<xsl:text> </xsl:text>
							<button onclick="cur_page.uncheck();cur_page.set_color_tracbars(275,275,258);" style="cursor:pointer;font:12px tahoma; border:1px solid #000; background-color:#EEEEDF; width:65px; height:20px;">juick</button>
							<xsl:text> </xsl:text>
							<button onclick="cur_page.uncheck();cur_page.set_color_tracbars(0,0,0);" style="cursor:pointer;font:12px tahoma; color: white; border:1px solid #000; background-color:#000000; width:65px; height:20px;">black</button>
						</span>						
					</span>
					
					<script type="text/javascript">
						rr0 = 238;
						gg0 = 238;
						bb0 = 223;
						
						width0=400;
						height0=60;
						
						rr = '';
						gg = '';
						bb = '';
						
						trackbar.getObject('color_R').init({
						onMove: function(){
						cur_page.setColor(this.leftValue, null, null,null,null,true);
						},
						dual: false, // two intervals
						width: 300, // px
						leftLimit: 0, // unit of value
						leftValue: rr0, // unit of value
						rightLimit: 255, // unit of value
						rightValue: 0, // unit of value
						clearLimits: true,
						clearValues : true
						});
						trackbar.getObject('color_G').init({
						onMove: function(){
						cur_page.setColor(null, this.leftValue,null,null, null,true);
						},
						dual: false, // two intervals
						width: 300, // px
						leftLimit: 0, // unit of value
						leftValue: gg0, // unit of value
						rightLimit: 255, // unit of value
						rightValue: 0, // unit of value
						clearLimits: true,
						clearValues : true
						});
						trackbar.getObject('color_B').init({
						onMove: function(){
						cur_page.setColor(null, null, this.leftValue,null,null,true);
						},
						dual: false, // two intervals
						width: 300, // px
						leftLimit: 0, // unit of value
						leftValue: bb0, // unit of value
						rightLimit: 255, // unit of value
						rightValue: 0, // unit of value
						clearLimits: true,
						clearValues : true
						});
						
						cur_page.set_color_tracbars(275,275,258);
						obj = document.getElementById("id_inp_login");
						if (obj!=null) obj.value='';
					</script>
					<!--<div style="width: 300px; text-align: left;"><input type="checkbox" id="id_ch_transparent" onchange="cur_page.setColor(null, null, null,null,null,true);">&#160;Transparent</div>-->					
				</div>
				
				<div style="float:right">
					<span>
						<b style="display: inline-block; margin-top: 4px;">Size</b>
						<button class="big_button" onclick="cur_page.set_size_tracbars(132, 13);" style="float:right; cursor:pointer;font:12px tahoma; border:1px solid #000;width:65px; height:20px;">default</button>
					</span>					
					<script type="text/javascript">
						trackbar.getObject('size_X').init({
						onMove: function(){
						cur_page.setColor(null, null,null, this.leftValue,null,true);
						},
						dual: false, // two intervals
						width: 300, // px
						leftLimit: 90, // unit of value
						leftValue: width0, // unit of value
						rightLimit: 800, // unit of value
						rightValue: 0, // unit of value
						clearLimits: true,
						clearValues : false,
						roundUp: 5
						});        
					</script>				
					
					<script type="text/javascript">
						trackbar.getObject('size_Y').init({
						onMove: function(){
						cur_page.setColor(null, null,null,null, this.leftValue,true);
						},
						dual: false, // two intervals
						width: 300, // px
						leftLimit: 50, // unit of value
						leftValue: height0, // unit of value
						rightLimit: 500, // unit of value
						rightValue: 0, // unit of value
						clearLimits: true,
						clearValues : false,
						roundUp: 5
						});        
					</script>
				</div>
				<div style="clear:both"></div>
			</div>
			
			<div style="margin-top:30px">
				<table cellpadding="0" cellspacing="2" align="center">
					<tr>
						<td align="right"><b>BB-code:</b></td>
						<td><input id="id_inp_bb" type="text" size="100" value="[url=http://juick.com/_login_/][img]http://juick.ra-project.net/EEEEDF_400x60/_login_.png[/img][/url]"/></td>
					</tr>
					<tr>
						<td align="right"><b>HTML-code:</b></td>
						<td><input id="id_inp_html" type="text" size="100" value='&lt;a href="http://juick.com/_login_/"&gt;&lt;img src="http://juick.ra-project.net/EEEEDF_400x60/_login_.png"&gt;&lt;/a&gt;'/></td>
					</tr>
				</table>				
			</div>
			
			<div style="margin-top:30px">
				<center>
					<div id="id_div_result" style="font-family:Arial; overflow: hidden; border:0px solid #000; background-color:#EEEEDF; width:400px; height:60px; text-align:left;">
						<xsl:call-template name="show_post"></xsl:call-template>
					</div>
					
					<img id="id_img_result" border="0" style="display: none;" src="" width="400" height="60"/>
					<br/>
					<button onclick="cur_page.show_result();" style="visibility: hidden;" id="id_btn_result" class="big_button">Show result</button>
				</center>
			</div>
		</div>
		
		<script>
			cur_page.uncheck();
			cur_page.setColor();
		</script>		
  </xsl:template>

</xsl:stylesheet>
