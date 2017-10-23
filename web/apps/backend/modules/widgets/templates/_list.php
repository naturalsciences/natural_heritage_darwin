<?php
if(isset($eid) && $eid != 0) $record_ref = "&eid=".$eid;
else $record_ref = "";

if(!isset($table)) $table = '';
else $table = '&table='.$table;
if(isset($view)) $read_only = '&view=true' ;
else $read_only = '' ;
$other_query = $table;
if(isset($query_options))
{
  $other_query = http_build_query($query_options->getRawValue());
  if($other_query != '') $other_query = '&'.$other_query;
}
?>
<script type="text/javascript">

$(document).ready(function () {
  $('body').widgets_screen(
  {
    change_order_url: '<?php echo url_for('widgets/changeOrder?category='.$category.$record_ref);?>',
    change_status_url: '<?php echo url_for('widgets/changeStatus?category='.$category.$record_ref);?>',
    reload_url: '<?php echo url_for('widgets/reloadContent?category='.$category.$record_ref.$other_query);?>',
    position_url: '<?php echo url_for('widgets/getWidgetPosition?category='.$category.$record_ref);?>',
    collection_img_up: '<?php echo  image_path('widgets_expand_up_button.png');?>',
    collection_img_down: '<?php echo  image_path('widget_expand_button.png');?>'
  });
});
</script>

<?php use_stylesheet('widgets.css'); ?>
<?php use_javascript('widgets.js'); ?>
<input type="hidden" id="refreshed" value="no">
<script type="text/javascript">
onload=function(){
var e=document.getElementById("refreshed");
if(e.value=="no")e.value="yes";
else{e.value="no";location.reload();}
}
</script>
<div class="widget_collection_global">
	<div class="widget_collection_container">
	  <?php $has_one_visible = false;?>
	  <?php foreach($widgets as $widget):?>
	    <?php if(! $widget->getVisible()) $has_one_visible = true;?>
            <div class="widget_preview <?php if($widget->getVisible()) echo 'hidden';?>" id="boardprev_<?php echo $widget->getGroupName();?>">
              <a href="<?php echo url_for('widgets/addWidget?widget='.$widget->getGroupName()."&category=".$category.$record_ref.$table.$other_query.$read_only);?>" alt="<?php echo $widget->getGroupName();?>">
                <?php echo __($widget->getTitlePerso());?>
              </a>
            </div>

	  <?php endforeach;?>
    <p class="clear">&nbsp;</p>
	</div>
	<div class="widget_collection_top">
	</div>
	<div class="widget_collection_button"><a href="#"><?php echo image_tag('widget_expand_button.png','alt=Expand');?></a></div>
</div>
