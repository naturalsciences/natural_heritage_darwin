<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="catalogue_methods">
<?php if(isset($notion) && (($notion == 'method' || $notion =='tool'))):?>
  <?php echo form_tag('methods_and_tools/search?notion='.$notion.( isset($is_choose) ? '&is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'methods_and_tools_filter'));?>
    <div class="container">
      <table class="search hidden" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
        <thead>
          <tr>
            <th><?php echo $form[$notion]->renderLabel() ?></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo $form[$notion]->render() ?></td>
            <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
          </tr>
        </tbody>
      </table>
      <div class="search_results">
        <div class="search_results_content">
        </div>
      </div>
      <?php if ($sf_user->isAtleast(Users::ENCODER)) : ?>        
      <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('methods_and_tools/new?notion='.$notion) ?>"><?php echo __('New');?></a></div>
      <?php endif ; ?>
    </div>
  </form>
<?php else:?>
  <?php echo __('You need to specify if you wish to work on tools or methods');?>
<?php endif;?>
</div>
<script language="javascript">
$(document).ready(function () {
  $('.catalogue_methods').choose_form({});
  $('form#methods_and_tools_filter').submit();
});
</script>
