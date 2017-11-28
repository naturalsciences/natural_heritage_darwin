<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text" omit-xml-declaration="yes" indent="no"/>
 <xsl:strip-space elements="*"/>

<!--HEADERS-->




<xsl:template match="//specimens/specimen[1]" mode="header"><xsl:text>id	collection	code	taxon_name	family	type_information	specimen_count_min	specimen_count_max	identifiers	identification_year	country	municipality	region_or_district	exact_site	ecology	latitude	longitude	elevation	collecting_date_begin	collecting_date_end	locality_properties	collectors	amount_females	amount_males	comments</xsl:text></xsl:template>

<!--END HEADER-->


<!--VALUES-->
<!--gtu-->
<xsl:template match="/search_result/specimens/specimen/gtu"><xsl:apply-templates select="gtu_element[gtu_element_name='Country']"/><xsl:text>	</xsl:text><xsl:apply-templates select="gtu_element[gtu_element_name='Municipality']"/><xsl:text>	</xsl:text><xsl:apply-templates select="gtu_element[gtu_element_name='Region or district']"/><xsl:text>	</xsl:text><xsl:apply-templates select="gtu_element[gtu_element_name='Exact site']"/><xsl:text>	</xsl:text><xsl:apply-templates select="gtu_element[gtu_element_name='ecology']"/><xsl:text>	</xsl:text><xsl:apply-templates select="coordinates"/>	<xsl:apply-templates select="date_begin"/>	<xsl:apply-templates select="date_end"/><xsl:apply-templates select="gtu_properties"/><xsl:text>	</xsl:text></xsl:template>

<!--
<xsl:template select="gtu_element"><xsl:apply-templates select="gtu_element[gtu_element_name='Continent']"/><xsl:apply-templates select="gtu_element[gtu_element_name='Country']"/><xsl:apply-templates select="gtu_element[gtu_element_name='Municipality']"/><xsl:text>ddddddddddddddddddddddddddddd</xsl:text><xsl:apply-templates select="gtu_element[gtu_element_name='Region or district']"/><xsl:apply-templates select="gtu_element[gtu_element_name='Exact site']"/><xsl:apply-templates select="gtu_element[gtu_element_name='ecology']"/><xsl:if test="count(./gtu_element) &gt; 0"><xsl:text>No ecology</xsl:text></xsl:if><xsl:text>loplop	</xsl:text></xsl:template>-->
<!--Continent-->
<xsl:template match="gtu_element[gtu_element_name='Continent']"></xsl:template>
<!--Country-->
<xsl:template match="gtu_element[gtu_element_name='Country']"><xsl:for-each select="./gtu_element_values/gtu_element_value"><xsl:value-of select="."/></xsl:for-each></xsl:template>
<!--Muncipality-->
<xsl:template match="gtu_element[gtu_element_name='Municipality']"><xsl:for-each select="./gtu_element_values/gtu_element_value"><xsl:value-of select="."/></xsl:for-each></xsl:template>
<!--Muncipality-->
<xsl:template match="gtu_element[gtu_element_name='Region or district']"><xsl:for-each select="./gtu_element_values/gtu_element_value"><xsl:value-of select="."/></xsl:for-each></xsl:template>
<!--Muncipality-->
<xsl:template match="gtu_element[gtu_element_name='Exact site']"><xsl:for-each select="./gtu_element_values/gtu_element_value"><xsl:value-of select="."/></xsl:for-each></xsl:template>
<!--ecology-->
<xsl:template match="gtu_element[gtu_element_name='ecology']"><xsl:for-each select="./gtu_element_values/gtu_element_value"><xsl:value-of select="."/></xsl:for-each></xsl:template>

<!--coordinates-->
<xsl:template match="coordinates/latitude"><xsl:value-of select="." /><xsl:text>	</xsl:text></xsl:template>
<xsl:template match="coordinates/longitude"><xsl:value-of select="." /><xsl:text>	</xsl:text></xsl:template>
<xsl:template match="coordinates/elevation"><xsl:value-of select="." /><xsl:text>	</xsl:text></xsl:template>
<!--collecting dates begin-->
<xsl:template match="date_begin"><xsl:apply-templates select="day"/><xsl:apply-templates select="month"/><xsl:apply-templates select="year"/><xsl:text>	</xsl:text></xsl:template>
<!--collecting dates end-->
<xsl:template match="date_end"><xsl:apply-templates select="day"/><xsl:apply-templates select="month"/><xsl:apply-templates select="year"/><xsl:text>	</xsl:text></xsl:template>
<!--date generic (append '/' to day and month)-->
<xsl:template match="day"><xsl:if test=". !=''"><xsl:value-of select="concat(., '/')" /></xsl:if></xsl:template>
<xsl:template match="month"><xsl:if test=". !=''"><xsl:value-of select="concat(., '/')" /></xsl:if></xsl:template>
<xsl:template match="year"><xsl:value-of select="." /></xsl:template>

