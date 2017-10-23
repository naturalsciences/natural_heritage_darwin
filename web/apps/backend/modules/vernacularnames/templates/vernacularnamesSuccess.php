<?php include_javascripts_for_form($form) ?>
<div id="vernacular_screen">
  <?php echo form_tag('vernacularnames/vernacularnames?table='.$sf_request->getParameter('table').'&id='.$sf_request->getParameter('id') , array('class'=>'edition qtiped_form'));?>
  <?php echo $form->renderGlobalErrors() ?>
  <table class="encoding property_values">
    <thead>
      <tr>
        <th><?php echo __('Community');?></th>
        <th colspan="2"><label><?php echo __('Vernacular name');?></label></th>
      </tr>
    </thead>
    <tbody id="property">
      <?php foreach($form['VernacularNames'] as $form_value):?>
        <?php include_partial('vernacular_names_values', array('form' => $form_value));?>
      <?php endforeach;?>
      <?php foreach($form['newVal'] as $form_value):?>
        <?php include_partial('vernacular_names_values', array('form' => $form_value));?>
      <?php endforeach;?>
    </tbody>
  </table>
  <div class='add_value'>
    <a href="<?php echo url_for('vernacularnames/addValue');?>/num/" id="add_prop_value"><?php echo __("Add Value") ; ?></a>
  </div>
  <table class="bottom_actions">
    <tfoot>
      <tr>
        <td>
          <a href="#" class="cancel_qtip"><?php echo __('Cancel');?></a>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>

<script  type="text/javascript">
  function addPropertyValue(event)
  {
    hideForRefresh('#vernacular_screen');
    event.preventDefault();
    $.ajax(
    {
      type: "GET",
      url: $(this).attr('href')+ (0+$('.property_values tbody#property tr').length),
      success: function(html)
      {
        $('.property_values tbody#property').append(html);
        showAfterRefresh('#vernacular_screen');
      }
    });
    return false;
  }

  $(document).ready(function () {

    $('.clear_prop').live('click', clearPropertyValue);

    $('#add_prop_value').click(addPropertyValue);

    $('form.qtiped_form').modal_screen();
  });
</script>
</div>
