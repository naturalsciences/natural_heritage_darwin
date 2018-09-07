<?php if(! $form->getObject()->isNew()):?>
<table class="catalogue_table edition">
<thead>
  <tr>
    <th><?php echo __('Target') ;?></th>
    <th><?php echo __('Type') ;?></th>
    <th><?php echo __('Title');?></th>
    <th><?php echo __('Details') ;?></th>
    <th><?php echo __('Person') ;?></th>
    <th><?php echo __('Date') ;?></th>
    <th><?php echo __('Has files') ;?></th>
    <th></th>
  </tr>
</thead>
<tbody>
  <?php foreach($maintenances as $item):?>
    <tr>
      <td>
        <?php if($item->getReferencedRelation() == 'loans'):?>
          <?php if($table == 'loans'):?>
            <?php echo __('Loans');?>
          <?php elseif($table.'loan_items'):?>
            <?php echo link_to(__("Loan"),'loan/edit?id='.$item->getRecordId());?>
          <?php endif;?>
        <?php elseif($item->getReferencedRelation() == 'loan_items'):?>
          <?php if($table == 'loans'):?>
            <?php echo link_to(__("Item #%id%",array('%id%'=>$item->getRecordId())) ,'loanitem/edit?id='.$item->getRecordId());?>
          <?php elseif($table.'loan_items'):?>
            <?php echo __('Loan Item');?>
          <?php endif;?>
        <?php endif;?>
      </td>
      <td><?php echo __($item->getCategory());?></td>
      <td><?php echo __($item->getActionObservation());?></td>
      <td><?php echo $item->getDescription();?></td>
      <td><?php echo link_to(
          $item->People->getFormatedName(),
          'people/edit',
          array('query_string'=>'id='.$item->People->getId())
          );?></td>
      <td><?php echo $item->getModificationDateTimeMasked(ESC_RAW);?></td>
      <td><?php if($item->getWithMultimedia()) echo image_tag('attach.png'); else echo '-';?></td>
      <td class="buttons">
        <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'maintenances/view?id='.$item->getId());?>
        <?php echo link_to(image_tag('edit.png',array('title'=>__('Edit'))),'maintenances/edit?id='.$item->getId());?>
        <?php echo link_to(image_tag('remove.png', array("title" => __("Delete"))),'maintenances/delmaintenance?id='.$item->getId(),'class=delete_maint_button');?>
      </td>
    </tr>
  <?php endforeach;?>
</tbody>
 <tfoot>
   <tr>
     <td colspan="8">
        <a href="<?php echo url_for('maintenances/new?table='.$table.'&record_id='.$eid);?>" target="_blank"><?php echo __('Add');?></a>
     </td>
   </tr>
  </tfoot>
</table>
<script type="text/javascript">
  $(document).ready(function () {
    $('.delete_maint_button').click(function(event)
    {
      event.preventDefault();
      el = $(this);
      $.ajax({
        url: el.attr('href'),
        success: function(html){
          el.closest('tr').hide();
        }
      });
    });
  });
</script>
<?php else:?>
  <?php echo __('Please save your item in order to view related maintenances') ?>
<?php endif;?>
