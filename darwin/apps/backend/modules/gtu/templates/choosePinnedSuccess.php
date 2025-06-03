<div class="page">
<?php if(count($items) !=0 ):?>
<?php $cpt=1;?>
<div><a class="result_choose_all"><?php echo __('Choose all');?></a></div>
  <table class="part_pinned_choose results">
  <?php use_helper('Text');?>
  <?php foreach($items as $i => $item):?>
	<tr class="rid_<?php echo $item->getId(); ?>">
		<td><?php print($cpt);?></td>
		<td>
			<?php echo $item->getId();?>
		</td>
		<td>
			<?php echo $item->getCode();?>
		</td>
		<td>
			<?php echo $item->getName(ESC_RAW);?>
		</td>
		<td>
		<!--ftheeten 2018 12 2-->
				<?php if(strlen(trim($item->getComments()))>0):?>
				<div>
						 <ul class='search_tags'>
						 <li>
							<ul class='name_tags_view'>
							 <?php if(strlen(trim($item->getComments()))>0):?>
								<?php foreach(explode("|", $item->getComments()) as $comment):?>
								<li><?php print($comment);?></li>
								<?php endforeach;?>
								<?php endif;?>
							</ul>
							</li>
						</ul>
						<br/>

					   </ul>
				 </div>
				 <?php endif;?>
				 <?php if(strlen(trim($item->getProperties()))>0):?>
				<div>
						 <ul class='search_tags'>
						 <li>
							<ul class='name_tags_view'>
							 <?php if(strlen(trim($item->getProperties()))>0):?>
								<?php foreach(explode("|", $item->getProperties()) as $property):?>
								<li><?php print($property);?></li>
								<?php endforeach;?>
								<?php endif;?>
							</ul>
							</li>
						</ul>
						<br/>

					   </ul>
			</div>
			<?php endif;?>
		<td>
		<td>
			<div class="result_choose"><?php echo __('Choose');?></div>
		</td>
  	<?php $cpt=$cpt+1;?>
	</tr>
  <?php endforeach;?>
  </table>
 <script  type="text/javascript">
    $(document).ready(function () {
        $.fn.qtip.zindex = 16001; // Non-modal z-index
        $('img.extd_info').each(function(){
          tip_content = $(this).next().html();
          $(this).qtip(
          {
            content: tip_content,
            style: {
              tip: true, // Give it a speech bubble tip with automatic corner detection
              name: 'cream'
            }
          });
        });
      $('.result_choose').bind('click', function () {
          ref_element_id = getIdInClasses($(this).closest('tr'));
          ref_element_name = $(this).closest('tr').children("td.item_name").text();
          if(typeof fct_update=="function")
          {
            $(this).closest('tr').remove();            
            fct_update(ref_element_id, ref_element_name);
            if($('table.part_pinned_choose').find('tr').length == 0) 
            {
              $('.results tbody tr').die('click');
             $('body').trigger('close_modal');           
            }
          }
          else
          {
            $('.results tbody tr').die('click');
            $('body').trigger('close_modal');
          }
      });
      //ftheeten 2016 06 07
      //attention: need to specify synchronous ajax queries in "addPined" (overviewSuccess.php)
       $('.result_choose_all').bind('click', function () {
        
         $('.result_choose').each(
            function(index)
            {
               $( this ).click();
            }
         );
      });
    });
    </script>
<?php else:?>
<p class="warn_message"><?php echo __('No Items here.');?> <?php echo link_to(__('Please pin some localities.'),'gtu/index?',array('target'=>'_blank'));?></p>
<?php endif;?>
</div>