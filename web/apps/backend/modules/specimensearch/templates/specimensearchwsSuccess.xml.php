<search_result>
<?php
	
		
	//echo  "<session_id>".$session_id_ws."</session_id>";
	echo  "<pagesize>".$pagesize."</pagesize>";
	echo  "<page>".$page."</page>";
	echo  "<offset>".$offset."</offset>";
	echo  "<order_by>".$orderBy."</order_by>";
	echo  "<order_dir>".$orderDir."</order_dir>";
	echo  "<debugstate>".$debugstate."</debugstate>";
	echo "<specimens>";
	foreach($specimensearch as $specimen) 
	{
		//echo htmlspecialchars_decode($specimen->getXMLRepresentation());
		$tmpSpecXML = new XMLRepresentationOfSpecimen($specimen);
		echo $tmpSpecXML->getXMLRepresentation();
		//echo htmlspecialchars_decode($tmpSpecXML->getXMLRepresentation());
		
	}
	echo "</specimens>";
?>
</search_result>