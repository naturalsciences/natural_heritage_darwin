<div class="page">

<?php if(count($items) !=0 ):?>

  <table class="part_pinned_choose results">
  <?php use_helper('Text');?>
  <?php foreach($items as $i => $item):?>
    <tr class="rid_<?php echo $item->getPartRef(); ?>">
      <td>
        <?php echo image_tag('info.png',"title=info class=extd_info");?>
        <div class="extended_info" style="display:none;">
          <dl>
              <dt><?php echo __('Collection :');?></dt>
              <dd><?php echo $item->getCollectionName();?></dd>
              <dt><?php echo __('Taxonomy :');?></dt>
              <dd><?php echo $item->getTaxonName();?></dd>
              <dt><?php echo __('Sampling Location :');?></dt>
              <dd><?php echo $item->getGtu(ESC_RAW);?></dd>
              <dt><?php echo __('Type :');?></dt>
              <dd><?php echo $item->getIndividualTypeGroup();?></dd>
              <dt><?php echo __('Sex :');?></dt>
              <dd><?php echo $item->getIndividualSex();?></dd>
              <dt><?php echo __('State :');?></dt>
              <dd><?php echo $item->getIndividualState();?></dd>
              <dt><?php echo __('Building :');?></dt>
              <dd><?php echo $item->getBuilding();?></dd>
              <dt><?php echo __('Floor :');?></dt>
              <dd><?php echo $item->getFloor();?></dd>
              <dt><?php echo __('Room :');?></dt>
              <dd><?php echo $item->getRoom();?></dd>
              <dt><?php echo __('Row :');?></dt>
              <dd><?php echo $item->getRow();?></dd>
              <dt><?php echo __('Shelf :');?></dt>
              <dd><?php echo $item->getShelf();?></dd>
          </dl>
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
      /* $('.row_delete').click(function(event)
        {
          event.preventDefault();
          $(this).closest('tr').remove();
          checkItem();
        });
  */
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
  <p class="warn_message"><?php echo __('No Items here. Please pin some items or another source to be able to do a mass action');?></p>
<?php endif;?>
</div>