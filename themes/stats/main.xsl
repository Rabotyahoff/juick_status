<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" omit-xml-declaration="yes" encoding="utf-8"/>

	<xsl:param name="res_site_url"></xsl:param>
	<xsl:param name="res_engine_url"></xsl:param>
	<xsl:param name="current_url"></xsl:param>
	<xsl:param name="cur_rights"></xsl:param>

	<xsl:template match="res">
		<script src="{$res_engine_url}js/ajax_url.js"></script>
		<script src="{$res_engine_url}js/jquery.transform.js"></script>
		<script src="{$res_site_url}js/stats.js"></script>
		<br/>
		<br/>
		<br/>
		<br/>
		<center>
			<table cellpadding="0" cellspacing="5" border="0">
				<tr>
					<td>
						<b>Juick login:</b>&#160;<input id="id_login" type="text" class="big_input" value="" onkeyup="key_up_on_login(this,event)"/>
					</td>
					<td width="110">
					  <button id="btn_get_data" style="white-space:nowrap;" onclick="show_login_stats();" class="big_button">Show stat</button>
					</td>
				</tr>
			</table>
			<br/>
			<br/>
			
			<div style="width:735px">
				<div  style="width:135px; float:left" id="id_periods"></div>
				<div  style="width:350px; float:left; text-align:left;" id="id_period_stat"></div>
				<div style="margin-left:35px;width:215px; float:left; text-align:left;" id="id_day_stats"></div>
			</div>
			
			<div style="clear:both"></div>
		</center>
		
		<br/>
		<br/>
		<br/>
		<br/>		
	</xsl:template>
</xsl:stylesheet>
