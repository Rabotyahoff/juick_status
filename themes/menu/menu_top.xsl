<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" version="1.0" omit-xml-declaration="no" indent="yes" encoding="utf-8"/>
  <xsl:decimal-format grouping-separator=" " NaN=""/>

	<xsl:param name="res_site_url"></xsl:param>
	<xsl:param name="res_engine_url"></xsl:param>
	<xsl:param name="current_url"></xsl:param>
	<xsl:param name="cur_rights"></xsl:param>
	
	<xsl:template match="res">
		<xsl:variable name="cur_path">
			<xsl:for-each select="//full_path/item">/<xsl:value-of select="."/></xsl:for-each>
		</xsl:variable>		
		
		<div class="header-bottom">
			<xsl:if test="count(menu/item)>0">
				<div class="nav2">
					<xsl:for-each select="menu/item">
						<xsl:sort select="@pos" data-type="number" order="ascending"/>
						<span style="padding-right:10px">
							<xsl:choose>
								<xsl:when test="link!=$cur_path">
									<a href="{link}"><xsl:value-of select="title" disable-output-escaping="yes"/></a>		
								</xsl:when>
								<xsl:otherwise>
									<b><xsl:value-of select="title" disable-output-escaping="yes"/></b>
								</xsl:otherwise>
							</xsl:choose>
						</span>
					</xsl:for-each>
				</div>
			</xsl:if>
		</div>		
  </xsl:template>

</xsl:stylesheet>
