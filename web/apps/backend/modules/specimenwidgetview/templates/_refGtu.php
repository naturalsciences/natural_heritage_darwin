<script language="JavaScript" type="text/javascript" src="<?php print(public_path('/openlayers/v4.x.x-dist/ol.js'));?>"></script>
<link rel="stylesheet" href="<?php print(public_path('/openlayers/v4.x.x-dist/ol.css'));?>">
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
      <td id="specimen_gtu_ref_code">
        <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
          <?php echo link_to($gtu->getCode(), 'gtu/view?id='.$spec->getGtuRef()) ?>
        <?php else:?>
          <?php echo $gtu->getCode();?>
        <?php endif;?>
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
        <?php echo $gtu->getMapOpenLayers3(ESC_RAW);?>
      </td>
    </tr>
	<!--addition ftheeten 2014-->
	 <tr>
        <th class="top_aligned">
          <?php echo __("Other information") ?>
        </th>
        <td>
			 <div class="inline">
				<?php 
					$tmpComments = Doctrine::getTable('Comments')->findForTable('gtu',$gtu->getId());
					
						$flagGo=TRUE;
						
						
						$nbr = count($tmpComments);
						if(! $nbr) 
						{
							echo "-";
							$flagGo=True;
						}
						if($flagGo===TRUE)
						{
							$str = '<ul  class="search_tags">';
								foreach($tmpComments as $valC)
								{
								 
									$str .= '<li><label>Comment<span class="gtu_group"> - '.$valC->getNotionConcerned().'</span></label><ul class="name_tags'.($view!=null?"_view":"").'">';
									$str .=  '<li>' . trim($valC->getComment()).'</li>';
									$str .= '</ul><div class="clear"></div>';
									
								  
								}
								$str .= '</ul>';
							echo $str;
						}
				?>
			</div>
		</td>
      </tr>	  
	<!--end addition ftheeten 2014-->
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
