    <p id="float_button">      
      <?php echo link_to(image_tag('previous.png'), 'specimensearch/index',array('title' => __('Cancel'))) ; ?>
      <?php if (!$form->getObject()->isNew()): ?>
        <?php echo link_to(image_tag('individual_expand.png'), 'specimen/new',array('title' => __('New specimen'))) ; ?>
        <?php echo link_to(image_tag('duplicate.png'),'specimen/new?duplicate_id='.$form->getObject()->getId(), array('title'=> __('Duplicate specimen'))) ; ?>
        <?php echo link_to(image_tag('remove.png'), 'specimen/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'),'title'=>__('Delete'))) ;?>
      <?php endif?>
      <?php echo image_tag('edit.png',array('class' => 'submit_image', 'title'=> __('Save'))) ; ?>
    </p>
    <script type="text/javascript">
    $(document).ready(function () {
      $('.submit_image').click(function() {
        $('#submit_spec_f1').trigger('click') ;
      }) ;
    });
    </script>