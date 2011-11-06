<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" version="1.0" omit-xml-declaration="no" indent="yes" encoding="utf-8"/>
  <xsl:decimal-format grouping-separator=" " NaN=""/>
	<xsl:param name="res_site_url"></xsl:param>
	<xsl:param name="res_engine_url"></xsl:param>
	<xsl:param name="current_url"></xsl:param>

  <xsl:template match="page">
  	<body>
  		<center>  			
  			<br/>
  			<h1>Page not found</h1>
  			<a href="/">main page</a>
  			<br/>
  			<br/>
  			<img src="{$res_site_url}img/tk/404.png" alt="404" title="404" width="256" height="256"/>
  		</center>
  	</body>
  </xsl:template>

</xsl:stylesheet>
