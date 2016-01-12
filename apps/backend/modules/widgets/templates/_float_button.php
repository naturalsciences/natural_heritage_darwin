<?php if ( isset($search_module) && isset($module) && isset($save_button_id) ): ?>
    <p id="float_button">
      <?php echo link_to(image_tag('previous.png'), $search_module,array('title' => __('Cancel'))) ; ?>
      <?php if (!$form->getObject()->isNew()): ?>
        <?php if (!isset($no_new)): ?>
          <?php echo link_to(image_tag('individual_expand.png'), $module.'/new',array('title' => __('New specimen'))) ; ?>
        <?php endif; ?>
        <?php if (!isset($no_duplicate)): ?>
          <?php echo link_to(image_tag('duplicate.png'),$module.'/new?duplicate_id='.$form->getObject()->getId(), array('title'=> __('Duplicate specimen'))) ; ?>
        <?php endif; ?>
        <?php echo link_to(image_tag('remove.png'), $module.'/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'),'title'=>__('Delete'))) ;?>
        <?php if (isset($print_button_id)): ?>
          <a href="#" class="print_image" title="<?php echo __('Print');?>"><?php echo image_tag('print.png', array('title'=>__('Print'), 'alt'=>__('Print'))) ; ?></a>
        <?php endif; ?>
      <?php endif?>
      <?php echo image_tag('save.png',array('class' => 'submit_image', 'title'=> __('Save'))) ; ?>
    </p>
    <script type="text/javascript">
    $(document).ready(function () {
      $('.submit_image').click(function() {
        $("#<?php echo $save_button_id; ?>").trigger('click') ;
      }) ;
      <?php if (!$form->getObject()->isNew() && isset($print_button_id)): ?>
      $('.print_image').click(function() {
        $("#<?php echo $print_button_id; ?>").trigger('click') ;
      }) ;
      <?php endif; ?>
    });
    </script>
<?php endif; ?>
