<script type="text/javascript">
$(document).ready(function ()
{
   $('table.classifications_edit').find('.info').click(function()
   {
     if($('.tree').is(":hidden"))
     {
       $.get('<?php echo url_for('catalogue/tree?table=lithostratigraphy&id='.$litho->Parent->getId()) ; ?>',function (html){
         $('.tree').html(html).slideDown();
         });
     }
     $('.tree').slideUp();
   });
});
</script>

<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'catalogue_lithostratigraphy','eid'=> $form->getObject()->getId(), 'view' => true)); ?>
<?php slot('title', __('View Lithostratigraphic unit'));  ?>
<div class="page">
    <h1><?php echo __('View Lithostratigraphic unit');?></h1>
  <div class="table_view">
  <table class="classifications_edit">
    <tbody>
      <tr>
        <th><?php echo $form['name']->renderLabel() ?></th>
        <td>
          <?php echo $litho->getName(); ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['local_naming']->renderLabel() ?></th>
        <td>
	<?php echo ($litho->getLocalNaming()) ? image_tag('checkbox_checked.png', array('alt'=>$litho->getLocalNaming())) : image_tag('checkbox_unchecked.png', array('alt'=>$litho->getLocalNaming()));?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['level_ref']->renderLabel() ?></th>
        <td>
          <?php echo $litho->Level->getLevelName() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['status']->renderLabel() ?></th>
        <td>
          <?php echo $litho->getStatus() ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['parent_ref']->renderLabel() ?></th>
        <td>
          <?php if ($litho->getParentRef() && $litho->Parent->getName() != "-") : ?>
            <?php echo link_to(__($litho->Parent->getName()), 'lithostratigraphy/view?id='.$litho->Parent->getId(), array('id' => $litho->Parent->getId())) ?>
            <?php echo image_tag('info.png',"title=info class=info");?>
            <div class="tree">
            </div>
          <?php else : ?>
          -
          <?php endif ; ?>
        </td>
      </tr>
      <tr>
        <th><?php echo __("Colour") ?></th>
        <td>
          <span class='round_color' style="background-color:<?php echo $litho->getColor() ?>">&nbsp;</span>
        </td>
      </tr>
      <tr>
        <td colspan="2"><?php echo image_tag('magnifier.gif');?> <?php echo link_to(__('Search related specimens'),'specimensearch/search', array('class'=>'link_to_search'));?>
<script type="text/javascript">
  $(document).ready(function (){
    search_data = <?php echo json_encode(array('specimen_search_filters[litho_item_ref]' => $litho->getId(), 'specimen_search_filters[litho_relation]' => 'equal' ));?>;

    $('.link_to_search').click(function (event){
      event.preventDefault();
      postToUrl($(this).attr('href'), search_data);
    });
  });
</script></td>
      </tr>
      <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
        <tr><td colspan="2"><?php echo image_tag('edit.png');?> <?php echo link_to(__('Edit this item'),'lithostratigraphy/edit?id='.$litho->getId());?></td></tr>
      <?php endif;?>
    </tbody>
  </table>
</div>
<div class="view_mode">
 <?php include_partial('widgets/screen', array(
	'widgets' => $widgets,
	'category' => 'cataloguewidgetview',
	'columns' => 1,
	'options' => array('eid' => $form->getObject()->getId(), 'table' => 'lithostratigraphy', 'view' => true)
	)); ?>
</div>
</div>
