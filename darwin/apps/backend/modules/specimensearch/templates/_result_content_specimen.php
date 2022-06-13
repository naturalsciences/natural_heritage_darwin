    <?php $action = $sf_user->isAtLeast(Users::ENCODER)?'edit':'view' ; ?>
    <td class="col_category">
      <?php if($specimen->getCategory() == 'physical' || $specimen->getCategory() == 'mixed' ):?>
        <?php echo image_tag('sp_in.png', array('alt' => __('Physical'), 'title'=> __('Physical')));?>
      <?php endif;?>
      <?php if($specimen->getCategory() == 'mixed' ):?>
        <?php echo __('+');?>
      <?php endif;?>
      <?php if($specimen->getCategory() == 'observation'  || $specimen->getCategory() == 'mixed' ):?>
        <?php echo image_tag('blue_eyel.png', array('alt' => __('Other'), 'title'=> __('Other')));?>
      <?php endif;?>
    </td>
    <td  class="col_collection">
      <?php if($specimen->getCollectionRef() > 0) : ?>
        <?php echo image_tag('info.png',"title=info class=info id=collection_".$specimen->getId()."_info");?>
        <?php if($sf_user->isAtLeast(Users::ADMIN) || ($sf_user->isAtLeast(Users::MANAGER) && $specimen->getHasEncodingRights())) : ?>           
          <a href="<?php echo url_for('collection/edit?id='.$specimen->getCollectionRef());?>"><?php echo trim($specimen->getCollectionNameFullPath(), "/");?></a>
        <?php else : ?>
         <?php echo trim($specimen->getCollectionNameFullPath(), "/");?></a>
        <?php endif ; ?>
        <div id="collection_<?php echo $specimen->getId();?>_tree" class="tree"></div>
        <script type="text/javascript">
            $('#collection_<?php echo $specimen->getId();?>_info').click(function() 
            {
              item_row=$(this).closest('tr');
              if(item_row.find('#collection_<?php echo $specimen->getId();?>_tree').is(":hidden"))
              {
                $.get('<?php echo url_for("catalogue/tree?table=collections&id=".$specimen->getCollectionRef()) ;?>',function (html){
                  item_row.find('#collection_<?php echo $specimen->getId();?>_tree').html(html).slideDown();
                  });
              }
              $('#collection_<?php echo $specimen->getId();?>_tree').slideUp();
            });
        </script>
      <?php endif ; ?>
    </td>
    <td class="col_codes">
      <?php if(isset($codes[$specimen->getId()])):?>
        <?php if(count($codes[$specimen->getId()]) <= 3):?>
          <?php echo image_tag('info-bw.png',"title=info class=info");?>
        <?php else:?>
          <?php echo image_tag('info.png',"title=info class=info id=spec_code_".$specimen->getId()."_info");?>
          <script type="text/javascript">
            $(document).ready(function () {
              $('#spec_code_<?php echo $specimen->getId();?>_info').click(function() 
              {
                item_row=$(this).closest('td');
                if(item_row.find('li.code_supp:hidden').length)
                {
                  item_row.find('li.code_supp').removeClass('hidden');
                }
                else
                {
                  item_row.find('li.code_supp').addClass('hidden');
                }
              });
            });
          </script>
        <?php endif;?>
        <ul>
            <?php $cpt = 0 ; foreach($codes[$specimen->getId()] as $key=>$code):?>            
                <?php if($code->getCodeCategory() == 'main') : ?>
                  <?php $cpt++ ; ?>
                  <li <?php if($cpt > 3) echo("class='hidden code_supp'"); ?>>
                  <!--rmca 2017 12 13-->
                   <strong>
                        <?php echo link_to( $code->getFullCode(), 'specimen/view?id='.$specimen->getId(), array('target' => '_blank'));?>
                    </strong>
                  </li> 			  
              <?php elseif ($sf_user->isAtLeast(Users::ENCODER)) : ?>
                          
                <li class="hidden code_supp" >
                    <?php if ($code->getCodeCategory() == 'main') echo "<strong>" ; ?>            
                    <?php echo $code->getFullCode(); ?>
                    <?php if ($code->getCodeCategory() == 'main') echo "</strong>" ; ?>
                </li>         
              <?php endif ; ?>
            <?php endforeach; ?>
        </ul>
      <?php endif;?>
    </td>
	<td class="col_import_ref">
		<?php print($specimen->getImportRef());?>
    </td>
	<td class="col_uuid">
		<?php print($specimen->getUuid());?>
	</td>
    <td class="col_taxon">
      <?php if($specimen->getTaxonRef() > 0) : ?>
        <?php echo image_tag('info.png',"title=info class=info id=taxon_".$specimen->getId()."_info");?>
        <a target="_blank" href="<?php echo url_for('taxonomy/view?id='.$specimen->getTaxonRef());?>"><?php echo $specimen->getTaxonName();?></a>
		<div style="font-style:italic"><?php print($specimen->getTaxonStatus());?></div>
		<!--JMHerpers 2019 05 29-->
		<!-- ?php if($specimen->getCites() == TRUE ) : ?> 
			&nbsp;<B><font size="4" color="red">(CITES!!)</font></B>
		 < ?php endif ; ? -->
	   <div id="taxon_<?php echo $specimen->getId();?>_tree" class="tree"></div>
        <script type="text/javascript">
            $('#taxon_<?php echo $specimen->getId();?>_info').click(function() 
            {
              item_row=$(this).closest('tr');
              if(item_row.find('#taxon_<?php echo $specimen->getId();?>_tree').is(":hidden"))
              {
                $.get('<?php echo url_for("catalogue/tree?table=taxonomy&id=".$specimen->getTaxonRef()) ;?>',function (html){
                  item_row.find('#taxon_<?php echo $specimen->getId();?>_tree').html(html).slideDown();
                  });
              }
              $('#taxon_<?php echo $specimen->getId();?>_tree').slideUp();
            });
        </script>
      <?php endif ; ?>&nbsp;
    </td>
    <td class="col_gtu">
      <?php if($specimen->getGtuRef() > 0) : ?>
        <?php if($specimen->getHasEncodingRights() || $specimen->getStationVisible() || $sf_user->isAtLeast(Users::ADMIN) ):?>
          <?php echo image_tag('info.png',"title=info class=info id=gtu_ctr_".$specimen->getId()."_info");?>
          <script type="text/javascript">
            $(document).ready(function()
            {
              $('#gtu_ctr_<?php echo $specimen->getId(); ?>_info').click(function() 
              {
                item_row = $(this).closest('tr');
                elem = item_row.find('#gtu_<?php echo $specimen->getId();?>_details');
                if(elem.is(":hidden"))
                { 
                  $.get('<?php echo url_for("gtu/completeTag?id=".$specimen->getId()."&view=true") ;?>',function (html){
                    item_row.find('.general_gtu').slideUp();
                    elem.html(html).slideDown();
                  });
                }
                else
                {
                  elem.slideUp();
                  item_row.find('.general_gtu').slideDown();
                }
              });
            });
          </script>
          <?php if ($action == 'edit') : ?>
            <a href="<?php echo url_for('gtu/'.$action.'?id='.$specimen->getGtuRef(), array("target"=>"_blank")) ;?>"><?php echo $specimen->getGtuCode();?></a>
          <?php else : ?>
            <?php echo $specimen->getGtuCode();?>
          <?php endif ; ?>
        <?php else:?>
          <?php echo image_tag('info-bw.png',"title=info class=info id=gtu_ctr_".$specimen->getId()."_info");?>
        <?php endif;?>

          <div class="general_gtu">
		   <?php if($specimen->getGtuCountryTagValue() != ""): ?>
            <strong><?php 
            //ftheeten 2018 10 04
            echo __('Country');?> :</strong>
           <?php echo $specimen->getGtuCountryTagValue(ESC_RAW);?>
		   </br>
          <?php endif ; ?>
          <?php if($specimen->getAllGtuTags() != ""): ?>
            <strong><?php 
            //ftheeten 2018 10 04
            echo __('Localities');?> :</strong>
           <?php echo $specimen->getAllGtuTags(ESC_RAW);?>
		   </br>
		   <?php endif ; ?>
		   <?php if($specimen->getGtuLocation() != ""): ?>
		   </br>
            <strong><?php 
            //ftheeten 2018 10 04
            echo __('Coordinates');?> :</strong>
           <?php echo "<br/>Long :". str_replace(",", " Lat. :",preg_replace('(\(|\))','',(string)$specimen->getGtuLocation()));?>
		   </br>
          <?php endif ; ?>
          </div>
          <div id="gtu_<?php echo $specimen->getId();?>_details" style="display:none;"></div>

      <?php endif ; ?>
    </td> 
    <!--ftheeten 2018 11 30-->
    <td class="col_collecting_dates">
			<!--jmherpers 2018 01 29-->
			<?php if(null !==$specimen->getTemporalInformation()->getFromDateMasked() && $specimen->getTemporalInformation()->getFromDateMask()):?>
				<?php if($specimen->getTemporalInformation()->getFromDateMask() >= 56):?>
					<b>From: </b><?php echo $specimen->getTemporalInformation()->getFromDateMasked(ESC_RAW);?>
					<?php elseif($specimen->getTemporalInformation()->getFromDateMask() == 48):?>
						<b>From: </b><?php echo substr($specimen->getTemporalInformation()->getFromDateMasked(ESC_RAW),12,10);?>
						<?php elseif($specimen->getTemporalInformation()->getFromDateMask() == 32):?>
							<b>From: </b><?php echo substr($specimen->getTemporalInformation()->getFromDateMasked(ESC_RAW),15,8);?>
				<?php endif ; ?>
            <?php endif ; ?>
            <?php if(null !==$specimen->getTemporalInformation()->getToDateMasked() && $specimen->getTemporalInformation()->getToDateMask()):?>
				<br/>
				<?php if($specimen->getTemporalInformation()->getToDateMask() >= 56):?>
					<b>To: </b><?php echo $specimen->getTemporalInformation()->getToDateMasked(ESC_RAW);;?>
					<?php elseif($specimen->getTemporalInformation()->getToDateMask() == 48):?>
						<b>To: </b><?php echo substr($specimen->getTemporalInformation()->getToDateMasked(ESC_RAW),12,10);?>
						<?php elseif($specimen->getTemporalInformation()->getToDateMask() == 32):?>
							<b>To: </b><?php echo substr($specimen->getTemporalInformation()->getToDateMasked(ESC_RAW),15,8);?>
				<?php endif ; ?>
            <?php endif ; ?>
			<!--end jmherpers 2018 01 29-->
    </td>

    <td  class="col_chrono">
      <?php if($specimen->getChronoRef() > 0) : ?>
        <?php echo image_tag('info.png',"title=info class=info id=chrono_".$specimen->getId()."_info");?>
        <a href="<?php echo url_for('chronostratigraphy/'.$action.'?id='.$specimen->getChronoRef());?>"><?php echo $specimen->getChronoName();?></a>
        <div id="chrono_<?php echo $specimen->getId();?>_tree" class="tree"></div>
        <script type="text/javascript">
    
            $('#chrono_<?php echo $specimen->getId();?>_info').click(function() 
            {
              item_row=$(this).closest('tr');
              if(item_row.find('#chrono_<?php echo $specimen->getId();?>_tree').is(":hidden"))
              {
                $.get('<?php echo url_for("catalogue/tree?table=chronostratigraphy&id=".$specimen->getChronoRef()) ;?>',function (html){
                  item_row.find('#chrono_<?php echo $specimen->getId();?>_tree').html(html).slideDown();
                  });
              }
              $('#chrono_<?php echo $specimen->getId();?>_tree').slideUp();
            });
        </script>
      <?php endif ; ?>
    </td>    
    <td  class="col_ig">
      <?php if($specimen->getIgRef() > 0) : ?>       
          <a href="<?php echo url_for('igs/'.$action.'?id='.$specimen->getIgRef());?>"><?php echo $specimen->getIgNum();?></a>
      <?php endif ;?>
    </td>    
    <td  class="col_litho">
      <?php if($specimen->getLithoRef() > 0) : ?>
        <?php echo image_tag('info.png',"title=info class=info id=litho_".$specimen->getId()."_info");?>
        <a href="<?php echo url_for('lithostratigraphy/'.$action.'?id='.$specimen->getLithoRef());?>"><?php echo $specimen->getLithoName();?></a>
        <div id="litho_<?php echo $specimen->getId();?>_tree" class="tree"></div>
        <script type="text/javascript">
            $('#litho_<?php echo $specimen->getId();?>_info').click(function() 
            {
              item_row=$(this).closest('tr');
              if(item_row.find('#litho_<?php echo $specimen->getId();?>_tree').is(":hidden"))
              {
                $.get('<?php echo url_for("catalogue/tree?table=lithostratigraphy&id=".$specimen->getLithoRef()) ;?>',function (html){
                  item_row.find('#litho_<?php echo $specimen->getId();?>_tree').html(html).slideDown();
                  });
              }
              $('#litho_<?php echo $specimen->getId();?>_tree').slideUp();
            });
        </script> 
      <?php endif ; ?>
    </td> 
    <td class="col_lithologic">
      <?php if($specimen->getLithologyRef() > 0) : ?>
        <?php echo image_tag('info.png',"title=info class=info id=lithologic_".$specimen->getId()."_info");?>
        <a href="<?php echo url_for('lithology/'.$action.'?id='.$specimen->getLithologyRef());?>"><?php echo $specimen->getLithologyName();?></a>
        <div id="lithologic_<?php echo $specimen->getId();?>_tree" class="tree"></div>
        <script type="text/javascript">
            $('#lithologic_<?php echo $specimen->getId();?>_info').click(function() 
            {
              item_row=$(this).closest('tr');
              if(item_row.find('#lithologic_<?php echo $specimen->getId();?>_tree').is(":hidden"))
              {
                $.get('<?php echo url_for("catalogue/tree?table=lithology&id=".$specimen->getLithologyRef()) ;?>',function (html){
                  item_row.find('#lithologic_<?php echo $specimen->getId();?>_tree').html(html).slideDown();
                  });
              }
              $('#lithologic_<?php echo $specimen->getId();?>_tree').slideUp();
            });
        </script> 
      <?php endif ; ?>
    </td>
    <td class="col_mineral">
      <?php if($specimen->getMineralRef() > 0) : ?>
        <?php echo image_tag('info.png',"title=info class=info id=mineral_".$specimen->getId()."_info");?>                
        <a href="<?php echo url_for('mineralogy/'.$action.'?id='.$specimen->getMineralRef());?>"><?php echo $specimen->getMineralName();?></a>
        <div id="mineral_<?php echo $specimen->getId();?>_tree" class="tree"></div>
        <script type="text/javascript">
            $('#mineral_<?php echo $specimen->getId();?>_info').click(function() 
            {
              item_row=$(this).closest('tr');
              if(item_row.find('#mineral_<?php echo $specimen->getId();?>_tree').is(":hidden"))
              {
                $.get('<?php echo url_for("catalogue/tree?table=mineralogy&id=".$specimen->getMineralRef()) ;?>',function (html){
                  item_row.find('#mineral_<?php echo $specimen->getId();?>_tree').html(html).slideDown();
                  });
              }
              $('#mineral_<?php echo $specimen->getId();?>_tree').slideUp();
            });
        </script> 
      <?php endif ; ?>
    </td>
    <td class="col_expedition">
      <?php if($specimen->getExpeditionRef() > 0) : ?>
        <a href="<?php echo url_for('expedition/'.$action.'?id='.$specimen->getExpeditionRef());?>"><?php echo $specimen->getExpeditionName();?></a>
      <?php endif ; ?>
    </td>
    <td class="col_acquisition_category">
        <?php echo $specimen->getAcquisitionCategory();?>
    </td>

