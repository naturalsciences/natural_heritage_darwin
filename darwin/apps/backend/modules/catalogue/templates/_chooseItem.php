<?php include_stylesheets_for_form($searchForm) ?>
<?php include_javascripts_for_form($searchForm) ?>
<?php
	$flagMenu=detect_menu_hidden();
?>
<?php if(isset($is_choose)) : ?><div class="warn_message"><?php echo __('catalogue_search_tips') ; ?></div><?php endif ; ?>
<div class="catalogue_filter">
<?php echo form_tag('catalogue/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'catalogue_filter'));?>
<?php $is_modal=false;?>
<?php if(isset($is_choose)) : ?>
	<?php if($is_choose) : ?>
	<?php $is_modal=true;?>
	<?php endif;?>
<?php endif;?>
<div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>
          <?php if(isset($searchForm['code'])):?>
            <th><?php echo $searchForm['code']->renderLabel();?></th>
          <?php endif;?>
          <th><?php echo $searchForm['name']->renderLabel();?></th>
          <?php if(isset($searchForm['classification'])):?>
            <th><?php echo $searchForm['classification']->renderLabel();?></th>
          <?php endif;?>
          <th><?php echo $searchForm['level_ref']->renderLabel();?></th>
		  <!--JMHerpers 2019 04 29-->
          <?php if(isset($searchForm['cites'])):?>
            <th><?php echo 'CITES';?></th>
          <?php endif;?>
          <?php if(isset($searchForm['status'])):?>
            <th><?php echo 'Status';?></th>
          <?php endif;?>
          <?php if(isset($searchForm['lower_bound']) && isset($searchForm['upper_bound'])):?>
            <th class="datesNum"><?php echo $searchForm['lower_bound']->renderLabel();?></th>
            <th class="datesNum"><?php echo $searchForm['upper_bound']->renderLabel();?></th>
          <?php endif;?>
        </tr>
      </thead>
      <tbody>
        <tr>
          <?php if(isset($searchForm['code'])):?>
            <td><?php echo $searchForm['code'];?></td>
          <?php endif;?>
          <td><?php echo $searchForm['name'];?><?php echo $searchForm->renderHiddenFields();?></td>
          <?php if(isset($searchForm['classification'])):?>
            <td><?php echo $searchForm['classification'];?></td>
          <?php endif;?>
          <td><?php echo $searchForm['level_ref'];?></td>
		  <!--JMHerpers 2019 04 29-->
		  <?php if(isset($searchForm['cites'])):?>
            <td><?php echo $searchForm['cites'];?></td>
          <?php endif;?>
          <?php if(isset($searchForm['status'])):?>
            <td><?php echo $searchForm['status'];?></td>
          <?php endif;?>
          <?php if(isset($searchForm['lower_bound']) && isset($searchForm['upper_bound'])):?>
            <td class="datesNum"><?php echo $searchForm['lower_bound'];?></td>
            <td class="datesNum"><?php echo $searchForm['upper_bound'];?></td>
          <?php endif;?>
                    </tr>
          <tr>
          <!--ftheeten 2017 06 30-->

            <!--<th><?php echo $searchForm['collection_ref']->renderLabel();?></th>-->
			<th>Collection</th>
   
		    <!--ftheeten 2017 06 30-->
           <?php if(isset($searchForm['metadata_ref'])):?>
           <!--jmherpers 2018 03 14-->
            <!--<th><?php echo $searchForm['metadata_ref']->renderLabel();?></th>-->
			<th>Taxonomy</th>
         
           <?php endif;?>
           <?php if(isset($searchForm['ig_number'])):?>
           <!--ftheeten 2018 03 23-->
           <th><?php echo $searchForm['ig_number']->renderLabel();?></th>
			
         
           <?php endif;?>
           </tr>
          <tr>
           <!--ftheeten 2017 06 30-->
           <?php if($is_modal):?>
                  <td><?php echo $searchForm['collection_ref_for_modal'];?></td>
			<?php else:?>
				<td><?php echo $searchForm['collection_ref'];?></td>
          <?php endif;?> 
		  <?php if(isset($searchForm['metadata_ref'])):?>
            <td><?php echo $searchForm['metadata_ref'];?></td>
          <?php endif;?>
          <?php if(isset($searchForm['ig_number'])):?>
            <td><?php echo $searchForm['ig_number'];?></td>
          <?php endif;?>
        </tr>

        <tr>
		  <td colspan="4"><input class="search_submit" type="submit" name="search" value="<?php echo __('Search');?>" /></td>
        </tr>
        <tr class="hidden">
          <td><?php echo $searchForm['relation'];?></td>
          <td <?php if(isset($searchForm['lower_bound'])) echo 'colspan="3"'; elseif(isset($searchForm['classification'])) echo 'colspan="3"';?>><span class="search_item_name"></span></td>
          <td class="widget_row_delete">
            <?php echo image_tag('remove.png', 'alt=Delete class=clear_relation id=clear_cat_relation'); ?>
            <?php echo help_ico($searchForm['relation']->renderHelp(),$sf_user);?>
          </td>   
        </tr>
      </tbody>
    </table>

    <div class="search_results">
      <div class="search_results_content">
      </div>
    </div>
    <?php if( (isset($user_allowed) && $user_allowed) || ($sf_user->getDbUserType() >= Users::ENCODER)&& $flagMenu ): ?>
    <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for($searchForm['table']->getValue().'/new') ?>"><?php echo __('New Unit');?></a></div>
    <?php endif ; ?>
  </div>
</form>
<script>
$(document).ready(function () {
  $('.catalogue_filter').choose_form({});
  $('#clear_cat_relation').click(function (event)
  {
    event.preventDefault();
    $('.search_item_name').html('');
    $('#searchCatalogue_item_ref').val('');
    $('.search_item_name').closest('tr').hide();
  });

  $(".new_link").click( function()
  {
   url = $(this).find('a').attr('href'),
   data= $(".search_form input[value!='']").serialize(),
   reg=new RegExp("(<?php echo $searchForm->getName() ; ?>)", "g");   
   open(url+'?'+data.replace(reg,'<?php echo $searchForm['table']->getValue() ; ?>'));
    return false;  
  });
  
  //ftheeten 2018 09 07
   $(".coll_for_taxonomy_ref").val("0"); 

    //ftheeten 2018 04 10
  var ig_num=urlParam('ig_num');
  if(!!ig_num)
  {
    
        $("#searchCatalogue_ig_number").val(decodeURIComponent(ig_num));
        $( ".search_form" ).submit();
  }      

});
</script>
</div>
