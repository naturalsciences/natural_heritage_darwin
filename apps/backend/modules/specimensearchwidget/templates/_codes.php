<table id="code_search" class="full_size">
  <thead>
    <tr>
      <th><?php echo __('Category');?></th>
      <th colspan="2"></th>
      <th class="between_col"><?php echo __('Between');?></th>
      <th class="between_col"><?php echo __('and');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($form['Codes'] as $i => $code):?>
      <?php include_partial('specimensearchwidget/codeline',array('code' => $code,'row_line'=>$i));?>
    <?php endforeach;?>
      <tr class="and_row">
        <td colspan="2"></td>
        <td colspan="5"><?php echo image_tag('add_blue.png'). link_to(__('Add code'),'specimensearch/addCode', array('class'=>'add_search_code'));?></td>
      </tr>
  </tbody>
</table>
<script  type="text/javascript">



function checkBetween()
{
  if( $('#code_search tbody .between_col:visible').length)
    $('#code_search thead .between_col').show();
  else
    $('#code_search thead .between_col').hide();
}

$(document).ready(function () {

  var num_fld = $('#code_search tbody tr').length;
  $('.add_search_code').click(function(event)
  {
    hideForRefresh('#codes');
    event.preventDefault();
    $.ajax({
      type: "GET",
      url: $(this).attr('href') + '/num/' + (num_fld++) ,
      success: function(html)
      {
        $('#code_search > tbody .and_row').before(html);
        $('#code_search > tbody tr:not(.and_row):last .between_col').hide();
        showAfterRefresh('#codes');
      }
    });
  });

  $('#code_search .code_between.prev').live('click',function (event)
  {
    event.preventDefault();
    tr = $(this).closest('tr');
    tr.find('.next').show();
    tr.find('.between_col').hide();
    checkBetween();
    $(this).hide();
  })

  $('#code_search .code_between.next').live('click',function (event)
  {
    event.preventDefault();
    tr = $(this).closest('tr');
    tr.find('.prev').show();
    tr.find('.between_col').show();
    $('#code_search thead .between_col').show();
    $(this).hide();
  })

  $('#code_search tbody tr').each(function(i)
  {
    if($(this).find('.between_col:first input').val() =='' && $(this).find('.between_col:last input').val() == '')
      $(this).find('.prev').click();
  });
  checkBetween();
  
    //ftheeten 2015 06 08
  //autocomplete for codes number
  /*
  $('.col_check').change(
		function(i)
		{
				autocomplete_rmca_array=$('.col_check:checked').map(function(){
				return $(this).val();
				}).get();
			
		}
  );*/

});
</script>
