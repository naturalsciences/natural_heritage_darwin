<!--ftheeten 2019 01 28-->
	<td class="col_col_peoples">
		<?php $cpt = 0 ; foreach(Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated("specimens", array('collector'),$specimen->getId() ) as $key=>$people):?>
			<li>
			<?php echo Doctrine_Core::getTable('People')->findOneById($people->getPeopleRef())->getFormatedName() ; ?>
			</li>
		<?php endforeach; ?>		
	</td>
	<td class="col_ident_peoples">
		 <?php foreach(Doctrine_Core::getTable('Identifications')->getIdentificationsRelated("specimens", $specimen->getId() ) as $keyIdent=>$ident):?>
			<?php $cpt = 0 ; foreach(Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated("identifications", array('identifier'), $ident->getId()) as $key=>$people):?>
				<li>
					<?php echo Doctrine_Core::getTable('People')->findOneById($people->getPeopleRef())->getFormatedName() ; ?>
				</li>
			 <?php endforeach?>
		 <?php endforeach?>
	</td>
	<td class="col_don_peoples">
		<?php $cpt = 0 ; foreach(Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated("specimens", array('donator'),$specimen->getId() ) as $key=>$people):?>
			<li>
			<?php echo Doctrine_Core::getTable('People')->findOneById($people->getPeopleRef())->getFormatedName() ; ?>
			</li>
		<?php endforeach; ?>		
	</td>    
 <!--ftheeten--->   
<td class="col_part"><?php echo $specimen->getSpecimenPart();?></td>
<td class="col_object_name"><?php echo $specimen->getObjectName();?></td>
<td class="col_part_status"><?php echo $specimen->getSpecimenStatus();?></td> 
<td class="col_comments" value_dw="<?php print($specimen->getId()); ?>" ><?php echo image_tag('loader.gif', Array("class"=>"spinner_comment"));?></td> 
<?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
  <?php $loc_patterns=Array(); ?>
  <td class="col_building"><?php $loc_patterns=add_to_array_not_null($loc_patterns, "building", $specimen->getBuilding()); echo $specimen->getBuilding()==''?'-':link_to($specimen->getBuilding(), url_for("specimensearch/search")."?submit=Search&".generate_search_query($loc_patterns) , array('target' => '_blank')); ?></td> 
  <td class="col_floor"><?php $loc_patterns= add_to_array_not_null($loc_patterns, "floor", $specimen->getFloor()); echo $specimen->getFloor()==''?'-':link_to($specimen->getFloor(), url_for("specimensearch/search")."?submit=Search&".generate_search_query($loc_patterns) , array('target' => '_blank')); ?></td> 
  <td class="col_room"><?php $loc_patterns= add_to_array_not_null($loc_patterns, "room", $specimen->getRoom()); echo $specimen->getRoom()==''?'-':link_to($specimen->getRoom(), url_for("specimensearch/search")."?submit=Search&".generate_search_query($loc_patterns) , array('target' => '_blank')); ?></td> 
  <td class="col_row"><?php $loc_patterns= add_to_array_not_null($loc_patterns, "row", $specimen->getRow()); echo $specimen->getRow()==''?'-':link_to($specimen->getRow(), url_for("specimensearch/search")."?submit=Search&".generate_search_query($loc_patterns) , array('target' => '_blank')); ?></td> 
  <td class="col_col"><?php $loc_patterns= add_to_array_not_null($loc_patterns, "col", $specimen->getCol()); echo $specimen->getCol()==''?'-':link_to($specimen->getCol(), url_for("specimensearch/search")."?submit=Search&".generate_search_query($loc_patterns) , array('target' => '_blank')); ?></td> 
  <td class="col_shelf"><?php $loc_patterns= add_to_array_not_null($loc_patterns, "shelf", $specimen->getShelf()); echo $specimen->getShelf()==''?'-':link_to($specimen->getShelf(), url_for("specimensearch/search")."?submit=Search&".generate_search_query($loc_patterns) , array('target' => '_blank')); ?></td> 
  <td class="col_container"><?php echo $specimen->getContainer();?></td> 
  <td class="col_container_type"><?php echo $specimen->getContainerType();?></td> 
  <td class="col_container_storage"><?php echo $specimen->getContainerStorage();?></td> 
  <td class="col_sub_container"><?php echo $specimen->getSubContainer();?></td> 
  <td class="col_sub_container_type"><?php echo $specimen->getSubContainerType();?></td> 
  <td class="col_sub_container_storage"><?php echo $specimen->getSubContainerStorage();?></td>
