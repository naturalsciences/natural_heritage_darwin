<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"


xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel"

>
 <xsl:strip-space elements="*"/>
<xsl:template match="/">
<!--Excel-->
<Workbook xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel"><OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
</OfficeDocumentSettings>
<ss:Worksheet ss:Name="Sheet1">
<Table>
<xsl:apply-templates select="//specimens/specimen[1]" mode="header" />
<xsl:apply-templates select="/search_result/specimens/specimen" />
</Table><x:WorksheetOptions/>
</ss:Worksheet>
</Workbook>
<!--Excel-->

</xsl:template>

<!--ID-->
<xsl:template match="/search_result/specimens/specimen/id">
	<Cell><Data ss:Type="String">
	<xsl:value-of select="."/>
	</Data></Cell>
</xsl:template>
<xsl:template match="/search_result/specimens/specimen/collection_code">
	<Cell><Data ss:Type="String">
	<xsl:value-of select="."/>
	</Data></Cell>
</xsl:template>
<xsl:template match="/search_result/specimens/specimen/specimen_codes">
	<Cell><Data ss:Type="String">
	<xsl:value-of select="specimen_code"/>
	</Data></Cell>
</xsl:template>

<!--taxo-->
<xsl:template match="/search_result/specimens/specimen/family">
<Cell><Data ss:Type="String"><xsl:value-of select="."/></Data></Cell>
</xsl:template>
<xsl:template match="/search_result/specimens/specimen/taxon">
<Cell><Data ss:Type="String"><xsl:value-of select="."/></Data></Cell>
</xsl:template>
<xsl:template match="/search_result/specimens/specimen/type_information">
<Cell><Data ss:Type="String"><xsl:value-of select="."/></Data></Cell>
</xsl:template>

<!--count-->
<xsl:template match="/search_result/specimens/specimen/specimen_count_min">
<Cell><Data ss:Type="String"><xsl:value-of select="."/></Data></Cell>
</xsl:template>
<xsl:template match="/search_result/specimens/specimen/specimen_count_max">
<Cell><Data ss:Type="String"><xsl:value-of select="."/></Data></Cell>
</xsl:template>

<!--VALUES-->
<!--gtu-->
<xsl:template match="/search_result/specimens/specimen/gtu">
<Cell><Data ss:Type="String"><xsl:apply-templates select="gtu_element[gtu_element_name='Country']/gtu_element_values/gtu_element_value"/></Data></Cell>
<Cell><Data ss:Type="String"><xsl:apply-templates select="gtu_element[gtu_element_name='Municipality']/gtu_element_values/gtu_element_value"/></Data></Cell>
<Cell><Data ss:Type="String"><xsl:apply-templates select="gtu_element[gtu_element_name='Region or district']/gtu_element_values/gtu_element_value"/></Data></Cell>
<Cell><Data ss:Type="String"><xsl:apply-templates select="gtu_element[gtu_element_name='Exact_site']/gtu_element_values/gtu_element_value"/></Data></Cell>
<Cell><Data ss:Type="String"><xsl:apply-templates select="gtu_element[gtu_element_name='ecology']/gtu_element_values/gtu_element_value"/></Data></Cell>
<xsl:apply-templates select="coordinates"/>
<Cell><Data ss:Type="String"><xsl:apply-templates select="date_begin"/></Data></Cell>
<Cell><Data ss:Type="String"><xsl:apply-templates select="date_end"/></Data></Cell>
<Cell><Data ss:Type="String"><xsl:apply-templates select="gtu_properties"/></Data></Cell>
</xsl:template>


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
<!--Muncipality-->
<xsl:template match="gtu_element[gtu_element_name='ecology']"><xsl:for-each select="./gtu_element_values/gtu_element_value"><xsl:value-of select="."/></xsl:for-each></xsl:template>

<!--coordinates-->
<xsl:template match="coordinates/latitude"><Cell><Data ss:Type="String"><xsl:value-of select="." /></Data></Cell></xsl:template>
<xsl:template match="coordinates/longitude"><Cell><Data ss:Type="String"><xsl:value-of select="." /></Data></Cell></xsl:template>
<xsl:template match="coordinates/elevation"><Cell><Data ss:Type="String"><xsl:value-of select="." /></Data></Cell></xsl:template>
<!--collecting dates begin-->
<xsl:template match="date_begin"><xsl:apply-templates select="day"/><xsl:apply-templates select="month"/><xsl:apply-templates select="year"/></xsl:template>
<!--collecting dates end-->
<xsl:template match="date_end"><xsl:apply-templates select="day"/><xsl:apply-templates select="month"/><xsl:apply-templates select="year"/></xsl:template>
<!--date generic (append '/' to day and month)-->
<xsl:template match="day"><xsl:if test=". !=''"><xsl:value-of select="concat(., '/')" /></xsl:if></xsl:template>
<xsl:template match="month"><xsl:if test=". !=''"><xsl:value-of select="concat(., '/')" /></xsl:if></xsl:template>
<xsl:template match="year"><xsl:value-of select="." /></xsl:template>


