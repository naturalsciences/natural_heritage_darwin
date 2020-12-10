<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text" omit-xml-declaration="yes" indent="no"/>


<!--collecting dates-->
<!--collecting dates begin-->
<xsl:template match="gtu/date_begin"><xsl:apply-templates select="day"/><xsl:apply-templates select="month"/><xsl:apply-templates select="year"/><xsl:text>	</xsl:text></xsl:template>
<!--collecting dates end-->
<xsl:template match="gtu/date_end"><xsl:apply-templates select="day"/><xsl:apply-templates select="month"/><xsl:apply-templates select="year"/><xsl:text>	</xsl:text></xsl:template>
<!--date generic (append '/' to day and month)-->
<xsl:template match="day"><xsl:if test=". !=''"><xsl:value-of select="concat(., '/')" /></xsl:if></xsl:template>
<xsl:template match="month"><xsl:if test=". !=''"><xsl:value-of select="concat(., '/')" /></xsl:if></xsl:template>
<xsl:template match="year"><xsl:value-of select="." /></xsl:template>
<!--determinator-->
<xsl:template match="identifications/identification/identifier"><xsl:for-each select="formated_name" ><xsl:if test="count(../preceding-sibling::identifier) &gt; 0">, </xsl:if><xsl:value-of select="."/></xsl:for-each></xsl:template>
<xsl:template match="identifications/identification/date/year"><xsl:value-of select="."/></xsl:template>

<!--collectors-->
<xsl:template match="collectors"><xsl:for-each select="collector" ><xsl:if test="count(preceding-sibling::collector) &gt; 0">,</xsl:if><xsl:value-of select="formated_name"/></xsl:for-each></xsl:template>

<!--VALUES-->

<xsl:template match="/search_result/specimens/specimen">
<!--1st row-->
<xsl:value-of select="specimen_codes/specimen_code"/>
<xsl:text> </xsl:text>
<xsl:value-of select="taxon"/>
<xsl:text> (</xsl:text>
<xsl:value-of select="family"/><xsl:text>)</xsl:text>
<xsl:text> </xsl:text>
<xsl:value-of select="type_information"/>
<!--2nd row-->
<xsl:if test="string-length(specimen_properties/specimen_property[property_type='N males']/lower_value) !=0 or string-length(specimen_properties/specimen_property[property_type='N females']/lower_value) !=0 ">
<xsl:text>
</xsl:text>
<xsl:text> </xsl:text>
<xsl:if test="specimen_properties/specimen_property[property_type='N males']/lower_value &gt; 0"><xsl:value-of select="specimen_properties/specimen_property[property_type='N males']/lower_value"/><xsl:text>M </xsl:text></xsl:if>
<xsl:if test="specimen_properties/specimen_property[property_type='N females']/lower_value &gt; 0"><xsl:value-of select="specimen_properties/specimen_property[property_type='N females']/lower_value"/><xsl:text>F</xsl:text></xsl:if>
</xsl:if>
<!--3rd row-->
<xsl:if test="string-length(identifications/identification/identifier) !=0 ">
<xsl:text>
</xsl:text>
<xsl:text> Det. by </xsl:text><xsl:apply-templates select="identifications/identification/identifier"/><xsl:text> in </xsl:text><xsl:apply-templates select="identifications/identification/date/year"/>
</xsl:if>
<!--4th row-->
<xsl:if test="string-length(identifications/identification/collectors) !=0 or string-length(gtu/date_begin) !=0">
<xsl:text>
</xsl:text>
<xsl:text> Col. by </xsl:text><xsl:apply-templates select="collectors"/><xsl:text> on </xsl:text><xsl:apply-templates select="gtu/date_begin"/>
</xsl:if>
<!--5th row-->
<xsl:if test="string-length(gtu/gtu_element[gtu_element_name='Municipality']/gtu_element_values/gtu_element_value) !=0 
or 
string-length(gtu/gtu_element[gtu_element_name='Region or district']/gtu_element_values/gtu_element_value) !=0
or
string-length(gtu/gtu_element[gtu_element_name='Exact site']/gtu_element_values/gtu_element_value) !=0
or
string-length(gtu/gtu_element[gtu_element_name='Country']/gtu_element_values/gtu_element_value) !=0
or
string-length(gtu/coordinates/latitude) !=0
or
string-length(gtu/coordinates/longitude) !=0
">
<xsl:text>
</xsl:text>
<xsl:text> Loc. </xsl:text>
<xsl:apply-templates select="gtu/gtu_element[gtu_element_name='Municipality']/gtu_element_values/gtu_element_value"/>
<xsl:if test="string-length(gtu/gtu_element[gtu_element_name='Region or district']/gtu_element_values/gtu_element_value) !=0">
<xsl:text> </xsl:text>
<xsl:apply-templates select="gtu/gtu_element[gtu_element_name='Region or district']/gtu_element_values/gtu_element_value"/>
</xsl:if>
<xsl:if test="string-length(gtu/gtu_element[gtu_element_name='Exact site']/gtu_element_values/gtu_element_value) !=0">
<xsl:text> </xsl:text>
<xsl:apply-templates select="gtu/gtu_element[gtu_element_name='Exact site']/gtu_element_values/gtu_element_value"/>
</xsl:if>
<xsl:if test="string-length(gtu/gtu_element[gtu_element_name='Country']/gtu_element_values/gtu_element_value) !=0">
<xsl:text> </xsl:text>
<xsl:apply-templates select="gtu/gtu_element[gtu_element_name='Country']/gtu_element_values/gtu_element_value"/>
</xsl:if>
<xsl:if test="string-length(gtu/coordinates/latitude) !=0 or string-length(gtu/coordinates/longitude) !=0">
<xsl:text>(</xsl:text>
<xsl:value-of select="concat('lat.: ', gtu/coordinates/latitude)"/>
<xsl:text> </xsl:text>
<xsl:value-of select="concat('long.: ',gtu/coordinates/longitude)"/>
<xsl:text>)</xsl:text>
</xsl:if>
</xsl:if>
<!--6th row-->
<xsl:if test="string-length(gtu/gtu_element[gtu_element_name='ecology']/gtu_element_values/gtu_element_value) !=0">
<xsl:text>
</xsl:text>
<xsl:text> Ecol. </xsl:text><xsl:apply-templates select="gtu/gtu_element[gtu_element_name='ecology']/gtu_element_values/gtu_element_value"/>
</xsl:if>
<!--next record-->
<xsl:text>
</xsl:text>
</xsl:template>


<!--MAIN-->
<xsl:template match="/">

<xsl:apply-templates select="/search_result/specimens/specimen" /></xsl:template>
</xsl:stylesheet>
