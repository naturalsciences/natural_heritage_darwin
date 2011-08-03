<?php echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<kml xmlns="http://earth.google.com/kml/2.0">
        <Document>


<?php foreach($items as $item):?>
                <Placemark>  
                      <name><?php echo $item->getCode();?></name>
<description><![CDATA[
      <div class="map_result_id_<?php echo $item->getId();?>">
        <div class="item_name hidden"><?php echo $item->getTagsWithCode(ESC_RAW);?></div>
        <div class=""><?php echo $item->getName(ESC_RAW);?></div>
        <?php if(! $is_choose):?>
          <?php echo link_to(image_tag('edit.png',array('title' => 'Edit')),'gtu/edit?id='.$item->getId());?>
        <?php echo link_to(image_tag('duplicate.png',array('title' => 'Duplicate')),'gtu/new?duplicate_id='.$item->getId());?>
        <?php else:?>
          <div class="result_choose" onclick="chooseGtuInMap(<?php echo $item->getId();?>);"><?php echo __('Choose');?></div>
        <?php endif;?>
      </div>
    ]]></description>
                        
        <?php echo $item->getKml(ESC_RAW);?>
                        
                </Placemark>


<?php endforeach;?>
        </Document>
</kml>