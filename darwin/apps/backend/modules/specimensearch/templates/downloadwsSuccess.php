<?php
$xml = new DomDocument();
$xsl = new DomDocument();

$xmlMain="";
foreach($specimenList as $specimen) 
		{
	
			$tmpSpecXML = new XMLRepresentationOfSpecimen($specimen);
			$xmlMain.= htmlspecialchars_decode($tmpSpecXML->getXMLRepresentation());
			
			
		}

	
$xmlMain="<search_result><specimens>".$xmlMain."</specimens></search_result>";
		
//print($specimensearchxml);
//$xml->loadXML(str_replace(htmlspecialchars_decode(trim($specimensearchxml)), "&", "&amp;") );



$xml->loadXML(str_replace("&","&amp;",trim(htmlspecialchars_decode($xmlMain))));
//print($specimensearchxml);
$arrayFile=explode("|",$xsltfile);


if(count($arrayFile==3))
{


	$fileXSLT=$arrayFile[0];
	$fileCSV=$arrayFile[1];
	$MIMEextension=$arrayFile[2];
	$xsl->load("./xslt_reports/".$fileXSLT);

	$xslProcessor= new XsltProcessor();
	$xslProcessor->importStyleSheet($xsl);
					

					
	$out = $xslProcessor->transformToXML($xml);
	print($out);
	


}
?>