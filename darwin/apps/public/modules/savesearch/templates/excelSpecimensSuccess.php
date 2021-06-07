<div class="page">
  
<div name="text_div" id="text_div">
</div>

<script type="text/javascript">

$(document).ready(function () {
	
	
	var total=<?php print($total_size);?>;
	var query_id=<?php print($query_id);?>;
	var user_id=<?php print($user_id);?>;
	var size=<?php print($size);?>;
    var max_page=<?php print($max_page);?>;
	var nbpages=Math.ceil(total/size);
	for(var i=1; i<=nbpages; i++)
	{
		if(i<=max_page)
		{
			
			var url_report="http://hippomenes.naturalsciences.be:8088/pentaho/api/repos/%3Apublic%3ADarwin2%3AReports_excel%3Areport_excel_pager.prpt/report?ID_USER="+user_id+"&ID_Q="+query_id+"&SIZE="+size+"&PAGE="+i+"&output-target=table%2Fexcel%3Bpage-mode%3Dflow&accepted-page=-1&showParameters=true&renderMode=REPORT&htmlProportionalWidth=false&userid=report&password=report";
			$("#text_div").text("Generating Excel "+i+"/"+nbpages);
			//console.log(url_report);
			window.open(url_report, '_blank');
			                
			
		}
	
	}
	
});
	
</script>
</div>