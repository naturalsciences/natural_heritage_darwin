<?php slot('title', __('Search Specimens'));  ?>

<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'specimensearch')); ?>
<?php use_javascript('double_list.js');?>
<div class="encoding">
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
  <div class="page" id="search_div">
    <h1 id="title"><?php echo __('Specimens Search');?></h1>
    <?php echo form_tag('specimensearch/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'specimensearch_form','id'=>'specimen_filter'));?>
      <div class="encod_screen" id="intro">
        <?php include_partial('widgets/screen', array(
          'widgets' => $widgets,
          'category' => 'specimensearchwidget',
          'columns' => 2,
          'options' => array('form' => $form),
        )); ?>
        <p class="clear"> </p>
        <p class="form_buttons">
          <div class="edit">
            <?php echo $form['col_fields'];?>
            <?php echo $form['rec_per_page']->render(array('class'=>'hidden'));?>
            <input type="submit" name="submit" id="submit" value="<?php echo __('Search'); ?>" class="search_submit">
          </div>
      </div>
      <div class="check_right" id="save_button">
        <?php include_partial('savesearch/saveSearch');?>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function () {
  check_screen_size() ;
  $(window).resize(function(){
    check_screen_size();
  });
});
</script>
