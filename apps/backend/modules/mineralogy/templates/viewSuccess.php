<script type="text/javascript">
$(document).ready(function ()
{
   $('table.classifications_edit').find('.info').click(function()
   {
     if($('.tree').is(":hidden"))
     {
       $.get('<?php echo url_for('catalogue/tree?table=mineralogy&id='.$mineral->Parent->getId()) ; ?>',function (html){
         $('.tree').html(html).slideDown();
         });
     }
     $('.tree').slideUp();
   });
});
</script>

<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_mineralogy','eid'=> $form->getObject()->getId(), 'view' => true)); ?>
<?php slot('title', __('View Mineralogic unit'));  ?>
<div class="page">
    <h1><?php echo __('View Mineralogic unit');?></h1>
  <div class="table_view">
  <table class="classifications_edit">
    <tbody>
      <tr>
        <th><?php echo $form['code']->renderLabel() ?></th>
        <td>
          <?php echo $mineral->getCode() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['name']->renderLabel() ?></th>
        <td>
          <?php echo $mineral->getName(); ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['classification']->renderLabel() ?></th>
        <td>
          <?php echo $mineral->getClassification() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['local_naming']->renderLabel() ?></th>
        <td>
          <?php echo ($mineral->getLocalNaming()) ? image_tag('checkbox_checked.png', array('alt'=>$mineral->getLocalNaming())) : image_tag('checkbox_unchecked.png', array('alt'=>$mineral->getLocalNaming()));?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['level_ref']->renderLabel() ?></th>
        <td>
          <?php echo $mineral->Level->getLevelName() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['status']->renderLabel() ?></th>
        <td>
          <?php echo $mineral->getStatus() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['parent_ref']->renderLabel() ?></th>
        <td>
          <?php if ($mineral->getParentRef() &&  $mineral->Parent->getName() != "-") : ?>
            <?php echo link_to(__($mineral->Parent->getName()), 'mineralogy/view?id='.$mineral->Parent->getId(), array('id' => $mineral->Parent->getId())) ?>
            <?php echo image_tag('info.png',"title=info class=info");?>
            <div class="tree">
            </div>
          <?php else : ?>
          -
          <?php endif ; ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['cristal_system']->renderLabel() ?></th>
        <td>
          <?php echo $mineral->getCristalSystem() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo __("Colour") ?></th>
        <td>
          <span class='round_color' style="background-color:<?php echo $mineral->getColor() ?>">&nbsp;</span>
        </td>
      </tr>
      <tr>
        <td colspan="2"><?php echo image_tag('magnifier.gif');?> <?php echo link_to(__('Search related specimens'),'specimensearch/search', array('class'=>'link_to_search'));?>
<script type="text/javascript">
  $(document).ready(function (){
    search_data = <?php echo json_encode(array('specimen_search_filters[mineral_item_ref]' => $mineral->getId(), 'specimen_search_filters[mineral_relation]' => 'equal' ));?>;
    $('.link_to_search').click(function (event){
      event.preventDefault();
      postToUrl($(this).attr('href'), search_data);
    });
  });
</script></td>
      </tr>
      <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
        <tr><td colspan="2"><?php echo image_tag('edit.png');?> <?php echo link_to(__('Edit this item'),'mineralogy/edit?id='.$mineral->getId());?></td></tr>
      <?php endif;?>
    </tbody>
  </table>
</div>
<div class="view_mode">
 <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'cataloguewidgetview',
	'columns' => 1,
	'options' => array('eid' => $form->getObject()->getId(), 'table' => 'mineralogy', 'view' => true)
	)); ?>
</div>
</div>
