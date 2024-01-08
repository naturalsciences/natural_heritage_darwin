<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_igs','eid'=> $form->getObject()->getId(), 'view' => true)); ?>
<?php slot('title', __('View I.G.'));  ?>
<div class="page">
    <h1><?php echo __('View I.G.');?></h1>
	
	<?php if(count($no_right_col) == 0 || $sf_user->isA(Users::ADMIN) ):?>
		<div style="margin-bottom:5px;">
		<div style="margin-bottom:5px;  float: left;"><b>Edit: </b><?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))), 'igs/edit?id='.$igs->getId()); ?></div>
		&nbsp;
		<div style="margin-bottom:5px; margin-left:15px; float: left;vertical-align: top;">
			<a target='_blank' href="<?php print(url_for('specimensearch/search')."?specimen_search_filters[ig_num]=".$igs->getIgNum().( isset($is_choose) ? '&is_choose='.$is_choose : ''));?>"><?php print(__("Search specimens"));?></a>
			</div>
		</div>
	<?php else: ?>
		<div style="margin-bottom:5px; float: left;">
			<a target='_blank' href="<?php print(url_for('specimensearch/search')."?specimen_search_filters[ig_num]=".$igs->getIgNum().( isset($is_choose) ? '&is_choose='.$is_choose : ''));?>"><?php print(__("Search specimens"));?></a>
			</div>
	<?php endif ?>
  <div class="table_view">
  <table>
    <tbody>
      <tr>
        <th><?php echo __('I.G. number:');?></th>
        <td>
          <?php echo $igs->getIgNum(); ?>
        </td>
      </tr>
      <tr>
        <th><?php echo __('I.G. creation date:'); ?></th>
        <td>
          <?php echo FuzzyDateTime::getDateTimeStringFromArray($igs->getIgDate()->getRawValue()) ?>
        </td>
      </tr>     
    </tbody>
  </table>
</div>  
 <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'cataloguewidgetview',
	'columns' => 1,
	'options' => array('eid' => $igs->getId(), 'table' => 'igs', 'view' => true)
	)); ?>
</div>
