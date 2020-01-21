<?php slot('title', __('View Specimens') .  ( $specimen->getTaxonRef()  ? " : ".$specimen->getTaxonName() : ""));  ?>

<div class="page viewer">
  <?php
	$ig=$specimen->getIgNum(); $spec_num=$specimen->getMainCode();
  ?>
  <?php if(strlen($specimen->getIgNum()??'')>0||strlen($specimen->getIgNum()??'')>0):?>
	<h1><?php echo __("I.G. Number : ");?><?php echo (strlen($specimen->getIgNum()??'')>0 ?  $specimen->getIgNum():'No I.G. Num' );?><br/><?php echo __("Number in Collection : ");?><?php echo (strlen($specimen->getIgNum()??'')>0 ?  $specimen->getMainCode():'No main code' );?> </h1>
  <?php endif;?>
  <h2 class="title"><?php echo __("Collection") ?></h2>
    <div class="borded right_padded">
      <table>
        <tbody>
          <tr>
            <td class="line">			
                <span class="pager_nav"><?php echo __("Name") ; ?>: </span><span><?php echo $specimen->getCollectionName() ; ?></span>
                <?php echo image_tag('info.png',"title=info class=info id=collection_info");?>
              <div id="collection_tree" class="tree"></div>
                <script type="text/javascript">
                   $('#collection_info').click(function()
                   {
                     if($('#collection_tree').is(":hidden"))
                     {
                       $.get('<?php echo url_for("search/tree?table=collections&id=".$specimen->getCollectionRef()) ;?>',function (html){
                         $('#collection_tree').html(html).slideDown();
                         });
                     }
                     $('#collection_tree').slideUp();
                   });
                </script>
              </div>
              <div class="line">
                <span class="pager_nav"><?php echo __("Institution") ; ?>: </span><span><?php echo $institute->getFamilyName() ; ?></span>
                <?php if ($institute->getAdditionalNames()) : ?>
                  (<span><?php echo $institute->getAdditionalNames() ; ?></span>)
                <?php endif ; ?>
              </div>
            </td>
            <td>
              <div class="tree_view">
                <div class="line">
                  <span class="pager_nav"><?php echo __("Conservator") ; ?>: </span><span><?php echo $col_manager->getFormatedName() ; ?></span>
                </div>
                <?php foreach($manager as $info) : ?>
                  <?php if($img = $info->getDisplayImage(1)) : ?>
                    <span class="line">
                      <?php echo image_tag($img,"title=info class=info");?> :
                      <span class="pager_nav"><?php echo $info->getEntry() ; ?></span>
                    </span>
                 <?php endif ; ?>
                <?php endforeach ; ?>
                <?php if(isset($col_staff) && $col_staff):?>
                  <div class="line">
                    <span class="pager_nav"><?php echo __("Staff Member") ; ?>: </span><span><?php echo $col_staff->getFormatedName() ; ?></span>
                  </div>
                <?php endif;?>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <?php if(count($common_names)) : ?>
    <h2 class="title"><?php echo __("Common Names") ?></h2>
    <div class="borded right_padded">
      <table class="classification">
        <thead>
          <tr>
            <th><?php echo __("Classification") ; ?></th>
            <th><?php echo __("Community/language") ; ?></th>
            <th><?php echo __("Names") ; ?></th>
          </tr>
        </thead>
        <tbody>
          <?php include_partial('classification',array('common_name' => $common_names->getRawValue(), 'spec' => $specimen)) ; ?>
        </tbody>
      </table>
    </div>
    <?php endif ; ?>
    <?php if($specimen->getTaxonRef() || $specimen->getChronoRef() || $specimen->getLithoRef() || $specimen->getMineralRef() || $specimen->getLithologyRef()):?>
    <h2 class="title"><?php echo __("Classifications") ?></h2>
    <div class="borded right_padded">
      <table>
        <thead>
          <tr>
            <th>&nbsp;</th>
            <th><?php echo __("level") ; ?></th>
          </tr>
        </thead>
        <tbody>
        <?php if($specimen->getTaxonRef()) : ?>
          <tr>
            <td class="line">
              <span class="pager_nav"><?php echo __("Taxonomy") ; ?>: </span><span><?php echo $specimen->getTaxonName() ; ?></span>
              <?php echo image_tag('info.png',"title=info class=info id=taxon_info");?>
            </td>
            <td class="view_level">
              <span><?php echo $specimen->getTaxonLevelName() ; ?></span>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <div id="taxon_tree" class="tree"></div>
              <script type="text/javascript">
                 $('#taxon_info').click(function()
                 {
                   if($('#taxon_tree').is(":hidden"))
                   {
                     $.get('<?php echo url_for("search/tree?table=taxonomy&id=".$specimen->getTaxonRef()) ;?>',function (html){
                       $('#taxon_tree').html(html).slideDown();
                       });
                   }
                   $('#taxon_tree').slideUp();
                 });
              </script>
              </div>
            </td>
            <td></td>
          </tr>
          <tr><td colspan="2">
            <?php include_partial('multimedia_classification', array('files' => $files, 'count' => $taxFilesCount,'type' => 'taxonomy')) ; ?>
          </td></tr>
        <?php endif ; ?>
        <?php if($specimen->getChronoRef()) : ?>
          <tr>
            <td class="line">
              <span class="pager_nav"><?php echo __("Chronostratigraphy") ; ?>: </span><span><?php echo $specimen->getChronoName() ; ?></span>
              <?php echo image_tag('info.png',"title=info class=info id=chrono_info");?>
            </td>
            <td>
              <span><?php echo $specimen->getChronoLevelName() ; ?></span>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <div id="chrono_tree" class="tree"></div>
              <script type="text/javascript">
                 $('#chrono_info').click(function()
                 {
                   if($('#chrono_tree').is(":hidden"))
                   {
                     $.get('<?php echo url_for("search/tree?table=chronostratigraphy&id=".$specimen->getChronoRef()) ;?>',function (html){
                       $('#chrono_tree').html(html).slideDown();
                       });
                   }
                   $('#chrono_tree').slideUp();
                 });
              </script>
              </div>
            </td>
          </tr>
          <tr><td colspan="2">
            <?php include_partial('multimedia_classification', array('files' => $files, 'count' => $chronoFilesCount,'type' => 'chronostratigraphy')) ; ?>
          </td></tr>
        <?php endif ; ?>
        <?php if($specimen->getLithoRef()) : ?>
          <tr>
            <td class="line">
              <span class="pager_nav"><?php echo __("Lithostatigraphy") ; ?>: </span><span><?php echo $specimen->getLithoName() ; ?></span>
              <?php echo image_tag('info.png',"title=info class=info id=litho_info");?>
            </td>
            <td>
              <span><?php echo $specimen->getLithoLevelName() ; ?></span>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <div id="litho_tree" class="tree"></div>
              <script type="text/javascript">
                 $('#litho_info').click(function()
                 {
                   if($('#litho_tree').is(":hidden"))
                   {
                     $.get('<?php echo url_for("search/tree?table=lithostratigraphy&id=".$specimen->getLithoRef()) ;?>',function (html){
                       $('#litho_tree').html(html).slideDown();
                       });
                   }
                   $('#litho_tree').slideUp();
                 });
              </script>
              </div>
            </td>
          </tr>
          <tr><td colspan="2">
            <?php include_partial('multimedia_classification', array('files' => $files, 'count' => $lithoFilesCount,'type' => 'lithostratigraphy')) ; ?>
          </td></tr>
        <?php endif ; ?>
        <?php if($specimen->getLithologyRef()) : ?>
          <tr>
            <td class="line">
              <span class="pager_nav"><?php echo __("Lithology") ; ?>: </span><span><?php echo $specimen->getLithologyName() ; ?></span>
              <?php echo image_tag('info.png',"title=info class=info id=lithology_info");?>
            </td>
            <td>
              <span><?php echo $specimen->getLithologyLevelName() ; ?></span>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <div id="lithology_tree" class="tree"></div>
              <script type="text/javascript">
                 $('#lithology_info').click(function()
                 {
                   if($('#lithology_tree').is(":hidden"))
                   {
                     $.get('<?php echo url_for("search/tree?table=lithology&id=".$specimen->getLithologyRef()) ;?>',function (html){
                       $('#lithology_tree').html(html).slideDown();
                       });
                   }
                   $('#lithology_tree').slideUp();
                 });
              </script>
              </div>
            </td>
          </tr>
          <tr><td colspan="2">
            <?php include_partial('multimedia_classification', array('files' => $files, 'count' => $lithologyFilesCount,'type' => 'lithology')) ; ?>
          </td></tr>
        <?php endif ; ?>
        <?php if($specimen->getMineralRef()) : ?>
          <tr>
            <td class="line">
              <span class="pager_nav"><?php echo __("Mineralogy") ; ?>: </span><span><?php echo $specimen->getMineralName() ; ?></span>
              <?php echo image_tag('info.png',"title=info class=info id=mineral_info");?>
            </td>
            <td>
              <span><?php echo $specimen->getMineralLevelName() ; ?></span>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <div id="mineral_tree" class="tree"></div>
              <script type="text/javascript">
                 $('#mineral_info').click(function()
                 {
                   if($('#mineral_tree').is(":hidden"))
                   {
                     $.get('<?php echo url_for("search/tree?table=mineralogy&id=".$specimen->getMineralRef()) ;?>',function (html){
                       $('#mineral_tree').html(html).slideDown();
                     });
                   }
                   $('#mineral_tree').slideUp();
                 });
              </script>
              </div>
            </td>
          </tr>
          <tr><td colspan="2">
            <?php include_partial('multimedia_classification', array('files' => $files, 'count' => $mineraloFilesCount,'type' => 'mineralogy')) ; ?>
          </td></tr>
        <?php endif ; ?>
        </tbody>
      </table>
    </div>
    <?php endif;?>
    <h2 class="title"><?php echo __("Specimen Characteristics") ?></h2>
    <div class="borded right_padded">
      <table class="caract_table">
        <tr>
          <td><span class="pager_nav"><?php echo __("Individual Count") ; ?> :</span></td>
          <td><span>
            <?php if($specimen->getSpecimenCountMin() == $specimen->getSpecimenCountMax())
                echo ($specimen->getSpecimenCountMin()==""?"-":$specimen->getSpecimenCountMin()) ;
              else
                echo __("Between ".$specimen->getSpecimenCountMin()." and ".$specimen->getSpecimenCountMax()) ;
             ?></span>
          </td>
        </tr>
        <tr>
          <td><span class="pager_nav"><?php echo __("Type") ; ?> :</span></td>
          <td>
            <span><?php echo ($specimen->getTypeSearch()=="undefined"?"-":$specimen->getTypeSearch()) ; ?></span>
          </td>
        </tr>
        <tr>
          <td><span class="pager_nav"><?php echo __("Sex") ; ?> :</span></td>
          <td>
            <span><?php echo ($specimen->getSex()=="undefined"?"-":$specimen->getSex()) ; ?></span>
          </td>
        </tr>
        <tr>
          <td><span class="pager_nav"><?php echo __("Stage") ; ?> :</span></td>
          <td>
            <span><?php echo ($specimen->getStage()=="undefined"?"-":$specimen->getStage()) ; ?></span>
          </td>
        </tr>
        <tr>
          <td><span class="pager_nav"><?php echo __("Country") ; ?> :</span></td>
          <td>
            <?php if($tags) : ?>
            <ul class="name_tags_view">
              <?php foreach($tags as $key=>$tag):?>
                <?php if($tag == "") echo "-" ; ?>
                <li class="tag_size_2"><?php echo $tag ;?></li>
              <?php endforeach;?>
            </ul>
            <?php else : ?>
              <span>-</span>
            <?php endif ; ?>
          </td>
        </tr>
        <tr>
          <td><span class="pager_nav"><?php echo __("Codes") ; ?> :</span></td>
          <td>
            <?php if(count($codes)) : ?>
            <ul class="">
              <?php foreach($codes as $key=>$code):?>
                <li class"><?php echo $code->getCodeFormated() ;?></li>
              <?php endforeach;?>
            </ul>
            <?php else : ?>
              <span>-</span>
            <?php endif ; ?>
          </td>
        <tr>
            <td><span class="pager_nav"><?php echo __("UUID") ; ?> :</span></td>
            <td>
            <span><?php echo (count($specimen->getSpecimensStableIds())>0?($specimen->getSpecimensStableIds())[0]->getUuid():"") ; ?></span>
          </td>
        </tr>
        </tr>
            <?php if(count($properties)) : ?>
        <tr>
          <td colspan="2">

              <h3><?php echo __("Properties") ; ?></h3>
              <table class="catalogue_table_view">
                <thead>
                  <tr>
                    <th><?php echo __('Property type');?></th>
                    <th><?php echo __('Applies To');?></th>
                    <th class="datesNum"><?php echo __('Date From');?></th>
                    <th class="datesNum"><?php echo __('Date To');?></th>
                    <th><?php echo __('Value');?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($properties as $property):?>
                      <tr>
                        <td><?php echo $property->getPropertyType();?></td>
                        <td><?php echo $property->getAppliesTo();?></td>
                        <td class="datesNum"><?php echo $property->getFromDateMasked(ESC_RAW);?></td>
                        <td class="datesNum"><?php echo $property->getToDateMasked(ESC_RAW);?></td>
                        <td>
                          <?php echo $property->getLowerValue();?>
                          <?php if($property->getUpperValue() != ''):?>
                            -> <?php echo $property->getUpperValue();?>
                          <?php endif;?>
                          <?php echo $property->getPropertyUnit();?>

                          <?php if($property->getPropertyAccuracy() != ''):?>
                            ( +- <?php echo $property->getPropertyAccuracy();?> <?php echo $property->getPropertyUnit();?>)
                          <?php endif;?>

                        </td>
                      </tr>
                  <?php endforeach;?>
                </tbody>
              </table>
          </td>
        </tr>        
        <?php endif ; ?>
        <?php if($specimen->getObjectName()!=""):?>
        <tr><td colspan="2"><h3></h3></td></tr>
        <tr>
          <td><span class="pager_nav"><?php echo __("Object name") ; ?> :</span></td>
          <td>
            <span><?php echo $specimen->getObjectName(); ?></span>
          </td>
        </tr>
        <tr>
          <td><span class="pager_nav"><?php echo __("Specimen State") ; ?> :</span></td>
          <td>
            <span><?php echo $specimen->getSpecimenStatus() ; ?></span>
          </td>
        </tr>
        <?php endif;?>
        <tr><td colspan="2">
          <?php include_partial('multimedia_classification', array('files' => $files, 'count' => $specFilesCount,'type' => 'spec')) ; ?>
        </td></tr>
      </table>

    </div>

    <?php if(isset($comments) && count($comments)) : ?>
    <h2 class="title"><?php echo __("Associated comment") ?></h2>
    <div class="borded right_padded">
      <table class="caract_table">
        <?php foreach ($comments as $comment) : ?>
        <tr>
          <td><span class="pager_nav"><?php echo $comment->getNotionText() ; ?> :</span></td>
          <td><?php echo $comment->getComment(); ?></td>
        </tr>
      <?php endforeach ; ?>
      </table>
    </div>
    <?php endif ; ?>

    <h2 class="title"><?php echo __("You think there's a mistake ? please suggest us a correction") ?></h2>
    <div class="suggestion_zone">
      <?php include_partial('suggestion', array('form' => $form,'id'=> $specimen->getId())) ; ?>
    </div>
  <?php if(!$full) : ?>
  <div class="check_right">    
    <input type="button" id="close_butt" value="<?php echo __('Close this record'); ?>">
  </div>
  <?php endif ; ?>  
  <script type="text/javascript">
    $(document).ready(function() {
      $('#close_butt').click(function(){
        window.close() ;
      });

      $('.expand_button').click(function()
      {
        zone = $(this).closest('td').find('.expand_zone');
        if(zone.is(":hidden"))
        {
          $(this).find('img').attr('src', '/images/blue_expand_up.png');
          zone.slideDown();
        }
        else {
          $(this).find('img').attr('src', '/images/blue_expand.png');
          zone.slideUp();
        }
      });
  });
 </script>
</div>
