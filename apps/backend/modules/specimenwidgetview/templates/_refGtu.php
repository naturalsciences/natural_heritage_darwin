<table class="catalogue_table_view">
  <tbody>
    <tr>
      <th>
        <?php echo __('Station visible ?') ?>
      </th>
      <td>
        <?php echo $spec->getStationVisible()?__("Yes"):__("No") ; ?>
      </td>
    </tr>
    <?php if(isset($gtu) && ($spec->getStationVisible() || (!$spec->getStationVisible() && $sf_user->isAtLeast(Users::ENCODER)))) : ?>
    <tr>
      <th><label><?php echo __('Sampling location code');?></label></th>
      <td>
        <?php echo link_to($gtu->getCode(), 'gtu/view?id='.$spec->getGtuRef()) ?>
      </td>
    </tr>
    <?php if($gtu->getLocation()):?>
    <tr>
      <th><label><?php echo __('Latitude');?></label></th>
      <td id="specimen_gtu_ref_lat"><?php echo $gtu->getLatitude() ; ?></td>
    </tr>
    <tr>
      <th><label><?php echo __('Longitude');?></label></th>
      <td id="specimen_gtu_ref_lon"><?php echo $gtu->getLongitude(); ?></td>
    </tr>
    <?php endif;?>
    <?php if($gtu->getElevation()):?>
    <tr>
      <th><label><?php echo __('Altitude');?></label></th>
      <td id="specimen_gtu_ref_elevation"><?php echo $gtu->getElevation().' +- '.$gtu->getElevationAccuracy().' m'; ?></td>
    </tr>
    <?php endif;?>
    <tr>
      <th><label><?php echo __('Date from');?></label></th>
      <td id="specimen_gtu_date_from" class="datesNum"><?php echo $gtu->getGtuFromDateMasked(ESC_RAW);?></td>
    </tr>
    <tr>
      <th><label><?php echo __('Date to');?></label></th>
      <td id="specimen_gtu_date_to" class="datesNum"><?php echo $gtu->getGtuToDateMasked(ESC_RAW);?></td>
    </tr>
    <tr>
      <th class="top_aligned">
        <?php echo __("Sampling location Tags") ?>
      </th>
      <td>
        <div class="inline">
          <?php echo $gtu->getName(ESC_RAW); ?>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2" id="specimen_gtu_ref_map">
        <?php echo $gtu->getMap(ESC_RAW);?>
      </td>
    </tr>
    <?php if (
      isset($commentsGtu) &&
      count($commentsGtu) != 0
      ): ?>
	  <tr id="specimen_gtu_related_info">
      <th>
        <?php echo __("Related comments") ?>
      </th>
      <td class="top_aligned">
        <?php use_helper('Text');?>
        <?php foreach($commentsGtu as $comment):?>
          <fieldset class="opened view_mode"><legend class="view_mode"><b><?php echo __('Notion');?></b> : <?php echo __($comment->getNotionText());?></legend>
            <?php echo auto_link_text( nl2br($comment->getComment())) ;?>
          </fieldset>
        <?php endforeach ; ?>
      </td>
    </tr>
    <?php endif; ?>
    <?php elseif(isset($gtu) && $gtu->hasCountries()):?>
    <tr>
      <th class="top_aligned">
        <?php echo __("Sampling location countries") ?>
      </th>
      <td>
        <div class="inline">
          <?php echo $gtu->getRawValue()->getName(null, true); ?>
        </div>
      </td>
    </tr>
    <?php endif ; ?>
  </tbody>
</table>
