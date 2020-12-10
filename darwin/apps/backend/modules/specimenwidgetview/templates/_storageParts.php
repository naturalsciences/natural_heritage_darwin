<?php use_helper('Text');?>
 <?php $retainedKey = 1;?>
<?php foreach($storageParts as $storagePart):?>
  <fieldset class="opened"><legend style="padding-right: 10px;padding-bottom: 5px;font-weight: bold;"><?php echo __('Storage part');?><?php echo __(' '.$retainedKey);?>
  </legend>
   <?php $retainedKey++;?>
  <fieldset class="opened"><legend><?php echo __('Parts');?>
  </legend>
  <table >
   <tr>
      <td style="padding-right: 10px;padding-bottom: 5px;font-weight: bold;"><?php echo __('Specimen part');?></td>
      <td style="padding-right: 10px;padding-bottom: 5px;">
        <?php echo $storagePart->getSpecimenPart();?>
      </td>
      </tr>   
      <tr>
      <td style="padding-right: 10px;padding-bottom: 5px;font-weight: bold;"><?php echo __('Object name');?></td>
       <td style="padding-right: 10px;padding-bottom: 5px;">
        <?php echo $storagePart->getObjectName();?>
      </td>
      </tr>
      <tr>
      <td style="padding-right: 10px;padding-bottom: 5px;font-weight: bold;"><?php echo __('Category');?></td>
       <td style="padding-right: 10px;padding-bottom: 5px;">
        <?php echo $storagePart->getCategory();?>
      </td>
      </tr>
    </table>
   
  </fieldset>


  <fieldset ><legend><?php echo __('Complete');?>
  </legend>
  <table >
   <tr>
      <td style="padding-right: 10px;padding-bottom: 5px;font-weight: bold;"><?php echo __('Specimen status');?></td>
       <td style="padding-right: 10px;padding-bottom: 5px;">
        <?php echo $storagePart->getSpecimenStatus();?>
      </td>
      </tr>
      <tr>
      <td style="padding-right: 10px;padding-bottom: 5px;font-weight: bold;"><?php echo __('Complete');?></rd>
       <td style="padding-right: 10px;padding-bottom: 5px;">
        <?php echo $storagePart->getcomplete();?>
      </td>
      </tr>
     </table>
   
  </fieldset>
  
    <fieldset ><legend><?php echo __('Localisation');?>
  </legend>
    <table class="catalogue_table_view">

      <tr>
      <th class="top_aligned"><?php echo __("Institution");?></th>
      <td><?php echo $storagePart->getInstitutionRef()==''?'-':$storagePart->getInstitution()->getFormatedName() ?></td>
      </tr>
      <tr>
        <th class="top_aligned"><?php echo __("Building");?></th>
        <td><?php echo $storagePart->getBuilding()==''?'-':$storagePart->getBuilding() ?></td>
      </tr>
      <tr>
        <th class="top_aligned"><?php echo __("Floor");?></th>
        <td><?php echo $storagePart->getFloor()==''?'-':$storagePart->getFloor() ?></td>
      </tr>
      <tr>
        <th class="top_aligned"><?php echo __("Room");?></th>
        <td><?php echo $storagePart->getRoom()==''?'-':$storagePart->getRoom() ?></td>
      </tr>
      <tr>
        <th class="top_aligned"><?php echo __("Row");?></th>
        <td><?php echo $storagePart->getRow()==''?'-':$storagePart->getRow() ?></td>
      </tr>
      <tr>
      <th class="top_aligned"><?php echo __("Column");?></th>
      <td><?php echo $storagePart->getCol()==''?'-':$storagePart->getCol() ?></td>
      </tr>
      <tr>
        <th class="top_aligned"><?php echo __("Shelf");?></th>
        <td><?php echo $storagePart->getShelf()==''?'-':$storagePart->getShelf() ?></td>
      </tr>
    </table>
       
  </fieldset>
  
   <fieldset ><legend><?php echo __('Container');?>
  </legend>
    <table class="catalogue_table_view">
  <tr>
	<th><?php echo __("supernumerary ?");?></th>
	<td><?php echo ($storagePart->getSurnumerary()?__('Yes'):__('No')); ?></td>
  </tr>

  <tr>
	<th><?php echo __("Container");?></th>
	<td><?php echo $storagePart->getContainer()==''?'-':$storagePart->getContainer() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Container type");?></th>
	<td><?php echo $storagePart->getContainerType()==''?'-':$storagePart->getContainerType() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Container storage");?></th>
	<td><?php echo $storagePart->getContainerStorage()==''?'-':$storagePart->getContainerStorage() ?></td>
  </tr>
  <tr>
	<th><?php echo __("Sub container");?></th>
	<td><?php echo $storagePart->getSubContainer()==''?'-':$storagePart->getSubContainer() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Sub Container Type");?></th>
	<td><?php echo $storagePart->getSubContainerType()==''?'-':$storagePart->getSubContainerType() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo __("Sub container storage");?></th>
	<td><?php echo $storagePart->getSubContainerStorage()==''?'-':$storagePart->getSubContainerStorage() ?></td>
  </tr>
</table>

  </fieldset>
  </fieldset>
<?php endforeach ; ?>    
