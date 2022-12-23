<?php slot('title', __('Specimens Search Result'));  ?>
<?php use_javascript('double_list.js');?>

<?php include_partial('result_cols', array('columns' => $columns, 'field_to_show' => $field_to_show,  'arrayDisplay' => $arrayDisplay));?>

<div class="encoding">
  <?php include_stylesheets_for_form($form) ?>
  <?php include_javascripts_for_form($form) ?>
  <div class="page" id="search_div">
    <h1 id="title"><?php echo __('Specimens Search Result');?></h1>
    <?php echo form_tag('specimensearch/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'specimensearch_form','id'=>'specimen_filter'));?>
      <!--ftheeten 2018 09 28-->
        <?php include_partial('widgets/float_button_search'); ?>
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
	  <!--jmherpers 2018 06 04-->
	  	<?php if(!isset($is_pinned_only_search) && ! $is_specimen_search):?>
			<input type="button" id="criteria_butt" class="save_search" value="<?php echo __('Back to criteria'); ?>">
		<?php elseif(! isset($is_pinned_only_search) && $is_specimen_search):?>
			<input type="button" id="del_from_spec" class="save_search" value="<?php echo __('Remove selected'); ?>">
		<?php endif;?>
				  
		<!-- added by Son -->
		<?php if($sf_user->isAtLeast(Users::ENCODER)):?>
			<input type="button" id="print_spec" class="save_search" value="<?php echo __('Print');?>" />
			<!-- end added code -->
			<!-- added by JMHerpers 2018/01/18-->
			<?php if($sf_user->isAtLeast(Users::ENCODER)):?>
				<input type="button" id="print_spec_thermic" class="save_search" value="<?php echo __('Thermic print');?>" /> 
			<?php endif;?>			
		<?php endif;?>
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
			 
			 //JMHerpers 2018/01/18

			//var url_printer="<?php echo(sfConfig::get('dw_url_thermic_printer')); ?>";
			var collect_to_print_thermic="<?php echo(sfConfig::get('dw_collect_to_print_thermic')); ?>";
			console.log(collect_to_print_thermic);
            var collect_array = collect_to_print_thermic.split(","); 
			
			$("#print_spec_thermic").click(function(event){
				
					
						var classes = [];
						var pass = false;
						var pass2 = false;
						var collect = false;
						var tmpArray=Array();

						//var url_printer_full=url_printer+'?op=on&id='+tmpArray.join("|");
						$('.spec_results > tbody > tr ').each(function(){
							$($(this).attr('class').split(' ')).each(function() {
								if (this.length>0 && $.inArray(this.valueOf(), classes) === -1) {
									if (this.valueOf().substring(0, 4) == 'rid_' ) {	
										collect = false;
										var id_spec=this.valueOf().match(/[0-9]+/g);
										id_spec=id_spec[0];
										
										var coll = $('.'+this.valueOf()).children('.col_collection').children('.Collid').val();
										var coll_list = "<?php 	$collist = sfConfig::get('dw_collect_to_print_thermic');
																$cols = explode(",", $collist);
																$collstr = "";
																foreach ($cols as $c) {
																	$q = Doctrine_Query::create()
																		->select('*')
																		->from('Collections')
																		->where('id = ?',$c);
																	$result =$q->FetchOne();
																	$collstr = $collstr.",".$result->getName();
																}
																echo($collstr);	?>";
										var i;
										/*if(jQuery.inArray(coll, collect_array) == -1){
											var collect = true;
										}*/
										/*if (collect == true && pass == false ) {
											alert("Attention, only specimen from "+coll_list.substring(1)+" will be printed");
											pass = true;
										}*/
										if (collect == false) {
											if (pass2 == false ) {
												alert("Labels are sent to thermic printer");
											}
											tmpArray.push(id_spec);									
											pass2 = true;
										}
										collect = false;
									}
								}    
							});	
						});
						var url_printer_full="<?php echo url_for('specimensearch/averyDennisonPrinterCall');?>?id="+tmpArray.join('_');
							console.log(url_printer_full);				
						if (tmpArray.join('_') != "" ){
							$.ajax({
								url: url_printer_full												
							}).done(
							function()	{}
							);
						}
					
				

	
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

					document.body.appendChild(form);
					form.submit();  
			  
					
			  });
			 
			 
			 
			});
	  </script>

	</div>
</div>
