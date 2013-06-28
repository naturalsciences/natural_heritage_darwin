<table  class="property_values">
  <thead style="<?php echo ($form['Insurances']->count() || $form['newInsurances']->count())?'':'display: none;';?>">
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
      <th><?php echo $form['Insurances_holder'];?></th>
    </tr>
  </thead>
    <?php $retainedKey = 0;?>
    <?php foreach($form['Insurances'] as $form_value):?>
      <?php include_partial('specimen/insurances', array('form' => $form_value, 'rownum'=>$retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
    <?php foreach($form['newInsurances'] as $form_value):?>
      <?php include_partial('specimen/insurances', array('form' => $form_value, 'rownum'=>$retainedKey));?>
      <?php $retainedKey = $retainedKey+1;?>
    <?php endforeach;?>
  <tfoot>
    <tr>
      <td colspan='5'>
        <div class="add_code">
          <?php
          if($module == 'specimen') $url = 'specimen/addInsurance';
          if($module == 'loans') $url = 'loan/addInsurance';
          if($module == 'loan_items') $url = 'loanitem/addInsurance';
          ?>
          <a href="<?php echo url_for($url. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>/num/" id="add_insurance"><?php echo __('Add Insurance');?></a>
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
