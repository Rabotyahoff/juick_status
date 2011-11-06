<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" omit-xml-declaration="yes" encoding="utf-8"/>
	
	<xsl:template match="dta">
		<h3><xsl:value-of select="date"/></h3>

    <xsl:choose>
      <xsl:when test="count(new_subscribers/item)+count(subscribers/item)+count(subscribers/item)>0">
        <h5 style="margin:2px">Подписчики:</h5>
        <div style="padding-bottom:10px">
          <xsl:for-each select="new_subscribers/item">
            <xsl:sort order="ascending" select="."/>
            <xsl:call-template name="show_user">
              <xsl:with-param name="uname"><xsl:value-of select="."/></xsl:with-param>
              <xsl:with-param name="class">new</xsl:with-param>
            	<xsl:with-param name="type">s</xsl:with-param>
            </xsl:call-template>
            <xsl:if test="position()!=last() or count(//subscribers/item)>0"><xsl:text>, </xsl:text></xsl:if>
          </xsl:for-each>
        	
        	<xsl:for-each select="subscribers/item[10>=position()]">
            <xsl:sort order="ascending" select="."/>
            <xsl:call-template name="show_user">
              <xsl:with-param name="uname"><xsl:value-of select="."/></xsl:with-param>
              <xsl:with-param name="class">sum</xsl:with-param>
            	<xsl:with-param name="type">s</xsl:with-param>
            </xsl:call-template>          
            <xsl:if test="position()!=last() or count(//unsubscribers/item)>0"><xsl:text>, </xsl:text></xsl:if>
          </xsl:for-each>
        	<xsl:if test="count(subscribers/item)>10">
        		<br/>
        		<a style="padding-right:5px" href="javascript:void(0)" onclick="if ($('#id_all_subs').css('display')=='none') $('#id_all_subs').css('display','');else $('#id_all_subs').css('display','none');">...ещё...</a>
        		<br/>
        		<span id="id_all_subs" style="display:none">
        			<xsl:for-each select="subscribers/item[position()>10]">
        				<xsl:sort order="ascending" select="."/>
        				<xsl:call-template name="show_user">
        					<xsl:with-param name="uname"><xsl:value-of select="."/></xsl:with-param>
        					<xsl:with-param name="class">sum</xsl:with-param>
        					<xsl:with-param name="type">s</xsl:with-param>
        				</xsl:call-template>          
        				<xsl:if test="position()!=last() or count(//unsubscribers/item)>0"><xsl:text>, </xsl:text></xsl:if>
        			</xsl:for-each>        			
        		</span>	      				
        	</xsl:if>        	
        	
          <xsl:for-each select="unsubscribers/item">
            <xsl:sort order="ascending" select="."/>
            <xsl:call-template name="show_user">
              <xsl:with-param name="uname"><xsl:value-of select="."/></xsl:with-param>
              <xsl:with-param name="class">un</xsl:with-param>
            </xsl:call-template>          
            <xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
          </xsl:for-each>     
        </div>        
      </xsl:when>
      <xsl:otherwise>
        <h5 style="margin:2px">Подписчиков нет</h5>
        <div style="padding-bottom:10px"></div>
      </xsl:otherwise>
    </xsl:choose>	  
	  
	  <xsl:choose>
	    <xsl:when test="count(new_friends/item)+count(friends/item)+count(unfriends/item)>0">
	      <h5 style="margin:2px">Подписки:</h5>
	      <div style="padding-bottom:10px">
	      	
	        <xsl:for-each select="new_friends/item">
	          <xsl:sort order="ascending" select="."/>
	          <xsl:call-template name="show_user">
	            <xsl:with-param name="uname"><xsl:value-of select="."/></xsl:with-param>
	            <xsl:with-param name="class">new</xsl:with-param>
	          	<xsl:with-param name="type">f</xsl:with-param>
	          </xsl:call-template>
	          <xsl:if test="position()!=last() or count(//friends/item)>0"><xsl:text>, </xsl:text></xsl:if>
	        </xsl:for-each>
	      	
	      	<xsl:for-each select="friends/item[10>=position()]">
	      		<xsl:sort order="ascending" select="."/>
	      		<xsl:call-template name="show_user">
	      			<xsl:with-param name="uname"><xsl:value-of select="."/></xsl:with-param>
	      			<xsl:with-param name="class">sum</xsl:with-param>
	      			<xsl:with-param name="type">f</xsl:with-param>
	      		</xsl:call-template>          
	      		<xsl:if test="position()!=last() or count(//unfriends/item)>0"><xsl:text>, </xsl:text></xsl:if>
	      	</xsl:for-each>
	      	<xsl:if test="count(friends/item)>10">
	      		<br/>
	      		<a style="padding-right:5px" href="javascript:void(0)" onclick="if ($('#id_all_friends').css('display')=='none') $('#id_all_friends').css('display','');else $('#id_all_friends').css('display','none');">...ещё...</a>
	      		<br/>
	      		<span id="id_all_friends" style="display:none">
	      			<xsl:for-each select="friends/item[position()>10]">
	      				<xsl:sort order="ascending" select="."/>
	      				<xsl:call-template name="show_user">
	      					<xsl:with-param name="uname"><xsl:value-of select="."/></xsl:with-param>
	      					<xsl:with-param name="class">sum</xsl:with-param>
	      					<xsl:with-param name="type">f</xsl:with-param>
	      				</xsl:call-template>          
	      				<xsl:if test="position()!=last() or count(//unfriends/item)>0"><xsl:text>, </xsl:text></xsl:if>
	      			</xsl:for-each>	      				
	      		</span>	      				
	      	</xsl:if>
	      	
	        <xsl:for-each select="unfriends/item">
	          <xsl:sort order="ascending" select="."/>
	          <xsl:call-template name="show_user">
	            <xsl:with-param name="uname"><xsl:value-of select="."/></xsl:with-param>
	            <xsl:with-param name="class">un</xsl:with-param>
	          </xsl:call-template>          
	          <xsl:if test="position()!=last()"><xsl:text>, </xsl:text></xsl:if>
	        </xsl:for-each>     
	      </div>	      
	    </xsl:when>
	    <xsl:otherwise>
	      <h5 style="margin:2px">Подписок нет</h5>
	      <div style="padding-bottom:10px"></div>
	    </xsl:otherwise>
	  </xsl:choose>
	  
	  <xsl:choose>
	    <xsl:when test="count(messages/item)>0">
	      <h5 style="margin:2px">Сообщения:</h5>
	      <div style="padding-bottom:10px">
	        <xsl:for-each select="messages/item">
	          <div style="border-bottom: 1px dotted; margin-bottom: 10px;">
	            <a href="http://juick.com/{mid}" target="_blank"><xsl:value-of select="date_message"/></a>
	            <xsl:text> </xsl:text>
	            <xsl:value-of select="body"/>
	            <br/><label style="color:#8B5327"><xsl:value-of select="tags"/></label>
	          </div>        
	        </xsl:for-each>     
	      </div>	      
	    </xsl:when>
	    <xsl:otherwise>
	      <h5 style="margin:2px">Сообщений нет</h5>
	      <div style="padding-bottom:10px"></div>
	    </xsl:otherwise>
	  </xsl:choose>	  
	</xsl:template>
  
  <xsl:template name="show_user">
    <xsl:param name="uname"></xsl:param>
    <xsl:param name="class"></xsl:param>
  	<xsl:param name="type">s</xsl:param>
  	
  	<xsl:variable name="is_bold">
  		<xsl:choose>
  			<xsl:when test="$type='s'">
  				<xsl:if test="count(//new_friends/item[.=$uname])+count(//friends/item[.=$uname])>0">1</xsl:if>
  			</xsl:when>
  			<xsl:otherwise>
  				<xsl:if test="count(//new_subscribers/item[.=$uname])+count(//subscribers/item[.=$uname])>0">1</xsl:if>
  			</xsl:otherwise>
  		</xsl:choose>  		
  	</xsl:variable>
  	
    <a style="text-decoration:none;" href="http://juick.com/{$uname}" target="_blank">
    	<span class="{$class}">
    		<xsl:attribute name="style"><xsl:if test="$is_bold=1">font-weight:bold;</xsl:if>text-decoration:underline;</xsl:attribute>
    		<xsl:value-of select="$uname"/>
    	</span>
    </a>
  </xsl:template>
</xsl:stylesheet>
