<script type="text/javascript">

$(document).ready(function () {
  $('body').widgets_screen(
  {
    collection_img_up: '<?php echo  image_path('widgets_expand_up_button.png');?>',
    collection_img_down: '<?php echo  image_path('widget_expand_button.png');?>'
  });
  
});
</script>

<?php use_stylesheet('widgets.css'); ?>
<?php use_javascript('widgets.js'); ?>
<div class="widget_collection_global search_columns">
  <div class="widget_collection_container">
    <ul class="column_menu">
      <?php foreach($columns as $col_name => $col):?>
        <li>
<!-- <?php echo $field_to_show[$col_name]; ?>
<span class="check_mark">&#10003;</span>
          <span class="uncheck_mark">&#10007;</span>-->
          <label class="col_switcher">
            <input type="checkbox" value="<?php echo $col_name;?>" <?php if($field_to_show[$col_name]=='check') echo 'checked="checked"'; ?> />
            <?php echo $col[1];?>
          </label>
        </li>
      <?php endforeach;?>
    </ul>
    <div class="clear"></div>
  </div>  
  <div class="widget_collection_top">
  </div>
  <div class="widget_collection_button"><a href="#"><?php echo image_tag('widget_expand_button.png','alt=Expand');?></a></div>
</div>

<script type="text/javascript">

$(document).ready(function () {


  $(".col_switcher :checkbox").change(function(){
    el = $('.col_' + $(this).val());
    if($(this).is(':checked'))
      el.show();
    else
      el.hide();

    //Update visible column list
    $('#specimen_search_filters_col_fields').val(getSearchColumnVisibilty());
  });
  
});
</script>
