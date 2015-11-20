<?php slot('title', __('Loan Overview'));  ?>
<div class="page">
    <h1 class="view_mode"><?php echo __('Overview');?></h1>

    <?php include_partial('tabs', array('loan'=> $loan, 'items'=>array(),'view'=>true)); ?>
    <div class="tab_content panel_view">
        <table class="catalogue_table_view">
        <thead>
          <tr>
            <th><?php echo __('Specimen') ;?></th>
            <th><?php echo __('Specimen Main code(s)') ;?></th>
            <th><?php echo __('I.g. Num');?></th>
            <th><?php echo __('Details') ;?></th>
            <th><?php echo __('Expedition') ;?></th>
            <th><?php echo __('Return') ;?></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($items as $item):?>
            <tr>
              <td><?php if($item->getSpecimenRef()):?>
                <?php echo image_tag('info.png',"title=info class=extd_info data_id=".$item->getSpecimenRef());?>
                <?php echo link_to('#' . $item->getSpecimenRef(), 'specimen/view?id='. $item->getSpecimenRef());?>
              <?php endif;?></td>
              <td>
                <?php echo include_component('specimenwidgetview', 'refMainCodes', array('eid'=>$item->getSpecimenRef()));?>
              </td>
              <td><?php
                $ig_num = $item->Ig->getIgNum();
                if ( !empty($ig_num) ) {
                  echo link_to($ig_num, 'igs/view?id=' . $ig_num);
                }
                ?>
              </td>
              <td><?php echo $item->getDetails();?></td>
              <td> <?php $date = new DateTime($item->getFromDate());
                echo $date->format('d/m/Y'); ?></td>
              <td> <?php $date = new DateTime($item->getToDate());
                echo $date->format('d/m/Y'); ?></td>
              <td><?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),'loanitem/view?id='.$item->getId());?></td>
            </tr>
          <?php endforeach;?>
        </tbody>
       </table>
       <?php if( (! isset($item) ) || isset($item) && count($item) == 0):?>
        <div class="warn_message"><?php echo __('There is currently no items in the loan.');?></div>
       <?php endif;?>
  <br />
      <p class="clear"></p>
      <p align="right">
        &nbsp;<a class="bt_close" href="<?php echo url_for('loan/edit?id='.$loan->getId()) ?>" id="spec_cancel"><?php echo __('Back to Loan');?></a>
      </p>

    </div>

</div>
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
