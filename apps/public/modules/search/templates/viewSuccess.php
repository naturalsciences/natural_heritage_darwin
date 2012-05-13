<?php slot('title', __('View Specimens') .  ( $individual->SpecimensFlat->getTaxonRef()  ? " : ".$individual->SpecimensFlat->getTaxonName() : ""));  ?>  

<div class="page viewer">
  <h1><?php echo __("Specimen Record");?></h1>
    <h2 class="title"><?php echo __("Collection") ?></h2>  
    <div class="borded right_padded">
      <table>
        <tbody>
          <tr>
            <td class="line">
                <span class="pager_nav"><?php echo __("Name") ; ?>: </span><span><?php echo $individual->SpecimensFlat->getCollectionName() ; ?></span>
                <?php echo image_tag('info.png',"title=info class=info id=collection_info");?>
              <div id="collection_tree" class="tree"></div>
                <script type="text/javascript">
                   $('#collection_info').click(function() 
                   {
                     if($('#collection_tree').is(":hidden"))
                     {
                       $.get('<?php echo url_for("search/tree?table=collections&id=".$individual->SpecimensFlat->getCollectionRef()) ;?>',function (html){
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
                <span class="line">
                <span class="pager_nav"><?php echo __("Collection manager") ; ?>: </span><span><?php echo $col_manager->getFormatedName() ; ?></span>
                </span>
                <?php foreach($manager as $info) : ?>
                  <?php if($img = $info->getDisplayImage(1)) : ?>
                    <span class="line">
                      <?php echo image_tag($img,"title=info class=info");?> :
                      <span class="pager_nav"><?php echo $info->getEntry() ; ?></span>
                    </span>
                 <?php endif ; ?>
                <?php endforeach ; ?>
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
          <?php include_partial('classification',array('common_name' => $common_names->getRawValue(), 'spec' => $individual->SpecimensFlat)) ; ?>
        </tbody>
      </table>
    </div>
    <?php endif ; ?>
    <?php if($individual->SpecimensFlat->getTaxonRef() || $individual->SpecimensFlat->getChronoRef() || $individual->SpecimensFlat->getLithoRef() || $individual->SpecimensFlat->getMineralRef() || $individual->SpecimensFlat->getLithologyRef()):?>
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
        <?php if($individual->SpecimensFlat->getTaxonRef()) : ?>
          <tr>
            <td class="line">
              <span class="pager_nav"><?php echo __("Taxonomy") ; ?>: </span><span><?php echo $individual->SpecimensFlat->getTaxonName() ; ?></span>
              <?php echo image_tag('info.png',"title=info class=info id=taxon_info");?>
            </td>
            <td class="view_level">
              <span><?php echo $individual->SpecimensFlat->getTaxonLevelName() ; ?></span>
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
                     $.get('<?php echo url_for("search/tree?table=taxonomy&id=".$individual->SpecimensFlat->getTaxonRef()) ;?>',function (html){
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
        <?php if($individual->SpecimensFlat->getChronoRef()) : ?>
          <tr>
            <td class="line">
              <span class="pager_nav"><?php echo __("Chronostratigraphy") ; ?>: </span><span><?php echo $individual->SpecimensFlat->getChronoName() ; ?></span>
              <?php echo image_tag('info.png',"title=info class=info id=chrono_info");?>
            </td>
            <td>
              <span><?php echo $individual->SpecimensFlat->getChronoLevelRef() ; ?></span>
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
                     $.get('<?php echo url_for("search/tree?table=chronostratigraphy&id=".$individual->SpecimensFlat->getChronoRef()) ;?>',function (html){
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
        <?php if($individual->SpecimensFlat->getLithoRef()) : ?>
          <tr>
            <td class="line">
              <span class="pager_nav"><?php echo __("Lithostatigraphy") ; ?>: </span><span><?php echo $individual->SpecimensFlat->getLithoName() ; ?></span>
              <?php echo image_tag('info.png',"title=info class=info id=litho_info");?>
            </td>
            <td>
              <span><?php echo $individual->SpecimensFlat->getLithoLevelRef() ; ?></span>
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
                     $.get('<?php echo url_for("search/tree?table=lithostratigraphy&id=".$individual->SpecimensFlat->getLithoRef()) ;?>',function (html){
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
        <?php if($individual->SpecimensFlat->getLithologyRef()) : ?>
          <tr>
            <td class="line">
              <span class="pager_nav"><?php echo __("Lithology") ; ?>: </span><span><?php echo $individual->SpecimensFlat->getLithologyName() ; ?></span>
              <?php echo image_tag('info.png',"title=info class=info id=lithology_info");?>
            </td>
            <td>
              <span><?php echo $individual->SpecimensFlat->getLithologyLevelRef() ; ?></span>
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
                     $.get('<?php echo url_for("search/tree?table=lithology&id=".$individual->SpecimensFlat->getLithologyRef()) ;?>',function (html){
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
        <?php if($individual->SpecimensFlat->getMineralRef()) : ?>
          <tr>
            <td class="line">
              <span class="pager_nav"><?php echo __("Mineralogy") ; ?>: </span><span><?php echo $individual->SpecimensFlat->getMineralName() ; ?></span>
              <?php echo image_tag('info.png',"title=info class=info id=mineral_info");?>
            </td>
            <td>
              <span><?php echo $individual->SpecimensFlat->getMineralLevelRef() ; ?></span>
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
                     $.get('<?php echo url_for("search/tree?table=mineralogy&id=".$individual->SpecimensFlat->getMineralRef()) ;?>',function (html){
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
          <td><span class="pager_nav"><?php echo __("Number of individual") ; ?> :</span></td>
          <td><span>
            <?php if($individual->getSpecimenIndividualsCountMin() == $individual->getSpecimenIndividualsCountMax()) 
                echo ($individual->getSpecimenIndividualsCountMin()==""?"-":$individual->getSpecimenIndividualsCountMin()) ;
              else
                echo __("Between ".$individual->getSpecimenIndividualsCountMin()." and ".$individual->getSpecimenIndividualsCountMax()) ;            
             ?></span>
          </td>
        </tr>
        <tr>
          <td><span class="pager_nav"><?php echo __("Type") ; ?> :</span></td>
          <td>
            <span><?php echo ($individual->getTypeSearch()=="undefined"?"-":$individual->getTypeSearch()) ; ?></span>
          </td>
        </tr>
        <tr>
          <td><span class="pager_nav"><?php echo __("Sex") ; ?> :</span></td>
          <td>
            <span><?php echo ($individual->getSex()=="undefined"?"-":$individual->getSex()) ; ?></span>
          </td>
        </tr>
        <tr>
          <td><span class="pager_nav"><?php echo __("Stage") ; ?> :</span></td>
          <td>
            <span><?php echo ($individual->getStage()=="undefined"?"-":$individual->getStage()) ; ?></span>
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
        <tr><td colspan="2">
          <?php include_partial('multimedia_classification', array('files' => $files, 'count' => $specFilesCount,'type' => 'spec')) ; ?>
        </td></tr>
      </table>

    </div>

    <h2 class="title"><?php echo __("You think there's a mistake ? please suggest us a correction") ?></h2>  
    <div class="suggestion_zone">
      <?php include_partial('suggestion', array('form' => $form,'id'=> $individual->getId())) ; ?>
    </div>
      
  <div class="check_right"> 
    <input type="button" id="close_butt" value="<?php echo __('Close this record'); ?>">
  </div>  
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
