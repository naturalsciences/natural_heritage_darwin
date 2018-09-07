<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_gtu','eid'=> $form->getObject()->getId(), 'view' => true)); ?>
<?php slot('title', __('View Sampling location'));  ?>
<div class="page">
  <h1><?php echo __('View Sampling location');?></h1>
  <div class="table_view">
    <table class="classifications_edit">
      <tbody>
      <tr>
        <th><?php echo $form['code']->renderLabel().":"; ?></th>
        <td>
          <?php echo $gtu->getCode(); ?>
        </td>
      </tr>
      <?php if($gtu->getLocation()):?>
      <!--ftheeten 2018 08 08-->
      <tr>
        <th><?php echo $form['coordinates_source']->renderLabel().":"; ?>:</th>
        <td><?php echo $gtu->getCoordinatesSource() ; ?></td>
      </tr>
      <tr>
        <th><?php echo $form['latitude']->renderLabel().":"; ?>:</th>
        <td><?php echo $gtu->getLatitude() ; ?></td>
      </tr>
      <tr>
        <th><?php echo $form['longitude']->renderLabel().":"; ?></th>
        <td><?php echo $gtu->getLongitude(); ?></td>
      </tr>
        <?php if($gtu->getCoordinatesSource()=="DMS"): ?>
        <tr>
            <th><?php echo __("DMS Latitude") ?></th>
            <td><?php echo $gtu->getLatitudeDmsDegree(); ?>°<?php echo $gtu->getLatitudeDmsMinutes(); ?>'<?php echo $gtu->getLatitudeDmsSeconds(); ?>" <?php ($gtu->getLatitudeDmsDirection()>0 ? print("N") : print("S") ); ?></td>
        </tr>
        <tr>
            <th><?php echo __("DMS Longitude") ?></th>
            <td><?php echo $gtu->getLongitudeDmsDegree(); ?>°<?php echo $gtu->getLongitudeDmsMinutes(); ?>'<?php echo $gtu->getLongitudeDmsSeconds(); ?>" <?php ($gtu->getLongitudeDmsDirection()>0 ? print("E") : print("W") ); ?></td>
        </tr>
        <?php endif; ?>
        <?php if($gtu->getCoordinatesSource()=="UTM"): ?>
        <tr>
            <th><?php echo __("UTM Northing") ?></th>
            <td><?php echo $gtu->getLatitudeUtm(); ?></td>
         </tr>
         <tr>
            <th><?php echo __("UTM Easting") ?></th>
            <td><?php echo $gtu->getLongitudeUtm(); ?></td>
         </tr>
        <?php endif; ?>
      <?php endif; ?>
      <?php if($gtu->getElevation() !== null && $gtu->getElevation() !== ''):?>
        <tr>
          <th><?php echo $form['elevation']->renderLabel().":";?></th>
          <td><?php echo $gtu->getElevation().' +- '.$gtu->getElevationAccuracy().' m'; ?></td>
        </tr>
      <?php endif;?>
      <tr>
        <th><?php echo $form['gtu_from_date']->renderLabel().":";?></th>
        <td class="datesNum"><?php echo $gtu->getGtuFromDateMasked(ESC_RAW);?></td>
      </tr>
      <tr>
        <th><?php echo $form['gtu_to_date']->renderLabel().":";?></th>
        <td class="datesNum"><?php echo $gtu->getGtuToDateMasked(ESC_RAW);?></td>
      </tr>
      <tr>
        <th class="top_aligned">
          <?php echo __("Sampling location Tags").":" ?>
        </th>
        <td>
          <div class="inline">
            <?php echo $gtu->getName(ESC_RAW); ?>
          </div>
        </td>
      </tr>
      <tr>
        <td id="refGtu" colspan="2">
          <?php echo $gtu->getMap(ESC_RAW);?>
        </td>
      </tr>
      <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
      <tr>
        <td colspan="2"><?php echo image_tag('edit.png');?> <?php echo link_to(__('Edit this location'),'gtu/edit?id='.$gtu->getId());?></td>
      </tr>
      <?php endif;?>
      </tbody>
    </table>
  </div>
  <div class="view_mode">
    <?php include_partial('widgets/screen', array(
      'widgets' => $widgets,
      'category' => 'cataloguewidgetview',
      'columns' => 1,
      'options' => array('eid' => $form->getObject()->getId(), 'table' => 'gtu', 'view' => true)
    )); ?>
  </div>
</div>
