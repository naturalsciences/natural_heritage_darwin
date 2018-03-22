<?php include_stylesheets_for_form($searchForm) ?>
<?php include_javascripts_for_form($searchForm) ?>

<div class="catalogue_filter">
<?php echo form_tag('catalogue/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'catalogue_filter'));?>
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
          <?php if(isset($searchForm['lower_bound']) && isset($searchForm['upper_bound'])):?>
            <th class="datesNum"><?php echo $searchForm['lower_bound']->renderLabel();?></th>
            <th class="datesNum"><?php echo $searchForm['upper_bound']->renderLabel();?></th>
          <?php endif;?>
           <!--ftheeten 2017 06 30-->
           <?php if(isset($searchForm['collection_ref'])&&isset($searchForm['collection_ref_for_modal'])):?>
		    <!--jmherpers 2018 03 14-->
            <!--<th><?php echo $searchForm['collection_ref']->renderLabel();?></th>-->
			<th>Collection</th>
           <?php endif;?>
		    <!--ftheeten 2017 06 30-->
           <?php if(isset($searchForm['metadata_ref'])):?>
           <!--jmherpers 2018 03 14-->
            <!--<th><?php echo $searchForm['metadata_ref']->renderLabel();?></th>-->
			<th>Taxonomy</th>
           <?php endif;?>
          <th>
          <th></th>
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
          <?php if(isset($searchForm['lower_bound']) && isset($searchForm['upper_bound'])):?>
            <td class="datesNum"><?php echo $searchForm['lower_bound'];?></td>
            <td class="datesNum"><?php echo $searchForm['upper_bound'];?></td>
          <?php endif;?>
           <!--ftheeten 2017 06 30-->
           <?php if(isset($searchForm['collection_ref'])&&isset($searchForm['collection_ref_for_modal'])):?>
           <?php if($is_choose===FALSE):?>
                <td><?php echo $searchForm['collection_ref'];?></td>
            <?php else:?>
                <td><?php echo $searchForm['collection_ref_for_modal'];?></td>
           <?php endif;?>
          <?php endif;?> 
		  <?php if(isset($searchForm['metadata_ref'])):?>
            <td><?php echo $searchForm['metadata_ref'];?></td>
          <?php endif;?>
          <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Search');?>" /></td>
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
    <!--ftheeten 2018 03 14 -->
    <?php if($searchForm['table']->getValue()=="taxonomy"):?>
        <input type="hidden" name="referrer" id="referrer" value="taxonomy"></input>
     <?php endif ; ?>
    <div class="search_results">
      <div class="search_results_content">
      </div>
    </div>
    <!--ftheeten 2018 03 14-->
    <div>
        <input style="display:none" type="button" class="float_button" name="get_last_taxon" id="get_last_taxon" value="Get Last taxon"></input>
    </div>
    <?php if( (isset($user_allowed) && $user_allowed) || ($sf_user->getDbUserType() >= Users::ENCODER) ): ?>
    <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for($searchForm['table']->getValue().'/new') ?>"><?php echo __('New taxon');?></a></div>
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
   //ftheeten 2018 02 14 add display:block
   $("#get_last_taxon").css("display", 'block');
   
   url = $(this).find('a').attr('href'),
   data= $('.search_form').serialize(),
   reg=new RegExp("(<?php echo $searchForm->getName() ; ?>)", "g");   
   open(url+'?'+data.replace(reg,'<?php echo $searchForm['table']->getValue() ; ?>'));
    return false;  
  });

//function 2017 03 30--

    $('.treelist li:not(li:has(ul)) img.tree_cmd').hide();
    $('.chk input').change(function()
    {
      li = $(this).closest('li');
      if(! $(this).is(':checked'))
        li.find(':checkbox').not($(this)).removeAttr('checked').change();
      else
        li.find(':checkbox').not($(this)).attr('checked','checked').change();
    });

    $('#clear_collections').click(function()
    {
       $('table.widget_sub_table').find(':checked').removeAttr('checked').change();
    });

    $('.collapsed').click(function()
    {
        $(this).addClass('hidden');
        $(this).siblings('.expanded').removeClass('hidden');
        $(this).parent().siblings('ul').show();
    });

    $('.expanded').click(function()
    {
        $(this).addClass('hidden');
        $(this).siblings('.collapsed').removeClass('hidden');
        $(this).parent().siblings('ul').hide();
    });

    $('#check_editable').click(function(){
      $('.treelist input:checked').removeAttr('checked').change();
      $('li[data-enc] > div > label > input:checkbox').attr('checked','checked').change();
    });


  //ftheeten 2017 07 06
  if($.trim($(".specimen_collection_ref").val()).length>0)
  {

        $('.coll_for_taxonomy_ref option[value="'+$.trim($(".specimen_collection_ref").val())+'"]').attr("selected", true);
  }
  //ftheeten 2017 07 06
  if($.trim($(".coll_for_taxonomy_insertion_ref").val()).length>0)
  {

        $('.coll_for_taxonomy_ref option[value="'+$.trim($(".coll_for_taxonomy_insertion_ref").val())+'"]').attr("selected", true);
  }
  
    //ftheeten 2017 07 23
  if($.trim($(".col_check_metadata_ref").val()).length>0)
  {
        var tmpID=$('.col_check_metadata_ref[id]').first().attr('id');
        var tmpName=$('.col_check_metadata_ref[id]').first().attr('name');
        $('.col_check_metadata_ref option[value="'+$.trim($(".col_check_metadata_ref").val())+'"]').attr("selected", true);
		$('.col_check_metadata_ref').prop("disabled", "disabled");
        var tmp=$('<input>').attr({
            type: 'hidden',
            id: tmpID,
            name: tmpName,
            });
       tmp.val($('.col_check_metadata_ref').val());     
       tmp.appendTo('#<?php echo ($is_choose)?'search_and_choose':'search' ?>');
  }
  
  //ftheeten 2018 03 14
  $("#get_last_taxon").click(
    function()
    {
        var lastTaxon=localStorage.getItem("last_scientific_name");
        $(".taxonomy_name_callback").val(lastTaxon);
        
        $(".taxonomy_level_callback").val($(".taxonomy_level_callback option:first").val());
        $(".taxonomy_collection_callback").val($(".taxonomy_collection_callback option:first").val());
        $(".col_check_metadata_callback").val($(".col_check_metadata_callback option:first").val());
        $(".search_submit").click();


    }
  );
});
</script>
</div>