<!--properties (gtu)-->
<xsl:template match="gtu_properties"><xsl:for-each select="gtu_property"><xsl:if test="count(preceding-sibling::gtu_property) &gt; 0">, </xsl:if><xsl:value-of select="property_type"/>: <xsl:value-of select="./lower_value"/></xsl:for-each></xsl:template>



<!--collectors-->
<xsl:template match="/search_result/specimens/specimen/collectors"><xsl:for-each select="collector" ><xsl:if test="count(preceding-sibling::collector) &gt; 0">,</xsl:if><xsl:value-of select="formated_name"/></xsl:for-each><xsl:text>	</xsl:text></xsl:template>


<!--identifications-->
<xsl:template match="/search_result/specimens/specimen/identifications"><xsl:apply-templates select="identification/identifier" /><xsl:text>	</xsl:text><xsl:apply-templates select="identification/date/year" /></xsl:template>
<xsl:template match="identification/identifier"><xsl:for-each select="formated_name" ><xsl:if test="count(../preceding-sibling::identifier) &gt; 0">, </xsl:if><xsl:value-of select="."/></xsl:for-each></xsl:template>
<xsl:template match="identification/date/year"><xsl:value-of select="."/><xsl:text>	</xsl:text></xsl:template>

<!--PROPERTIES-->
<xsl:template match="/search_result/specimens/specimen/specimen_properties"><xsl:apply-templates select="specimen_property[property_type='N males']"/><xsl:text>	</xsl:text><xsl:apply-templates select="specimen_property[property_type='N females']"/><xsl:text>	</xsl:text></xsl:template>
<!--<xsl:template match="specimen_property"><xsl:apply-templates select="specimen_property[property_type='N males']"/><xsl:text>	</xsl:text><xsl:apply-templates select="specimen_property[property_type='N females']"/><xsl:text>	</xsl:text></xsl:template>-->
<!--Females-->
<xsl:template match="specimen_property[property_type='N females']"><xsl:value-of select="./lower_value"/></xsl:template>
<!--males-->
<xsl:template match="specimen_property[property_type='N males']"><xsl:value-of select="./lower_value"/></xsl:template>

<!--ID-->
<xsl:template match="/search_result/specimens/specimen/id">
<xsl:value-of select="."/><xsl:text>	</xsl:text>
</xsl:template>
<xsl:template match="/search_result/specimens/specimen/collection_code">
<xsl:value-of select="."/><xsl:text>	</xsl:text>
</xsl:template>
<xsl:template match="/search_result/specimens/specimen/specimen_codes">
<xsl:value-of select="specimen_code"/><xsl:text>	</xsl:text>
</xsl:template>

<!--taxo-->
<xsl:template match="/search_result/specimens/specimen/family">
<xsl:value-of select="."/><xsl:text>	</xsl:text>
</xsl:template>
<xsl:template match="/search_result/specimens/specimen/taxon">
<xsl:value-of select="."/><xsl:text>	</xsl:text>
</xsl:template>
<xsl:template match="/search_result/specimens/specimen/type_information">
<xsl:value-of select="."/><xsl:text>	</xsl:text>
</xsl:template>

<!--count-->
<xsl:template match="/search_result/specimens/specimen/specimen_count_min">
<xsl:value-of select="."/><xsl:text>	</xsl:text>
</xsl:template>
<xsl:template match="/search_result/specimens/specimen/specimen_count_max">
<xsl:value-of select="."/><xsl:text>	</xsl:text>
</xsl:template>

<!--specimens comments-->
<xsl:template match="/search_result/specimens/specimen/specimen_comments">
<xsl:for-each select="specimen_comment">
<xsl:if test="count(preceding-sibling::specimen_comment) &gt; 0">, </xsl:if>
<xsl:value-of select="comment_type"/>: <xsl:value-of select="comment_value"/>
</xsl:for-each>
</xsl:template>

<!--VALUES-->
<xsl:template match="specimen/*"></xsl:template>
<xsl:template match="/search_result/specimens/specimen"><xsl:apply-templates match="specimen/*" /><xsl:text>
</xsl:text></xsl:template>


<!--MAIN-->
<xsl:template match="/">
<xsl:apply-templates select="//specimens/specimen[1]" mode="header" /><xsl:text>
</xsl:text>
<xsl:apply-templates select="/search_result/specimens/specimen" /></xsl:template>
</xsl:stylesheet>
