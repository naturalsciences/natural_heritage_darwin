<?php if( count($loans) ) : ?> 
<?php use_helper('Text');?>  
<table class="show_table">
  <thead>
    <tr>
      <th>&nbsp;</th>
      <th class="left_aligned"><?php echo __('Name'); ?></th>
      <th class="left_aligned"><?php echo __('Status'); ?></th>
      <th class="left_aligned"><?php echo __('From'); ?></th>
      <th class="left_aligned"><?php echo __('To'); ?></th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php $max_stringlengte = 15; ?>
    <?php foreach($loans as $loan) : ?>
    <tr>
      <td>
        <?php if(strlen($loan['name']) > $max_stringlengte ):?>
          <?php echo image_tag('info.png', 'class=more_name');?>
        <?php else:?>
          <?php echo image_tag('info-bw.png', 'class=no_more_name');?>
        <?php endif;?>
        <ul class="field_change">
            <?php echo $loan['name'];?>
        </ul>
      </td>
      <td><?php echo truncate_text($loan['name'],$max_stringlengte); ?></td>
      <td><?php echo $status[$loan->getId()]->getFormattedStatus(); ?></td>
      <td><?php echo $loan['from_date_formatted']; ?></td>
      <td class="<?php if($loan->getIsOverdue()) echo 'loan_overdue';?>">
        <?php if($loan->getExtendedToDateFormatted() != ''):?>
          <?php echo $loan->getExtendedToDateFormatted();?>
        <?php else:?>
          <?php echo $loan->getToDateFormatted();?>
        <?php endif;?>
      </td>
      <td><?php echo link_to(image_tag('blue_eyel.png'),'loan/view?id='.$loan->getId(), 'class=view_loan');?></td>
      <?php if( in_array($loan->getId(),sfOutputEscaper::unescape($rights)) || $sf_user->isA(Users::ADMIN) ) :?>
        <td><?php echo link_to(image_tag('edit.png'),'loan/edit?id='.$loan->getId(), 'class=edit_loan');?></td>
      <?php else :?>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <?php endif; ?>
    </tr>
    <?php endforeach ; ?>
  
  </tbody>
  <tfoot>
    <tr><td colspan="8"> <?php if( $myTotalLoans > 5) include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?></td></tr>
    <tr><td colspan="2">&nbsp;</td>
        <td colspan="3">
          <?php echo image_tag('blue_eyel.png');?>&nbsp;
          <?php echo link_to(__('View all loans I\'m implied in'), 'loan/index?loans_filters[status]=opened', 'class=view_all');?></td>
        <td colspan="3">
          <?php if ($sf_user->isAtLeast(Users::ENCODER)):?>
            <?php echo image_tag('add_blue.png');?>&nbsp;
            <?php echo link_to(__('Add'),'loan/new', 'class=add_link');?>
          <?php else:?>
            &nbsp;
          <?php endif; ?>
        </td>
    </tr>
  </tfoot>
</table>

  <script language="javascript" type="text/javascript"> 
    $(document).ready(function()
    {
      $('img.more_name').each(function()
      {
	$(this).qtip(
	{
	  content: $(this).next().html(),
	  delay: 100,
	  show: { solo: true},
	  position: { my : 'bottom right',target: 'mouse'}
	});
      });

      $(function () {
	$('#myLoans .pager_nav a').click(function(event)
	{
	    event.preventDefault();
	    $.ajax({
	      url: $(this).attr('href'),
	      success: function(html) {
		$('#myLoans .widget_content').html(html);
	      }
	    });
	});
      });
    });
  </script>
<?php else :?>
  <?php echo __('Nothing here'); ?>
<?php endif;?>

</form>
