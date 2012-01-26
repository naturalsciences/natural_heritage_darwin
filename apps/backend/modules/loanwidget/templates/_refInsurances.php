<table  class="property_values">
  <thead style="<?php echo ($form['Insurances']->count() || $form['newInsurance']->count())?'':'display: none;';?>">
    <tr>
      <th>
        <?php echo __('Date from'); ?>
      </th>      
      <th>
        <?php echo __('Date to'); ?>
      </th>   
      <th>
        <?php echo __('Value'); ?>
      </th>         
      <th>
        <?php echo __('Currency'); ?>
      </th>
      <th>
	<?php echo $form['insurance'];?>
      </th>
    </tr>
  </thead>
    <?php $retainedKey = 0;?>
    <?php foreach($form['Insurances'] as $form_value):?>
      <?php include_partial('parts/insurances', array('form' => $form_value, 'rownum'=>$retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
    <?php foreach($form['newInsurance'] as $form_value):?>
      <?php include_partial('parts/insurances', array('form' => $form_value, 'rownum'=>$retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
  <tfoot>
    <tr>
      <td colspan='5'>
        <div class="add_code">
          <a href="<?php echo url_for('loan/addInsurance?table='.$table. ($form->getObject()->isNew() ? '': '&id='.$form->getObject()->getId()) );?>/num/" id="add_insurance"><?php echo __('Add Insurance');?></a>
        </div>
      </td>
    </tr>
  </tfoot>
</table>
<script  type="text/javascript">
  $(document).ready(function () {
    $('#add_insurance').click(function()
    {
        hideForRefresh('#refInsurances');
        parent_el = $(this).closest('table.property_values');
        $.ajax(
        {
          type: "GET",
          url: $(this).attr('href')+ (0+$(parent_el).find('tbody').length),
          success: function(html)
          {
            $(parent_el).append(html);
            $(parent_el).find('thead:hidden').show();
            showAfterRefresh('#refInsurances');
          }
        });
        return false;
    });
  });
</script>
