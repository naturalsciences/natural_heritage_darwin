  <table>
    <tr>
      <th>
        <?php echo $form['MassActionForm']['ig_ref']['ig_ref']->renderLabel();?>
      </th>
    </tr>
    <tr>
      <td>
        <?php echo $form['MassActionForm']['ig_ref']['ig_ref']->renderError();?>
        <?php echo $form['MassActionForm']['ig_ref']['ig_ref']->render(array('class' => 'inline'));?>
      </td>
    <tr>
  </table>

  <script  type="text/javascript">
  $(document).ready(function () {
      changeSubmit(true);
  });
  $('#mass_action_MassActionForm_ig_ref_ig_ref_check').change(function(){
    if($(this).val()) 
    {
      $.ajax({
        type: 'POST',
        url: "<?php echo url_for('igs/addNew') ?>",
        data: "num="+$('#mass_action_MassActionForm_ig_ref_ig_ref_name').val(),
        success: function(html){
          $('li#toggledMsg').hide();
          $('#mass_action_MassActionForm_ig_ref_ig_ref').val(html) ;
        }
      });  
    }
  }) ;
  </script>
