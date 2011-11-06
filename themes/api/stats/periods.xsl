<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" omit-xml-declaration="yes" encoding="utf-8"/>
	
	<xsl:template match="dta">
		<xsl:for-each select="item">
			<span class="lnk" onclick="show_some_period(this, '{period}')"><xsl:value-of select="str"/></span>
			<xsl:if test="position()!=last()"><br/><br/></xsl:if>
		</xsl:for-each>	
	</xsl:template>
</xsl:stylesheet>