<!--properties (localities)-->
<xsl:template match="gtu_properties"><Cell><Data ss:Type="String"><xsl:for-each select="gtu_property"><xsl:if test="count(preceding-sibling::gtu_property) &gt; 0">, </xsl:if><xsl:value-of select="property_type"/>: <xsl:value-of select="./lower_value"/></xsl:for-each></Data></Cell></xsl:template>


<!--collectors-->
<xsl:template match="/search_result/specimens/specimen/collectors"><Cell><Data ss:Type="String"><xsl:for-each select="collector" ><xsl:if test="count(preceding-sibling::collector) &gt; 0">,</xsl:if><xsl:value-of select="formated_name"/></xsl:for-each></Data></Cell></xsl:template>


<!--identifications-->
<xsl:template match="/search_result/specimens/specimen/identifications"><Cell><Data ss:Type="String"><xsl:apply-templates select="identification/identifier" /></Data></Cell><Cell><Data ss:Type="String"><xsl:apply-templates select="identification/date/year" /></Data></Cell></xsl:template>
<xsl:template match="identification/identifier"><xsl:for-each select="formated_name" ><xsl:if test="count(../preceding-sibling::identifier) &gt; 0">, </xsl:if><xsl:value-of select="."/></xsl:for-each></xsl:template>
<xsl:template match="identification/date/year"><xsl:value-of select="."/></xsl:template>


<!--PROPERTIES-->
<xsl:template match="/search_result/specimens/specimen/specimen_properties"><Cell><Data ss:Type="String"><xsl:apply-templates select="specimen_property[property_type='N males']"/></Data></Cell><Cell><Data ss:Type="String"><xsl:apply-templates select="specimen_property[property_type='N females']"/></Data></Cell></xsl:template>

<xsl:template match="specimen_property[property_type='N females']"><xsl:value-of select="./lower_value"/></xsl:template>

<!--males-->
<xsl:template match="specimen_property[property_type='N males']"><xsl:value-of select="./lower_value"/></xsl:template>


<!--specimens comments-->
<xsl:template match="/search_result/specimens/specimen/specimen_comments">
<Cell><Data ss:Type="String">
<xsl:for-each select="specimen_comment">
<xsl:if test="count(preceding-sibling::specimen_comment) &gt; 0">, </xsl:if>
<xsl:value-of select="comment_type"/>: <xsl:value-of select="comment_value"/>
</xsl:for-each>
</Data></Cell>
</xsl:template>

<!--HEADERS-->


<xsl:template match="//specimens/specimen[1]" mode="header">
<Row ss:Height="12.8409">
<Cell><Data ss:Type="String">id</Data></Cell>
<Cell><Data ss:Type="String">collection</Data></Cell>
<Cell><Data ss:Type="String">code</Data></Cell>
<Cell><Data ss:Type="String">taxon_name</Data></Cell>
<Cell><Data ss:Type="String">family</Data></Cell>
<Cell><Data ss:Type="String">type_information</Data></Cell>
<Cell><Data ss:Type="String">specimen_count_min</Data></Cell>
<Cell><Data ss:Type="String">specimen_count_max</Data></Cell>
<Cell><Data ss:Type="String">identifiers</Data></Cell>
<Cell><Data ss:Type="String">identification_year</Data></Cell>
<Cell><Data ss:Type="String">country</Data></Cell>
<Cell><Data ss:Type="String">municipality</Data></Cell>
<Cell><Data ss:Type="String">region_or_district</Data></Cell>
<Cell><Data ss:Type="String">exact_site</Data></Cell>
<Cell><Data ss:Type="String">ecology</Data></Cell>
<Cell><Data ss:Type="String">latitude</Data></Cell>
<Cell><Data ss:Type="String">longitude</Data></Cell>
<Cell><Data ss:Type="String">elevation</Data></Cell>
<Cell><Data ss:Type="String">collecting_date_begin</Data></Cell>
<Cell><Data ss:Type="String">collecting_date_end</Data></Cell>
<Cell><Data ss:Type="String">locality_properties</Data></Cell>
<Cell><Data ss:Type="String">collectors</Data></Cell>
<Cell><Data ss:Type="String">amount_males</Data></Cell>
<Cell><Data ss:Type="String">amount_females</Data></Cell>
<Cell><Data ss:Type="String">comments</Data></Cell>
</Row>
</xsl:template>

<!--END HEADER-->


<!--VALUES-->
<xsl:template match="specimen/*"></xsl:template>
<xsl:template match="/search_result/specimens/specimen"><Row ss:Height="12.8409"><xsl:apply-templates match="specimen/*" /></Row></xsl:template>

</xsl:stylesheet>
