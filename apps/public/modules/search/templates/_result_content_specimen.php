    <td class="col_category">
      <?php if($specimen->getCategory() == 'physical' || $specimen->getCategory() == 'mixed' ):?>
        <?php echo image_tag('sp_in.png', array('alt' => __('Physical'), 'title'=> __('Physical')));?>
      <?php endif;?>
      <?php if($specimen->getCategory() == 'mixed' ):?>
        <?php echo __('+');?>
      <?php endif;?>
      <?php if($specimen->getCategory() == ''  || $specimen->getCategory() == 'mixed' ):?>
        <?php echo image_tag('blue_eyel.png', array('alt' => __('Other'), 'title'=> __('Other')));?>
      <?php endif;?>
    </td>
    <td  class="col_collection">
      <?php if($specimen->getCollectionRef() > 0) : ?>
        <span class="line">
        <?php echo image_tag('info.png',"title=info class=info id=collection_".$id."_info");?>
        <?php echo $specimen->getCollectionName();?>
        <span>
        <div id="collection_<?php echo $id;?>_tree" class="tree"></div>
        <script type="text/javascript">
            $('#collection_<?php echo $id;?>_info').click(function() 
            {
              item_row=$(this).closest('tr');
              if(item_row.find('#collection_<?php echo $id;?>_tree').is(":hidden"))
              {
                $.get('<?php echo url_for("search/tree?table=collections&id=".$specimen->getCollectionRef()) ;?>',function (html){
                  item_row.find('#collection_<?php echo $id;?>_tree').html(html).slideDown();
                  });
              }
              $('#collection_<?php echo $id;?>_tree').slideUp();
            });
        </script>
      <?php endif ; ?>
    </td>
    <td class="col_taxon">
      <?php if($specimen->getTaxonRef() > 0) : ?>
        <span class="line">
        <?php echo image_tag('info.png',"title=info class=info id=taxon_".$id."_info");?>
        <?php echo $specimen->getTaxonName();?>
        </span>
        <div id="taxon_<?php echo $id;?>_tree" class="tree"></div>
        <script type="text/javascript">
            $('#taxon_<?php echo $id;?>_info').click(function() 
            {
              item_row=$(this).closest('tr');
              if(item_row.find('#taxon_<?php echo $id;?>_tree').is(":hidden"))
              {
                $.get('<?php echo url_for("search/tree?table=taxonomy&id=".$specimen->getTaxonRef()) ;?>',function (html){
                  item_row.find('#taxon_<?php echo $id;?>_tree').html(html).slideDown();
                  });
                $(this).closest('td').css('width','auto') ;
              }
              $('#taxon_<?php echo $id;?>_tree').slideUp();
            });
        </script> 
      <?php endif ; ?>
    </td>

    <td class="col_gtu">
      <?php if($specimen->getGtuRef() != 0 ) : ?>
          <?php echo $specimen->getCountryTags(ESC_RAW) ; ?>
      <?php endif ; ?>
    </td>
    <td class="col_chrono">
      <?php if($specimen->getChronoRef() > 0) : ?>
        <?php echo image_tag('info.png',"title=info class=info id=chrono_".$id."_info");?>
        <?php echo $specimen->getChronoName();?>
        <div id="chrono_<?php echo $id;?>_tree" class="tree"></div>
        <script type="text/javascript">
            $('#chrono_<?php echo $id;?>_info').click(function() 
            {
              item_row=$(this).closest('tr');
              if(item_row.find('#chrono_<?php echo $id;?>_tree').is(":hidden"))
              {
                $.get('<?php echo url_for("search/tree?table=chronostratigraphy&id=".$specimen->getChronoRef()) ;?>',function (html){
                  item_row.find('#chrono_<?php echo $id;?>_tree').html(html).slideDown();
                  });
              }
              $('#chrono_<?php echo $id;?>_tree').slideUp();
            });
        </script> 
      <?php endif ; ?>
    </td>
    <td  class="col_litho">
      <?php if($specimen->getLithoRef() > 0) : ?>
        <?php echo image_tag('info.png',"title=info class=info id=litho_".$id."_info");?>
        <?php echo $specimen->getLithoName();?>
        <div id="litho_<?php echo $id;?>_tree" class="tree"></div>
        <script type="text/javascript">
            $('#litho_<?php echo $id;?>_info').click(function() 
            {
              item_row=$(this).closest('tr');
              if(item_row.find('#litho_<?php echo $id;?>_tree').is(":hidden"))
              {
                $.get('<?php echo url_for("search/tree?table=lithostratigraphy&id=".$specimen->getLithoRef()) ;?>',function (html){
                  item_row.find('#litho_<?php echo $id;?>_tree').html(html).slideDown();
                  });
              }
              $('#litho_<?php echo $id;?>_tree').slideUp();
            });
        </script> 
      <?php endif ; ?>
    </td> 
    <td class="col_lithologic">
      <?php if($specimen->getLithologyRef() > 0) : ?>
        <?php echo image_tag('info.png',"title=info class=info id=lithologic_".$id."_info");?>
        <?php echo $specimen->getLithologyName();?>
        <div id="lithologic_<?php echo $id;?>_tree" class="tree"></div>
        <script type="text/javascript">
            $('#lithologic_<?php echo $id;?>_info').click(function() 
            {
              item_row=$(this).closest('tr');
              if(item_row.find('#lithologic_<?php echo $id;?>_tree').is(":hidden"))
              {
                $.get('<?php echo url_for("search/tree?table=lithology&id=".$specimen->getLithologyRef()) ;?>',function (html){
                  item_row.find('#lithologic_<?php echo $id;?>_tree').html(html).slideDown();
                  });
              }
              $('#lithologic_<?php echo $id;?>_tree').slideUp();
            });
        </script>
      <?php endif ; ?>
    </td>
    <td class="col_mineral">
      <?php if($specimen->getMineralRef() > 0) : ?>
        <?php echo image_tag('info.png',"title=info class=info id=mineral_".$id."_info");?>
        <?php echo $specimen->getMineralName();?>
        <div id="mineral_<?php echo $id;?>_tree" class="tree"></div>
        <script type="text/javascript">
            $('#mineral_<?php echo $id;?>_info').click(function() 
            {
              item_row=$(this).closest('tr');
              if(item_row.find('#mineral_<?php echo $id;?>_tree').is(":hidden"))
              {
                $.get('<?php echo url_for("search/tree?table=mineralogy&id=".$specimen->getMineralRef()) ;?>',function (html){
                  item_row.find('#mineral_<?php echo $id;?>_tree').html(html).slideDown();
                  });
              }
              $('#mineral_<?php echo $id;?>_tree').slideUp();
            });
        </script>  
      <?php endif ; ?>
    </td>
    <td class="col_expedition">
      <?php if($specimen->getExpeditionRef() > 0) : ?>
        <?php echo $specimen->getExpeditionName();?>
      <?php endif ; ?>
    </td> 
