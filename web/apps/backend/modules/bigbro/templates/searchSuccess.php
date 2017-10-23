<?php if($form->isValid()):?>
  <?php if(isset($items) && $items->count() != 0 && isset($orderBy) && isset($orderDir) && isset($currentPage) && isset($is_choose)):?>
    <?php
      if($orderDir=='asc')
        $orderSign = '<span class="order_sign_down">&nbsp;&#9660;</span>';
      else
        $orderSign = '<span class="order_sign_up">&nbsp;&#9650;</span>';
    ?>
    <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
    <?php include_partial('global/pager_info', array('form' => $form, 'pagerLayout' => $pagerLayout)); ?>
    <div class="results_container">
      <table class="results">
        <thead>
	  <tr>
            <th>
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=modification_date_time'.( ($orderBy=='modification_date_time' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Date');?>
                <?php if($orderBy=='modification_date_time') echo $orderSign ?>
              </a>
            </th>
            <th class="user_ref">
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=user_ref'.( ($orderBy=='user_ref' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('User');?>
                <?php if($orderBy=='user_ref') echo $orderSign ?>
              </a>
            </th>
            <th class="referenced_relation">
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=referenced_relation'.( ($orderBy=='referenced_relation' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Relation');?>
                <?php if($orderBy=='referenced_relation') echo $orderSign ?>
              </a>
            </th>
            <th class="record_id">
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=record_id'.( ($orderBy=='record_id' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Record Id');?>
                <?php if($orderBy=='record_id') echo $orderSign ?>
              </a>
            </th>
            <th class="action">
              <a class="sort" href="<?php echo url_for($s_url.'&orderby=action'.( ($orderBy=='action' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
                <?php echo __('Action');?>
                <?php if($orderBy=='action') echo $orderSign ?>
              </a>
            </th>
	    <th></th>
          </tr>
        </thead>
        <tbody>
	<?php foreach($items as $change):?>
	  <tr>
	    <td><?php $date = new DateTime($change['modification_date_time']);
		echo $date->format('Y/m/d H:i:s'); ?></td>
	    <td>
	      <?php echo link_to($change['Users']['formated_name'], 'user/edit?id='.$change['Users']['id']);?>
	      <?php echo image_tag('next.png','class=search_with_user alt='.$change['Users']['id']);?>
	    </td>
	    <td><?php echo $change['referenced_relation'];?></td>
	    <td>
	      <?php if($change['action'] != 'delete' && $change->getLink() != ''):?>
          <?php echo link_to($change['record_id'], $change->getLink());?>
        <?php else:?>
          <?php echo $change['record_id'];?>
	      <?php endif;?>
	      <?php echo image_tag('next.png','class=search_with_record alt='.$change['record_id'].' ref='.$change['referenced_relation']);?>
	    </td>
	    <td class="trk_action"><?php if($change['action'] == 'insert') echo __('inserted');
                elseif($change['action'] == 'update') echo __('updated');
                elseif($change['action'] == 'delete') echo __('deleted');?>
        </td>
	    <td>
        <?php if($change['action']=='update'):?>
          <?php echo image_tag('info.png', 'class=more_trk');?>
          <ul class="field_change">
          <?php $old_change = $change->getDiffAsArray(true);foreach($change->getDiffAsArray(false) as $field => $value):?>
              <li <?php if($old_change[$field] != $value):?> class="underlined"<?php endif;?>>
                <strong><?php echo $field;?></strong>  
                <?php if($old_change[$field] != $value):?>
                  <em><?php echo $old_change[$field];?></em> -> <?php echo $value;?>
                <?php else:?>
                  <?php echo $value;?>
                <?php endif;?></li>
          <?php endforeach;?>
        </ul>

        <?php else:?>
          <?php echo image_tag('info.png', 'class=more_trk');?>
          <ul class="field_change">
            <?php foreach($change->getDiffAsArray($change['action']=='delete' ? true : false) as $field => $value):?>
              <li><strong><?php echo $field;?></strong> <?php echo $value;?></li>
          <?php endforeach;?>	      
          </ul>
        <?php endif;?>
	    </td>
	  </tr>
	<?php endforeach;?>

        </tbody>
      </table>
    </div>
<script type="text/javascript">
$(document).ready(function()
{
      $('.search_with_user').click(function(){
	  $('#users_tracking_filters_user_ref').val($(this).attr('alt'));
      });

      $('.search_with_record').click(function(){
	  $('#users_tracking_filters_referenced_relation').val($(this).attr('ref'));
	  $('#users_tracking_filters_record_id').val($(this).attr('alt'));
      });

      $('img.more_trk').each(function()
      {
        $(this).qtip(
        {
         content: $(this).next().html(),
         delay: 100,
         show: { solo: true}
        });
      });
});
</script>
    <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
  <?php else:?>
    <?php echo __('Nothing to watch...Check with the KGB ?');?>
  <?php endif;?>
<?php else:?>
  <div class="error">
    <?php echo $form;?>
  </div>
<?php endif;?>
