<table class="table_main_info">
  <tbody>
    <tr>
      <th><?php echo __('Part Id');?></th>
      <td>
        <?php if($loan->getSpecimenRef()):?>
          <?php echo image_tag('info.png',"title=info class=extd_info data_id=".$loan->getSpecimenRef());?>
          <?php echo link_to('#'.$loan->getSpecimenRef(),'specimen/view?id='.$loan->getSpecimenRef());?>
        <?php endif;?>
      </td>
      <th><?php echo __('Details');?></th>
      <td rowspan="3" class="loanitem_details"><?php echo $loan->getDetails();?></td>
    </tr>
    <tr>
      <th><?php echo __('I.G. number');?></th>
      <td><?php echo $loan->Ig->getIgNum();?></td>
    </tr>
    <tr>
      <th><?php echo __('Return date');?></th>
      <td><?php $date = new DateTime($loan->getToDate());
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
