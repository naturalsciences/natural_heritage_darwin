<table class="table_main_info">
  <tbody>
    <?php echo $form->renderGlobalErrors() ?>
    <?php $obj = $form->getObject();?>
    <tr>
      <th><?php echo __('Part Id');?></th>
      <td>
        <?php if($obj->getSpecimenRef()):?>
          <?php echo image_tag('info.png',"title=info class=extd_info data_id=".$obj->getSpecimenRef());?>
          <?php echo link_to('#'.$obj->getSpecimenRef(),'specimen/edit?id='.$obj->getSpecimenRef());?>
        <?php endif;?>
      </td>
      <th><?php echo __('Details');?></th>
      <td rowspan="3" class="loanitem_details"><?php echo $obj->getDetails();?></td>
    </tr>
    <tr>
      <th><?php echo __('I.G. number');?></th>
      <td><?php  if($obj->getIgRef()) echo $obj->Ig->getIgNum();?></td>
    </tr>
    <tr>
      <th><?php echo __('Return date');?></th>
      <td><?php $date = new DateTime($obj->getToDate());
                echo $date->format('d/m/Y'); ?></td>
    </tr>
  </tbody>
</table>

<script  type="text/javascript">
$(document).ready(function () {
  $('.extd_info').mouseover(function(event){
      $(this).qtip({
        show: {
          ready: true,
          delay: 0,
          event: event.type,
          solo: true,
        },
        //hide: { event: 'mouseout' },
        style: {
          tip: true, // Give it a speech bubble tip with automatic corner detection
          name: 'cream'
        },
        content: {
          text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
          title: { text: '<?php echo __("Linked Info") ; ?>' },
          ajax: {
            url: '<?php echo url_for("loan/getPartInfo");?>',
            type: 'GET',
            data: { id:   $(this).attr('data_id') }
          }
        },
        events: {
          hide: function(event, api) {
            api.destroy();
          }
        }
      });
    });
  });
</script>