<?php endif ; ?>
<td class="col_specimen_count">
  <?php if($specimen->getSpecimenCountMin() != $specimen->getSpecimenCountMax()):?>
    <?php echo $specimen->getSpecimenCountMin() . ' - '.$specimen->getSpecimenCountMax();?>
  <?php else:?>
    <?php echo $specimen->getSpecimenCountMin();?>
  <?php endif;?>
</td>
<td class="col_loans">
  <?php if(isset($loans[$specimen->getId()])):?>
    <?php if ($loans[$specimen->getId()][0]['loans_count'] === 1): ?>
      <?php if ($loans[$specimen->getId()][0]['loans_right'] === 1): ?>
        <a class="ellipsis" href="<?php echo url_for('loan/view?id='.$loans[$specimen->getId()][0]['loans_ref']);?>" target="_blank" title="<?php echo $loans[$specimen->getId()][0]['loans_name'];?>"><?php echo $loans[$specimen->getId()][0]['loans_name'];?></a><span id="loan_status" class="<?php echo $loans[$specimen->getId()][0]['loans_status_class']; ?>" title="<?php echo $loans[$specimen->getId()][0]['loans_status_tooltip']; ?>">&nbsp;<?php echo $loans[$specimen->getId()][0]['loans_status']; ?></span>
      <?php else: ?>
        <span class="loan_naming ellipsis" title="<?php echo $loans[$specimen->getId()][0]['loans_name'];?>"><?php echo $loans[$specimen->getId()][0]['loans_name'];?></span><span id="loan_status" class="<?php echo $loans[$specimen->getId()][0]['loans_status_class']; ?>" title="<?php echo $loans[$specimen->getId()][0]['loans_status_tooltip']; ?>">&nbsp;<?php echo $loans[$specimen->getId()][0]['loans_status']; ?></span>
      <?php endif; ?>
    <?php else:?>
      <span class="counting">
        <?php echo $loans[$specimen->getId()][0]['loans_count'];?>
        <?php echo image_tag('info.png',array('title'=>'info', 'class'=>'info loans_info', 'id'=>"loans_".$specimen->getId()));?>
      </span>
      <div id="loans_<?php echo $specimen->getId();?>_list" class="tree">
        <?php foreach ($loans[$specimen->getId()][0]['specimen_infos'] as $spec_infos):?>
          <?php if($spec_infos['access_right'] === 1): ?>
            <a class="ellipsis" href="<?php echo url_for('loan/view?id='.$spec_infos['id']);?>" target="_blank" title="<?php echo $spec_infos['name'];?>"><?php echo $spec_infos['name'];?></a><span id="loan_status" class="<?php echo $spec_infos['status_class']; ?>" title="<?php echo $spec_infos['status_tooltip']; ?>">&nbsp;<?php echo $spec_infos['status']; ?></span></br>
          <?php else: ?>
            <span class="loan_naming ellipsis"><?php echo $spec_infos['name'];?></span><span id="loan_status" class="<?php echo $spec_infos['status_class']; ?>" title="<?php echo $spec_infos['status_tooltip']; ?>">&nbsp;<?php echo $spec_infos['status']; ?></span></br>
          <?php endif; ?>
        <?php endforeach;?>  
      </div>
    <?php endif;?>
  <?php else: ?>
    <span class="counting">0</span>
  <?php endif;?>
</td>
 <td class="col_mids"><?php echo $specimen->getMidsLevel();?>

