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
        <?php 
              $stringlengte = strlen($loan['name']);
	      if( $stringlengte > $max_stringlengte )
	      {
		$info_image = 'info.png';
                $info_class = 'class=more_name';
              }
              else
              {
                $info_image = 'info-bw.png';
                $info_class = 'class=no_more_name';
              }  
	      echo image_tag($info_image, $info_class); 
	?>
        <ul class="field_change">
            <?php echo $loan['name'];?>     
        </ul>
      </td>
      <td><?php echo truncate_text($loan['name'],$max_stringlengte); ?></td>
      <td><?php echo $status[$loan->getId()]; ?></td>
      <td><?php $fromDate = new DateTime($loan['from_date']); 
		echo $fromDate->format('d/m/Y');
	  ?></td>
      <td><?php $toDate = new DateTime($loan['to_date']);   
		echo $toDate->format('d/m/Y');
	  ?></td>   
      <td><?php echo link_to(image_tag('blue_eyel.png'),url_for(array('module'=> 'loan', 'action' => 'index', 'id' => $loan->getId())), 'class=view_loan');?></td> 
      <?php if( $rights[$loan->getId()] ) :?>
      <td><?php echo link_to(image_tag('edit.png'),url_for(array('module'=> 'loan', 'action' => 'edit', 'id' => $loan->getId())), 'class=edit_loan');?></td>
      <td><?php echo link_to(image_tag('remove.png'),
                             url_for(array('module'=> 'loan', 'action' => 'delete', 'id' => $loan->getId(), 'on_board' => TRUE)), 
                             array('method' => 'delete', 'confirm' => __('Are you sure?'), 'class' => 'delete_loan')
                            );
          ?>
      </td>
      <?php else :?>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <?php endif; ?>
    </tr>
    <?php endforeach ; ?>
  
  </tbody>
  <tfoot>
    <tr><td colspan="8"> <?php if( $myTotalLoans > 5) include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?></td></tr>
    <tr><td colspan='2'>&nbsp;</td>
        <td colspan='3' style='text-align: center;'><?php echo image_tag('blue_eyel.png'); echo "&nbsp;". link_to(__('View all loans I\'m implied in'),url_for(array('module'=> 'loan', 'action' => 'index', 'id' => $sf_user->getId()/*'viewAll'*/)), 'class=view_all');?></td>
        <td colspan='3'><?php if ($sf_user->isAtLeast(Users::ENCODER)) 
                             {
			       echo image_tag('add_blue.png'); 
                               echo "&nbsp;". link_to(__('Add'),url_for(array('module'=> 'loan', 'action' => 'new')), 'class=add_link');
                             }
			     else
                             {
                               echo "&nbsp;";
                             }
                       ?>
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