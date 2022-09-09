<?php use_helper('Text');?>
<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Link');?></th>
      <th><?php echo __('Comment');?></th>
      <th><?php echo __('Category');?></th>
      <th><?php echo __('Contributor');?></th>
      <th><?php echo __('Disclaimer');?></th>
      <th><?php echo __('License');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($links as $link):?>
      <?php  if($link->getCategory()!="image_link"||($link->getCategory()=="image_link"&&array_key_exists($link->getDisplayOrder(),$sf_data->getRaw('correspondingViewerLink'))===FALSE)):?>
          <tr>
            
           <td  style="font-weight:bold; text-align:center">
             <?php  if($link->getCategory()=="html_3d_snippet"):?>
                <a  class="link_catalogue_view" href="<?php echo url_for("extlinks/snapchatSnippet?id=".$link->getId());?>/model/undefined">View (3D)</a></td>
		     <?php  elseif($link->getCategory()=="iiif"):?>
				<a  class="link_catalogue_view" href="<?php echo url_for("extlinks/iiifViewer?id=".$link->getId());?>/model/undefined">View (IIIF)</a></td>
             <?php  elseif($link->getCategory()=="thumbnail"):?>
                    <?php  if(array_key_exists($link->getDisplayOrder(),$sf_data->getRaw('correspondingViewerLink'))):?>
                        <a class="link_catalogue_view" href="<?php echo url_for("extlinks/extViewer?id=".$sf_data->getRaw('correspondingViewerLink')[$link->getDisplayOrder()]);?>"><img   style="max-width:100px" src="<?php echo url_for($link->getUrl());?>"/></a>
                   <?php else:?>
                       <img  class="link_catalogue_view" style="max-width:100px" src="<?php echo url_for($link->getUrl());?>"/>
                    <?php endif;?>
					<!--ftheeten 2018 03 13-->
			<?php  elseif($link->getCategory()=="document"):?>
					<a target="_blank" href="<?php echo url_for($link->getUrl());?>"/>Link</a>
		    <?php  else:?>
					<a target="_blank" href="<?php echo url_for($link->getUrl());?>"/><?php print($link->getUrl());?> (<?php print($link->getCategory());?>)</a>
             <?php endif;?>
            </td>
            <td>
              <div title="<?php echo $link->getComment();?>"><?php echo truncate_text($link->getComment(), 50);?></div>
            </td>
             <td>
              <div title="<?php echo $link->getCategory();?>"><?php echo $link->getCategory();?></div>
            </td>
            <td>
              <div title="<?php echo $link->getContributor();?>"><?php echo $link->getContributor();?></div>
            </td>
            <td>
              <div title="<?php echo $link->getDisclaimer();?>"><?php echo $link->getDisclaimer();?></div>
            </td>
             <td>
              <div title="<?php echo $link->getLicense();?>"><?php echo $link->getLicense();?></div>
            </td>
          </tr>
       <?php endif;?>
  <?php endforeach;?>
  </tbody>
</table>
