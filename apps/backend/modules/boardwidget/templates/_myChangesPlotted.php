<?php echo javascript_include_tag('jquery.flot.min.js');?> 
<!--[if IE]><?php echo javascript_include_tag('excanvas.min.js');?><![endif]-->
<script id="source" language="javascript" type="text/javascript"> 
$(function () {
	var reclose = false;
	if($('#myChangesPlotted .widget_top_button:visible').length)
	{
	  $('#myChangesPlotted .widget_content').show();
	  reclose=true;
	}
	var d = [<?php foreach($items as $item):?>
			  <?php echo '['. ( strtotime($item[0])*1000 ).', "'.$item[1].'"],' ;?>
			<?php endforeach;?>];

    $.plot($("#placeholder"),[d], {
            xaxis: {
                mode: "time",
                minTickSize: [1, "<?php switch($sf_request->getParameter('range'))
										{
										  case 'year': echo 'month';break;
										  case 'month': echo 'day';break;
										  default: echo 'day';break;
										}
					  ;?>"]
            },
			grid: { hoverable: true, clickable: true },
    		points: { show: true },
			lines: { fill: true, show: true}
        });




	 if (reclose) $('#myChangesPlotted .widget_content').hide();

     $('#myChangesPlotted ul.graph_range a').click(function(event)
     {
		event.preventDefault();
		$.ajax({
		  url: $(this).attr('href'),
          success: function(html) {
			$('#myChangesPlotted .widget_content').html(html);
		  }
		});
     });
});
</script> 
  <ul class="graph_range">
	<li class="inline"><?php echo link_to(__('Week'),'widgets/reloadContent?category=board&widget=myChangesPlotted&range=week');?></li>
	<li class="inline"><?php echo link_to(__('Month'),'widgets/reloadContent?category=board&widget=myChangesPlotted&range=month');?></li>
	<li class="inline"><?php echo link_to(__('Year'),'widgets/reloadContent?category=board&widget=myChangesPlotted&range=year');?></li>
  </ul>

<div id="placeholder" style="width:400px;height:200px"></div>
