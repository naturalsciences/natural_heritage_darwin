<div class="page">

<?php if(count($items) !=0 ):?>

  <table class="part_pinned_choose results">
  <?php use_helper('Text');?>
  <?php foreach($items as $i => $item):?>
    <tr class="rid_<?php echo $item->getPartRef(); ?>">
      <td>
        <?php echo image_tag('info.png',"title=info class=extd_info");?>
        <div class="extended_info" style="display:none;">
          <?php include_partial('extInfo', array('item'=> $item)); ?>
        </div>
      </td>
      <td class="item_name hidden"><?php echo $item->getPartRef();?></td>
      <td>
        <?php echo truncate_text($item->getAggregatedName(),40);?>
      </td>
      <td>
        <div class="result_choose"><?php echo __('Choose');?></div>
      </td>
    </tr>
  <?php endforeach;?>
  </table>


    <script  type="text/javascript">
    $(document).ready(function () {
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
          $('.results tbody tr').die('click');
          $('body').trigger('close_modal');
      });
    });
    </script>
<?php else:?>
  <p class="warn_message"><?php echo __('No Items here.');?> <?php echo link_to(__('Please pin some parts.'),'specimensearch/index',array('target'=>'_blank'));?></p>

<?php endif;?>
</div>