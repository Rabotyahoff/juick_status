<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" omit-xml-declaration="yes" encoding="utf-8"/>
	
	<xsl:template match="dta">
		<div style="padding-left:62px;">
			<label class="sum">всего</label>
			&#160;
			<label class="new">новых</label>
			&#160;
			<label class="un">отписок</label>
		</div>
		<div style="clear:both"></div>
		
		<div class="header">
			<div class="cell" style="width:35px">
				число
			</div>
			<div class="cell" style="width:85px;">
				подписчиков				
			</div>
			<div class="cell" style="width:85px;">
				подписок
			</div>
			<div class="cell" style="width:85px;">
				сообщений				
			</div>			
		</div>
		<div style="clear:both"></div>
		
		<div style="height:700px; overflow:auto; width:365px;">
			<xsl:for-each select="item">
				<div class="line" onclick="show_day_stats(this,'{date}')">
					<div class="cell" style="width:35px">
						<xsl:value-of select="substring(date,1,2)"/>
					</div>
					<div class="cell" style="width:85px;">
						<label class="sum"><xsl:call-template name="show_val"><xsl:with-param name="val"><xsl:value-of select="cnt_subscribers"/></xsl:with-param></xsl:call-template></label>
						<label class="new"><xsl:call-template name="show_val"><xsl:with-param name="val"><xsl:value-of select="cnt_new_subscribers"/></xsl:with-param></xsl:call-template></label>
	  				<label class="un"><xsl:call-template name="show_val"><xsl:with-param name="val"><xsl:value-of select="cnt_unsubscribers"/></xsl:with-param></xsl:call-template></label>
					</div>
					<div class="cell" style="width:85px;">
						<label class="sum"><xsl:call-template name="show_val"><xsl:with-param name="val"><xsl:value-of select="cnt_friends"/></xsl:with-param></xsl:call-template></label>
	  				<label class="new"><xsl:call-template name="show_val"><xsl:with-param name="val"><xsl:value-of select="cnt_new_friends"/></xsl:with-param></xsl:call-template></label>
						<label class="un"><xsl:call-template name="show_val"><xsl:with-param name="val"><xsl:value-of select="cnt_unfriends"/></xsl:with-param></xsl:call-template></label>				
					</div>
					<div class="cell" style="width:85px;">
						<xsl:call-template name="show_val"><xsl:with-param name="val"><xsl:value-of select="cnt_messages"/></xsl:with-param></xsl:call-template>				
					</div>
					<div style="clear:both"></div>
				</div>
				<div style="clear:both"></div>
			</xsl:for-each>
		</div>
			
	</xsl:template>
	
	<xsl:template name="show_val">
		<xsl:param name="val"></xsl:param>
		<xsl:choose>
			<xsl:when test="$val=''">0</xsl:when>
			<xsl:otherwise><xsl:value-of select="$val"/></xsl:otherwise>
		</xsl:choose>
	</xsl:template>
</xsl:stylesheet>
