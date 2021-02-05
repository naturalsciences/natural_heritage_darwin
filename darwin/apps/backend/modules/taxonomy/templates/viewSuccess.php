<script type="text/javascript">
$(document).ready(function ()
{
   $('table.classifications_edit').find('.info').click(function()
   {
     if($('.tree').is(":hidden"))
     {
       $.get('<?php echo url_for('catalogue/tree?table=taxonomy&id='.$taxon->Parent->getId()) ; ?>',function (html){
         $('.tree').html(html).slideDown();
         });
     }
     $('.tree').slideUp();
   });
});
</script>

<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_taxonomy','eid'=> $form->getObject()->getId(), 'view' => true)); ?>
<?php slot('title', __('View Taxonomic unit'));  ?>
<div class="page">
    <h1><?php echo __('View Taxonomic unit');?></h1>
  <div class="table_view">
  <table class="classifications_edit">
    <tbody>
      <tr>
        <th><?php echo $form['name']->renderLabel() ?></th>
        <td>
          <?php echo $taxon->getNameWithFormat(ESC_RAW); ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['level_ref']->renderLabel() ?></th>
        <td>
          <?php echo $taxon->Level->getLevelName() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['status']->renderLabel() ?></th>
        <td>
          <?php echo __($taxon->getStatus()) ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['extinct']->renderLabel() ?></th>
        <td>
  		    <?php if($form['extinct']->getValue()) echo __("Yes") ; else echo __("No") ;?>
        </td>
      </tr>
	  <!--jmherpers 2019 04 25-->
	  <tr>
        <th><?php echo $form['cites']->renderLabel() ?></th>
        <td>
  		    <?php if($form['cites']->getValue()) echo __("Yes") ; else echo __("No") ;?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['parent_ref']->renderLabel() ?></th>
        <td>
          <?php if ($taxon->getParentRef() &&  $taxon->Parent->getName() != "-") : ?>
          <?php echo link_to(__($taxon->Parent->getNameWithFormat(ESC_RAW)), 'taxonomy/view?id='.$taxon->Parent->getId(), array('id' => $taxon->Parent->getId())) ?>
          <?php echo image_tag('info.png',"title=info class=info");?>
          <div class="tree">
          </div>
          <?php else : ?>
          -
          <?php endif ; ?>
        </td>
      </tr>
      <tr>
       <td colspan="2"><?php echo image_tag('magnifier.gif');?> <?php echo link_to(__('Search related specimens'),'specimensearch/search', array('class'=>'link_to_search'));?>
 with children <input type="checkbox" name="child_taxa" id="child_taxa" checked> with synonyms <input type="checkbox" name="syno_taxa" id="syno_taxa" checked>
<script type="text/javascript">
  $(document).ready(function (){

    
	$('.link_to_search').click(function (event){
      search_data = <?php echo json_encode(array('specimen_search_filters[taxa_list]' => $taxon->getId(), 'specimen_search_filters[taxon_relation][0]' => 'equal' ));?>;
	  if($("#child_taxa").is(':checked'))
		{
			console.log("child");
			search_data["specimen_search_filters[taxon_relation][1]"]="child";
		}
		if($("#syno_taxa").is(':checked'))
		{
			search_data["specimen_search_filters[taxon_relation][2]"]="synonym";
		}
      event.preventDefault();
      postToUrl($(this).attr('href'), search_data, true);
    });
  });
</script></td>
      </tr>
	  
	  <tr>
	
	  <td>
		<?php echo image_tag('magnifier.gif');?>
          <?php preg_match('/(.+?)([A-Z]|\(|$)/', $taxon->getName(), $matches);
					  if(count($matches)>1)
					  {
						echo link_to("Search this Taxonomic Unit on GBIF",sfConfig::get('dw_gbif_url').$matches[1],array('target'=>"_blank"));
					  }
					  else
					  {
						  echo link_to("Search this Taxonomic Unit on GBIF",sfConfig::get('dw_gbif_url'). $taxon->getName(),array('target'=>"_blank"));						  
					  }
					  ?><?php echo image_tag('gbif_small');?>
        </td>
	  </tr>

      <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
        <tr><td colspan="2"><?php echo image_tag('edit.png');?> <?php echo link_to(__('Edit this item'),'taxonomy/edit?id='.$taxon->getId());?></td></tr>
      <?php endif;?>
    </tbody>
  </table>
</div>
 <div class="view_mode">
  <?php include_partial('widgets/screen', array(
    'widgets' => $widgets,
    'category' => 'cataloguewidgetview',
    'columns' => 1,
    'options' => array('eid' => $form->getObject()->getId(), 'table' => 'taxonomy', 'view' => true)
    )); ?>
  </div>
</div>
