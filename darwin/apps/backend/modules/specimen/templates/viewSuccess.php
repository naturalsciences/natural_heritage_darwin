<?php slot('title', __('View Specimen'));  ?>
<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'specimen','eid'=> $specimen->getId(),'view' => true)); ?>
<?php use_stylesheet('widgets.css') ?>
<?php use_javascript('widgets.js') ?>
<?php use_javascript('button_ref.js') ?>
<?php use_javascript('../barcode/jquery-barcode.js') ?>
<div class="page">
  <h3 class="spec">
  <span class="title"><?php echo __('View Specimen');?></span>
    <span class="specimen_actions">
        <?php if($sf_user->isPinned($specimen->getId(), 'specimen')) {
          $txt = image_tag('blue_pin_on.png', array('class'=>'pin_but pin_on'));
          $txt .= image_tag('blue_pin_off.png', array('class'=>'pin_but pin_off hidden'));
        }else{
          $txt = image_tag('blue_pin_on.png', array('class'=>'pin_but pin_on hidden'));
          $txt .= image_tag('blue_pin_off.png', array('class'=>'pin_but pin_off'));
        }?>
        <?php echo link_to($txt, 'savesearch/pin?source=specimen&id='.$specimen->getId(), array('class'=>'pin_link'));?>
        <?php if($hasEncodingRight):?>
          <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))), 'specimen/edit?id='.$specimen->getId()); ?>
        <?php endif;?>
		<?php if($sf_user->isAtLeast(Users::MANAGER)):?>
				<input type="button" id="print_spec_thermic" class="save_search" value="<?php echo __('Thermic print');?>" /> 
		<?php endif;?>
		<?php if($specimen->getRestrictedAccess()):?>
			<i><b> Non public</b></i>
		<?php endif;?>
    </span>
  </h3>
  <div class="encod_screen edition" id="intro">
   <div>
      <?php include_partial('widgets/screen', array(
        'widgets' => $widgets,
        'category' => 'specimenwidgetview',
        'columns' => 2,
        'options' => array('eid'=> $specimen->getId(), 'level' => 2, 'view' => true),
      )); ?>
    </div>

    <p class="clear"></p>
    <p align="right">
      &nbsp;<a class="bt_close" href="<?php echo url_for('specimensearch/index') ?>" id="spec_cancel"><?php echo __('Back');?></a>
    </p>
  </div>
</div>
<script  type="text/javascript">

$(document).ready(function () {
  $('body').catalogue({});
  $('.pin_but').click(function(e){
    e.preventDefault();
    if($(this).hasClass('pin_on'))
    {
      $(this).parent().find('.pin_off').removeClass('hidden');
      $(this).addClass('hidden') ;
      pin_status = 0;
    }
    else
    {
      $(this).parent().find('.pin_on').removeClass('hidden');
      $(this).addClass('hidden') ;
      pin_status = 1;
    }
    $.get( $(this).parent().attr('href') + '/status/' + pin_status,function (html){});
  });
  
  //printer
  $("#print_spec_thermic").click(function(event)
  {
				
				
			
					var classes = [];
					var pass = false;
					var pass2 = false;
					var collect = false;
						

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
										//if (collect == false) {
											//if (pass2 == false ) {
												alert("Labels are sent to thermic printer");
											//}
																			
											//pass2 = true;
										//}
										collect = false;
									}
								}    
							});	
						});
						var url_printer_full="<?php echo url_for('specimensearch/averyDennisonPrinterCall');?>?id="+<?php print($specimen->getId())?>;
							console.log(url_printer_full);				
						
							$.ajax({
								url: url_printer_full												
							}).done(
							function()	{
								
								alert("Labels are sent to thermic printer");
							}
							);
						
					
				

	
			});
});
</script>
