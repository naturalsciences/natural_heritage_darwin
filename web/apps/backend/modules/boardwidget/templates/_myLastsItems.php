<?php slot('widget_title',__('My Lasts Items'));  ?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php if(count($items)):?>
<table class="show_table">
        <thead>
	  <tr>
            <th>
                <?php echo __('Date');?>
            </th>
            <th>
            </th>
            <th class="referenced_relation">
                <?php echo __('Relation');?>
            </th>

            <th>
              <?php echo __('Action');?>
            </th>
          </tr>
        </thead>
    <tbody>
      <?php foreach($items as $item):?>
      <tr>
        <td><?php $date = new DateTime($item['modification_date_time']); echo $date->format('d.m.y (a)'); ?></td>
        <td><?php if($item['action'] != 'delete'):?>
             <?php if($item->getLink() != ''):?>
                <?php echo link_to(image_tag('next.png'), $item->getLink());?>
              <?php endif;?>
            <?php else:?>
              <span class="small_item"><?php echo __('deleted');?></span>
            <?php endif;?>
        </td>
        <td><?php echo $item['referenced_relation'];?></td>
        <td>
          <?php if($item['action'] == 'insert') echo __('inserted');
                elseif($item['action'] == 'update') echo __('updated');
                elseif($item['action'] == 'delete') echo __('deleted');?>
        </td>
    </tr>
            
    <?php endforeach;?>
    </tbody>
  </table>
<br />
  <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>

  <script language="javascript" type="text/javascript"> 
  $(function () {
     $('#myLastsItems').choose_form({});
     $('#myLastsItems .pager_nav a').click(function(event)
     {
	event.preventDefault();
	$.ajax({
	  url: $(this).attr('href'),
          success: function(html) {
	    $('#myLastsItems .widget_content').html(html);
	  }
	});
     });
  });
  </script>
<?php else:?>
  <?php echo __('Nothing here');?>
<?php endif;?>
<br />
