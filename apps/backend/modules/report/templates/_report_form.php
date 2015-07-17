<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php echo form_tag('report/add', array('class'=>'table_view search_form','id'=>'report_form'));?>
  <table>
    <tr>
      <?php foreach($fields as $field => $name) : ?>
        <?php if(!isset($fields_options[$field]['second_line'])) : ?>
          <th><?php echo $form[$field]->renderLabel() ; ?></th>
        <?php endif; ?>
      <?php endforeach ; ?>
      <th><?php echo $form['format']->renderLabel() ; ?></th>
      <th><?php echo $form['comment']->renderLabel() ; ?></th>
      <th></th>
    </tr>
    <tr>
      <td colspan="<?php echo count($fields)+2-$fields_at_second_line ; ?>"><?php echo $form->renderGlobalErrors() ; ?></td>
    </tr>
    <tr>
      <?php foreach($fields as $field => $name) : ?>
        <?php if(!isset($fields_options[$field]['second_line'])) : ?>
          <td><?php echo $form[$field]->renderError() ; ?></td>
        <?php endif; ?>
      <?php endforeach ; ?>
      <td><?php echo $form['format']->renderError() ; ?></td>
      <td></td>
      <td></td>

    </tr>
    <tr>
      <?php foreach($fields as $field => $name) : ?>
        <?php if(!isset($fields_options[$field]['second_line'])) : ?>
          <td><?php echo $form[$field]->render() ; ?></td>
        <?php endif; ?>
      <?php endforeach ; ?>
      <td><?php echo $form['format']->render() ; ?></td>
      <td><?php echo $form['comment']->render() ; ?></td>
      <td><?php echo $form['name']->render() ; ?>
          <input <?php echo ($fast?'':'id="submit_btn"') ?> class="search_submit" type="submit" name="add" value="<?php echo __('Add'); ?>" />
      </td>
    </tr>
    <?php foreach($fields as $field => $name) : ?>
      <?php if(isset($fields_options[$field]['second_line'])) : ?>
        <tr><th colspan="<?php echo count($fields)-$fields_at_second_line ; ?>"><?php echo $form[$field]->renderLabel() ; ?></th></tr>
        <tr><td colspan="<?php echo count($fields)-$fields_at_second_line ; ?>"><?php echo $form[$field]->render() ; ?></td></tr>
      <?php endif; ?>
    <?php endforeach; ?>
  </table>
</form>

<script language="javascript">
  $(document).ready(function() {
    $('#submit_btn').click(function(event) {
      event.preventDefault() ;
      $.ajax({
        type: "POST",
        url: $('#report_form').attr('action'),
        data: $('#report_form').serialize(),
        success: function(html) {
          $(".report_form").html(html);
          refresh_reports() ;
          $('#report_list').val('') ;
        }
      });
    });
  }) ;
</script>