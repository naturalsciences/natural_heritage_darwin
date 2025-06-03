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
        <?php echo image_tag('info.png',"title=info class=extd_info");?>
        <div class="extended_info" style="display:none;">
          <?php include_partial('extInfo', array('item'=> $item)); ?>
        </div>
      </td>
      <td class="item_name hidden"><?php echo $item->getId();?></td>
      <td>
        <?php echo truncate_text($item->getAggregatedName(),40);?>
      </td>
      <td>
        <div class="result_choose"><?php echo __('Choose');?></div>
      </td>
    </tr>
	<?php $cpt=$cpt+1;?>
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
      //attention: need to specify synchrnous ajax queries in "addPined" (overveiwSuccss.php)
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
  <p class="warn_message"><?php echo __('No Items here.');?> <?php echo link_to(__('Please pin some specimens.'),'specimensearch/index?specimen_search_filters[what_searched]=part',array('target'=>'_blank'));?></p>

<?php endif;?>
</div>
