<?php slot('title', __('Reports (temporary RMCA)'));  ?>

<div class="page">
  <h1><?php echo __('Reports (temporary RMCA)');?></h1>
  <form action='<?php echo url_for("specimensearch/downloadws");?>' method="POST"  target="_blank" style="display: inline; margin: 0;">
	   <select name="lbltem" id="lbltem" style='width:300px;'">
			<option value="-1">Pick up a report format</option>
			<option value="darwin2xls.xsl|export.xls|application/vnd.ms-excel+xml">Excel (XML for Excel)</option>
			<option value="darwin2csv.xsl|export.csv|text/csv">CSV</option>
			<option value="darwin2ara_reports.xsl|export_ara.doc|text/plain">File Arachnomorphae</option>
	   </select>
	     <a href=# id="print" title="Print Label"><button type="Submit" onclick="">Download</button> </a>
   
   
   
  	<textarea id="tmpData" name="tmpData" class="hideLabelXML" style="display:none">
	<?php
	/*echo htmlspecialchars_decode("<search_result>");
	echo htmlspecialchars_decode("<specimens>");
	foreach($specimensearch as $specimen) 
		{
			//echo $specimen->getXMLRepresentation();
			$tmpSpecXML = new XMLRepresentationOfSpecimen($specimen);
			echo htmlspecialchars_decode($tmpSpecXML->getXMLRepresentation());
			//echo htmlspecialchars($tmpSpecXML->getXMLRepresentation());
			
		}
	echo htmlspecialchars_decode("</specimens>");
	echo htmlspecialchars_decode("</search_result>");*/
	?>
	</textarea>  
	<input type="hidden" id="sess_id" name="sess_id" value="<?php print($random);?>"/>
	</form>


</div> 
  