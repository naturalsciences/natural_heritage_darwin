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
          <input id="submit_btn" class="search_submit" type="submit" name="add" value="<?php echo __('Add'); ?>" />
      </td>
    </tr>
    <?php foreach($fields as $field => $name) : ?>
      <?php if(isset($fields_options[$field]['second_line'])) : ?>
        <tr class="<?php echo $form[$field]->renderId().((isset($model_name))?'_'.$model_name:'');?>"><th colspan="<?php echo count($fields)+2-$fields_at_second_line ; ?>"><?php echo $form[$field]->renderLabel() ; ?></th></tr>
        <tr class="<?php echo $form[$field]->renderId().((isset($model_name))?'_'.$model_name:'');?>">
          <td colspan="<?php echo count($fields)+2-$fields_at_second_line ; ?>"><?php echo $form[$field]->render() ; ?></td>
        </tr>
      <?php endif; ?>
    <?php endforeach; ?>
  </table>
</form>

<script language="javascript">
  $(document).ready(function() {
    $('#submit_btn').click(function(event) {
      event.preventDefault() ;
      var catalogue_get_param = '';
      var target_url = $('#report_form').attr('action');
      var target_url_get_params = '?';
      if($("#reports_catalogue_type") != 'undefined') {
        catalogue_get_param = 'catalogue='+encodeURIComponent($("#reports_catalogue_type").val());
      }
      if (target_url.indexOf('?') > -1) {
        target_url_get_params = '&';
      }
      $.ajax({
        type: "POST",
        url: target_url + target_url_get_params + catalogue_get_param,
        data: $('#report_form').serialize(),
        success: function(html) {
          $(".report_form").html(html);
          if($("ul.error_list").length == 0 || $("td#report_successfuly_added").length > 0) {
            refresh_reports();
            // @Todo Make the function called on change generic so it's not duplicated code - already on indexSuccess.php
            $('#report_list').off("change").val('').on("change",
              function (event) {
                event.preventDefault();
                if ($(this).val() != '') {
                  $.ajax({
                    type: "POST",
                    url: '<?php echo url_for("report/getReport") ; ?>',
                    data: 'name=' + $(this).val(),
                    success: function (html) {
                      $(".report_form").html(html);
                    }
                  });
                  $(".report_form").html('<img src="/images/loader.gif" />');
                }
                $(".report_form").html('');
              }
            );
          }
        }
      });
    });
  }) ;
</script>