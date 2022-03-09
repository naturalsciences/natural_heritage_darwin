<?php if(isset($pagerLayout) && isset($form['rec_per_page'])): ?>
  <?php 
        //ftheeten 2018 04 17
        
       
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $tmp_array=$_POST;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
     $tmp_array=$_GET;
    }
    $tmp_array=array_filter_recursive($tmp_array);
    $abs_url=url_for("specimensearch/ajaxPager/is_choose/",true)."/".$currentPage."?".http_build_query($tmp_array);
   ?>
    <div class="pager paging_info">
      <table>
        <tr>
          <td><?php echo image_tag('info2.png');?></td>
          <td>
		  <div id="result_stat" class="result_stat">
	    <?php echo format_number_choice('[0]No Results Retrieved|[1]Your query retrieved  1 record|(1,+Inf]Your query retrieved : <br/>  &#8226; %1% records <i>(wait for more details)</i>', array('%1%' =>  $pagerLayout->getPager()->getNumResults(),  '%2%' =>  $pagerLayout->getPager()->additional_count["count_ig"]),  $pagerLayout->getPager()->getNumResults()) ?>
		</div>
	  </td>
	  <td>
		<div id="result_mids" class="result_mids"/>
	  </td>
          <td><ul><li><?php echo $form['rec_per_page']->renderLabel(); echo $form['rec_per_page']->render(); ?></li></ul></td>
        </tr>
      </table>
    </div>

  <script type="text/javascript">
  $(document).ready(function () {
    $("<?php if(! isset($container)) echo ".results_container"; else echo $container;?>").pager({});
	var get_mids=function()
	{
		$.getJSON( "<?php print($abs_url); ?>", function( data ) {
				
				var text_count="Your query retrieved : <br/>  &#8226; "+data.count+" records<br/>   &#8226; "+data.count_ig+" distinct records based on I.G. and main collection tag<br/>   &#8226; "+data.count_min+" physical records (minimal estimate) <br/>  &#8226; "+data.count_max+" physical records (maximal estimate)";
				$("#result_stat").html(text_count);
				var mids_count=" <br/>  &#8226; MIDS 0 : "+data.count_mids0+" records, "+data.spec_min_mids0+" physical records (minimal estimate), "+data.spec_max_mids0+" physical records (maximal estimate) <br/>  &#8226; MIDS 1 : "+data.count_mids1+" records, "+data.spec_min_mids1+" physical records (minimal estimate), "+data.spec_max_mids1+" physical records (maximal estimate)  <br/>  &#8226; MIDS 2 : "+data.count_mids2+" records, "+data.spec_min_mids2+" physical records (minimal estimate), "+data.spec_max_mids2+" physical records (maximal estimate)  <br/>  &#8226; MIDS 3 : "+data.count_mids3+" records, "+data.spec_min_mids3+" physical records (minimal estimate), "+data.spec_max_mids3+" physical records (maximal estimate) ";
				$("#result_mids").html(mids_count);
			});
	}
	get_mids();
	

  });
  </script>
<?php endif; ?>
