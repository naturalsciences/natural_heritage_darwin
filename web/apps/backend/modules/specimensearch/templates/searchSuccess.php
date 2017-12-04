<?php slot('title', __('Specimens Search Result'));  ?>
<?php use_javascript('double_list.js');?>
<?php include_partial('result_cols', array('columns' => $columns, 'field_to_show' => $field_to_show,  'arrayDisplay' => $arrayDisplay));?>

<div class="encoding">
  <?php include_stylesheets_for_form($form) ?>
  <?php include_javascripts_for_form($form) ?>
  <div class="page" id="search_div">
    <h1 id="title"><?php echo __('Specimens Search Result');?></h1>
    <?php echo form_tag('specimensearch/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'specimensearch_form','id'=>'specimen_filter'));?>
      <ul id="intro" class="hidden">
        <?php 
        // Render all the form fields as hidden input if possible. if the value is an array or and object render them as usual
        foreach($form as $row)
        { 
          $w = new sfWidgetFormInputHidden();
          $attributes = $form->getWidget($row->getName())->getAttributes();
          if(is_string($row->getValue()) || is_null($row->getValue()))
            echo '<li>'.$w->render( $form->getWidgetSchema()->generateName($row->getName()),$row->getValue(),$attributes).'</li>';
          else
            echo '<li>'.$row.'</li>';
        }?>
      </ul>
      <div class="search_results">
        <div class="search_results_content">
          <?php include_partial('searchSuccess',
                                array('specimensearch' => $specimensearch,
                                      'codes' => $codes,
                                      'form' => $form, 
                                      'orderBy' => $orderBy,
                                      's_url' => $s_url,
                                      'orderDir' => $orderDir,
                                      'currentPage' => $currentPage,
                                      'pagerLayout' => $pagerLayout,
                                      'is_specimen_search' => $is_specimen_search,
                                      'columns' => $columns,
                                     )
                               ); ?>
        </div>
      </div>
      <?php if(isset($is_pinned_only_search)):?>
        <input type="hidden" name="pinned" value="true" />
      <?php endif;?>
      <script  type="text/javascript">
        $(document).ready(function () {

          $('form#specimen_filter select.double_list_select-selected option').attr('selected', 'selected');
          $('body').duplicatable({
            duplicate_href: '<?php echo url_for('specimen/confirm');?>',
            duplicate_binding_type: 'live'
          });

          $("#criteria_butt").click(function(){
            // Reselect all double list options that should be selected to be taken in account in the form submit
            // Submit the form with criteria = 1 -> telling we request the index template
            $('#specimen_filter').attr('action','<?php echo url_for('specimensearch/search?criteria=1');?>').submit();
          });
        <?php if($is_specimen_search):?>
          $('#del_from_spec').click(function(){
            pins_array = new Array();
            $('.remove_spec:checked').each(function(){
              pins_array.push( $(this).val() );
            });
            if(pins_array.length == 0) {
              alert("<?php echo __('You must select at least one specimen.');?>");
            }
            else {
              if(confirm('<?php echo addslashes(__('Are you sure?'));?>'))
              {
                $.get('<?php echo url_for('savesearch/removePin?search='.$is_specimen_search);?>/ids/' + pins_array.join(',') ,function (html){
                  for(var i = 0; i < pins_array.length; i++) {
                    $('.rid_' + pins_array[i]).remove();
                  }
                  if($('.spec_results tbody tr').length == 0) {
                    location.reload();
                  }
                });
              }
            }
          });
        <?php endif;?>
          $('#export_spec').click(function(event){
            $('form.specimensearch_form').attr('action', $('form.specimensearch_form').attr('action') + '/export/csv');
            $('form.specimensearch_form').submit();
          });

        });
      </script>
    </form>
      <div class="check_right" id="save_button"> 
        <a href="<?php echo url_for('specimen/confirm') ; ?>" class="hidden"></a>
        <?php include_partial('savesearch/saveSpec', array('spec_lists'=>$spec_lists));?>

        <?php if(! $is_specimen_search):?>
          <?php include_partial('savesearch/saveSearch');?>
        <?php endif;?>
      </div>
      <?php if(!isset($is_pinned_only_search) && ! $is_specimen_search):?>
        <input type="button" id="criteria_butt" class="save_search" value="<?php echo __('Back to criteria'); ?>">
      <?php elseif(! isset($is_pinned_only_search) && $is_specimen_search):?>
        <input type="button" id="del_from_spec" class="save_search" value="<?php echo __('Remove selected'); ?>">
      <?php endif;?>
      <!--<input type="button" id="export_spec" class="save_search" value="<?php echo __('Export');?>" />-->
	  
	  <!-- added by Son -->
      <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
          <input type="button" id="print_spec" class="save_search" value="<?php echo __('Print');?>" />
          <!-- end added code -->
          
          <!-- added by Franck -->
          <input type="button" id="xml_spec" class="save_search" value="<?php echo __('XML');?>" />
          <!-- ftheeten 2016/01/29-->
          <input type="button" id="report_spec" class="save_search" value="<?php echo __('Report');?>" />
          <!-- these fields are filled by _searchSucess.php -->
       <?php endif;?>
	  <input type="hidden" name="h_current_page" id="h_current_page" value="-1"/>
	  <input type="hidden" name="h_order_by" id="h_order_by" value="-1"/>
	  <input type="hidden" name="h_order_dir" id="h_order_dir" value="-1"/>
      <!-- end added code -->
	  <script  type="text/javascript">
	  
$(document).ready(function () {
	//alert('<?php echo $currentPage ?>');

  $("#print_spec").click(function(event){
        
        form = document.createElement('form');
        form.setAttribute('method', 'POST');
		form.setAttribute('action', '<?php echo url_for("specimensearch/print")?>');
		form.setAttribute('target', '_blank');
		
        <?php if ($search_request->hasParameter('specimen_search_filters')){
                  echo "myvar = document.createElement('input');
                        myvar.setAttribute('name', 'specimen_search_filters');
                        myvar.setAttribute('value', '";
                  echo serialize($_POST['specimen_search_filters']);
                  echo "');
                  form.appendChild(myvar);";
                  }
        ?>
           <?php if ($search_request->hasParameter('search_id')){
                  echo "myvar = document.createElement('input');
                        myvar.setAttribute('name', 'search_id');
                        myvar.setAttribute('value', '";
                  echo $search_request->getParameter('search_id','');
                  echo "');
                  form.appendChild(myvar);";
                  }
        ?>
		//pagesize seems useless
		 myvar2= document.createElement('input');
		 myvar2.setAttribute('name','pagesize');
		 myvar2.setAttribute('value','10');
		 form.appendChild(myvar2);
		 
		 var tmpCurrentPage=$("#h_current_page").val();
		 myvar3= document.createElement('input');
		 myvar3.setAttribute('name','current_page');
		 myvar3.setAttribute('id','current_page');
		 myvar3.setAttribute('value',tmpCurrentPage);
		 form.appendChild(myvar3);
		 
		 var tmpOrderBy=$("#h_order_by").val();
		 myvar4= document.createElement('input');
		 myvar4.setAttribute('name','order_by');
		 myvar4.setAttribute('id','order_by');
		 myvar4.setAttribute('value',tmpOrderBy);
		 form.appendChild(myvar4);
		 
		 
		 var tmpOrderDir=$("#h_order_dir").val();
		 myvar5= document.createElement('input');
		 myvar5.setAttribute('name','order_dir');
		 myvar5.setAttribute('id','order_dir');
		 myvar5.setAttribute('value',tmpOrderDir);
		 form.appendChild(myvar5);
 

        document.body.appendChild(form);
        form.submit();  
    
 });
 
 
 //ftheeten 2016/01/29
   $("#report_spec").click(function(event){
        
        form = document.createElement('form');
        form.setAttribute('method', 'POST');
		form.setAttribute('action', '<?php echo url_for("specimensearch/report")?>');
		form.setAttribute('target', '_blank');
		
        <?php if ($search_request->hasParameter('specimen_search_filters')){
                  echo "myvar = document.createElement('input');
                        myvar.setAttribute('name', 'specimen_search_filters');
                        myvar.setAttribute('value', '";
                  echo serialize($_POST['specimen_search_filters']);
                  echo "');
                  form.appendChild(myvar);";
                  }
        ?>
           <?php if ($search_request->hasParameter('search_id')){
                  echo "myvar = document.createElement('input');
                        myvar.setAttribute('name', 'search_id');
                        myvar.setAttribute('value', '";
                  echo $search_request->getParameter('search_id','');
                  echo "');
                  form.appendChild(myvar);";
                  }
        ?>
		//pagesize seems useless
		 myvar2= document.createElement('input');
		 myvar2.setAttribute('name','pagesize');
		 myvar2.setAttribute('value','10');
		 form.appendChild(myvar2);
		 
		 var tmpCurrentPage=$("#h_current_page").val();
		 myvar3= document.createElement('input');
		 myvar3.setAttribute('name','current_page');
		 myvar3.setAttribute('id','current_page');
		 myvar3.setAttribute('value',tmpCurrentPage);
		 form.appendChild(myvar3);
		 
		 var tmpOrderBy=$("#h_order_by").val();
		 myvar4= document.createElement('input');
		 myvar4.setAttribute('name','order_by');
		 myvar4.setAttribute('id','order_by');
		 myvar4.setAttribute('value',tmpOrderBy);
		 form.appendChild(myvar4);
		 
		 
		 var tmpOrderDir=$("#h_order_dir").val();
		 myvar5= document.createElement('input');
		 myvar5.setAttribute('name','order_dir');
		 myvar5.setAttribute('id','order_dir');
		 myvar5.setAttribute('value',tmpOrderDir);
		 form.appendChild(myvar5);
 

        document.body.appendChild(form);
        form.submit();  
    
 });
 
  $("#xml_spec").click(function(event){
  

	
	form = document.createElement('form');
        form.setAttribute('method', 'POST');
        form.setAttribute('action', '<?php echo url_for("searchspecimenws")?>');
		form.setAttribute('target', '_blank');
		<?php if ($search_request->hasParameter('specimen_search_filters')){
                  echo "myvar = document.createElement('input');
                        myvar.setAttribute('name', 'specimen_search_filters');
                        myvar.setAttribute('value', '";
                  echo serialize($_POST['specimen_search_filters']);
                  echo "');
                  form.appendChild(myvar);";
                  }
        ?>
           <?php if ($search_request->hasParameter('search_id')){
                  echo "myvar = document.createElement('input');
                        myvar.setAttribute('name', 'search_id');
                        myvar.setAttribute('value', '";
                  echo $search_request->getParameter('search_id','');
                  echo "');
                  form.appendChild(myvar);";
                  }
        ?>
		
		 //pagesize seems useless
		 myvar2= document.createElement('input');
		 myvar2.setAttribute('name','pagesize');
		 myvar2.setAttribute('value','10');
		 form.appendChild(myvar2);
		 
		 var tmpCurrentPage=$("#h_current_page").val();
		 myvar3= document.createElement('input');
		 myvar3.setAttribute('name','current_page');
		 myvar3.setAttribute('id','current_page');
		 myvar3.setAttribute('value',tmpCurrentPage);
		 form.appendChild(myvar3);
		 
		 var tmpOrderBy=$("#h_order_by").val();
		 myvar4= document.createElement('input');
		 myvar4.setAttribute('name','order_by');
		 myvar4.setAttribute('id','order_by');
		 myvar4.setAttribute('value',tmpOrderBy);
		 form.appendChild(myvar4);
		 
		 
		 var tmpOrderDir=$("#h_order_dir").val();
		 myvar5= document.createElement('input');
		 myvar5.setAttribute('name','order_dir');
		 myvar5.setAttribute('id','order_dir');
		 myvar5.setAttribute('value',tmpOrderDir);
		 form.appendChild(myvar5);
		 
		 
		 
		//alert(tmpOrderBy);
		//alert(tmpOrderDir);
 

        document.body.appendChild(form);
        form.submit();  
  
        
  });
 
 
 
});
	  </script>

	</div>
</div>
