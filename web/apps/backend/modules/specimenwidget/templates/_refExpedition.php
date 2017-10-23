<?php if(isset($view) && $view) : ?>
  <?php echo $form->getObject()->Expeditions->getName() ; ?>
<?php else  : ?>
 <table style="display: inline-block"><tbody><tr><td ><div ><?php echo $form['expedition_ref']->renderError() ?>
  <?php echo $form['expedition_ref']->render() ?></div></td><td><a class="widget_deleteProperty"  title="<?php echo __('Delete expedition') ?>"><?php echo image_tag('remove.png'); ?></a></td></tr></tbody></table>
        <script>
      $('a.widget_deleteProperty').click(function() {
        $("#specimen_expedition_ref_name").val("");
        $("#specimen_expedition_ref").val("");
      });
</script>
<?php endif ; ?>
