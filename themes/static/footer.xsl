<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" version="1.0" omit-xml-declaration="no" indent="yes" encoding="utf-8"/>
  <xsl:decimal-format grouping-separator=" " NaN=""/>

	<xsl:param name="res_site_url"></xsl:param>
	<xsl:param name="res_engine_url"></xsl:param>
	<xsl:param name="current_url"></xsl:param>
	<xsl:param name="cur_rights"></xsl:param>
	
	<xsl:template match="res">
		<div style="margin-top:30px; margin-bottom:30px">
			<!--<a href="http://juick.com"><b>Juick</b></a> - сервис микроблогов
			<br/>-->
			Предложения и пожелания по работе сервиса направлять сюда <a href="http://juick.com/RA"><b>PM @RA</b></a>
			<br/> 
			Проект на <a href="https://github.com/Rabotyahoff/juick_status">github</a>
		</div>
		
		<center>
			<small><a href="mailto:rabotyahoff@gmail.com">Rabotyahoff Alexandr</a>&#160;&#160;© 2009-2011</small>
		</center>
		<br/>
  </xsl:template>

</xsl:stylesheet>
