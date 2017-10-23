<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<!--[if lte IE 8]>
    <link rel="stylesheet" href="/leaflet/leaflet.ie.css" />
<![endif]-->
<div class="catalogue_gtu">
<?php echo form_tag('gtu/search'.( isset($is_choose) && $is_choose  ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'gtu_filter'));?>
  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>
 <!--ftheeten 2016 06 28-->
        <tr>
            <td colspan="4" ><div style='float: left'> <div class='blue_link' id='get_ig'><a style='text-align: left'><?php echo __('Get IG');?></a></div>
            <div class='blue_link' id='get_specimen_number'><a><?php echo __('Get Specimen number');?></a></div>
            <div class='blue_link' id='get_station_number'><a><?php echo __('Get stations number');?></a></div></td>
        </tr>        
        <tr>
          <th><?php echo $form['code']->renderLabel() ?></th>
         
          <th></th>
        </tr>
       </thead>
      <tbody>
        
        <tr>
          <td><?php echo $form['code']->render() ?></td>
        
          <td></td>
        </tr>
        <tr>
          <th colspan="4"><?php echo $form['tags']->renderLabel() ?></th>
        </tr>

        <?php echo include_partial('andSearch',array('form' => $form['Tags'][0], 'row_line' => 0));?>

        <tr class="and_row">
          <td colspan="3"></td>
          <td>
            <?php echo image_tag('add_blue.png');?> <a href="<?php echo url_for('gtu/andSearch');?>" class="and_tag"><?php echo __('And'); ?></a>
          </td>
        </tr>
      </tbody>

      </table>

      <fieldset id="lat_long_set">
        <legend><?php echo __('Show Result as map');?> <input type="checkbox" id="show_as_map" autocomplete="off"></legend>
          <table>
            <tr>
              <td>
              </td>
              <th>
                <?php echo $form['lat_from']->renderLabel();?>
              </th>
              <th>
                <?php echo $form['lon_from']->renderLabel();?>
              </th>
            </tr>
            <tr>
              <th class="right_aligned"><?php echo __('Between');?></th>
              <td><?php echo $form['lat_from'];?></td>
              <td><?php echo $form['lon_from'];?><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
            </tr>
            <tr>
              <th class="right_aligned"><?php echo __('And');?></th>
              <td><?php echo $form['lat_to'];?></td>
              <td><?php echo $form['lon_to'];?><?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
            </tr>
          </table>
          <div id="map_search_form" style="display:none">
            <?php echo __('Show accuracy of each point');?> <input type="checkbox" id="show_accuracy" /><br /><br />
            <div style="height:400px;width:100%" id="smap"></div>

 <div class="pager paging_info hidden">
   <?php echo image_tag('info2.png');?>
    <span class="inner_text"></span>
  </div>

         </div>
    </fieldset>
    <?php echo $form->renderHiddenFields();?>
    <div class="edit">
      <input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" />
    </div>
<div class="clear"></div>
    <script  type="text/javascript">
    initSearchMap();

	
	
	
    $(document).ready(function () {
	
	

      $('.catalogue_gtu').choose_form({});

      $(".new_link").click( function() {
        url = $(this).find('a').attr('href'),
        data= $('.search_form').serialize(),
        reg=new RegExp("(<?php echo $form->getName() ; ?>)", "g");   
        open(url+'?'+data.replace(reg,'gtu'));
        return false;  
      });


      var num_fld = 1;
      $('.and_tag').click(function()
      {
        hideForRefresh('#gtu_filter');
        $.ajax({
            type: "GET",
            url: $(this).attr('href') + '/num/' + (num_fld++) ,
            success: function(html)
            {
              $('table.search > tbody .and_row').before(html);
              showAfterRefresh('#gtu_filter');
            }
        });
        return false;

      });
	  

        
       //ftheeten 2016 06 28
        $('#get_ig').click( function()
        {
           var idGTU=$("#specimen_ig_ref_name").val();
            if(!!idGTU)
            {
                    $("#gtu_filters_code").val(idGTU);
            }
            else
            {
                 $("#gtu_filters_code").val('');
            }
        });
        //ftheeten 2016 06 28
        $('#get_specimen_number').click( function()
        {
            var code_word="";
            if($("#specimen_newCodes_0_code_prefix").length>0)
            {
                
                code_word="#specimen_newCodes_0_code";
            }
            else if($("#specimen_Codes_0_code_prefix").length>0)
            {
                    code_word="#specimen_Codes_0_code";
            }
            var valSpecCodePrefix=$(code_word+"_prefix").val()||'';
			var valSpecCodePrefixSeparator= $(code_word+"_prefix_separator").val()||'';
			var valSpecCode= $(code_word).val()||'';
			var valSpecCodeSuffixSeparator=$(code_word+"_suffix_separator").val()||"";
			var valSpecCodeSuffix= $(code_word+"_suffix").val()||'';
			var codeTotal=valSpecCodePrefix.concat(valSpecCodePrefixSeparator.concat(valSpecCode.concat(valSpecCodeSuffixSeparator.concat(valSpecCodeSuffix))));
            if(!!codeTotal)
            {
                    $("#gtu_filters_code").val(codeTotal);
            }
            else
            {
                 $("#gtu_filters_code").val('');
            }
        });
        
        //ftheeten 2016 06 28
        $('#get_station_number').click( function()
        {
           var idGTU=$(".view_loc_code").text();
            if(!!idGTU)
            {
                    $("#gtu_filters_code").val(idGTU);
            }
            else
            {
                 $("#gtu_filters_code").val('');
            }
        });
        
        
    });
    </script>
    <div class="search_results">
      <div class="search_results_content"> 
      </div>
    </div>
    <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('gtu/new') ?>"><?php echo __('New');?></a></div>
  </div>
</form> 
</div>

