<div>
<table id="code_search" class="full_size">
  <thead>
	<!--added ftheeten RMCA 2015 01 07-->
    <tr>
      <td colspan="6" style="text-align:center">
		Valid label:<br/><div style="display: inline-block;text-align:center"><?php echo $form['valid_label'];?></div></td>
     </td>
	</tr>
    <tr>
      <td colspan="6">
		Boolean Selector:<?php echo $form['code_boolean'];?></td>
     </td>
	</tr>
	<tr>
	   <td colspan="6">
		<div style="font-weight:bold; text-align:center; vertical-align:middle; padding-bottom:10px;">Exact match:<?php echo $form['code_exact_match'];?></div>
		</td>
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
<div>
<table class="full_size">
    <thead>
         <tr>
            <th><?php echo $form['codes_list']->renderLabel();?> : </th>
            <th style="font-style: italic;"><?php echo $form['exact_codes_list']->renderLabel();?> :</th>
         </tr>
    </thead>
    <tbody>
      <!--ftheeten 2018 11 22--->
        <tr>
            <td><select name="select2_codes" id="select2_codes" multiple="multiple"></select><?php echo $form['codes_list'];?></td>        
            <td><?php echo $form['exact_codes_list'];?></td>        
        </tr>
        <!---->
  </tbody>
</table>
</div>
<script  type="text/javascript">

var autocomplete_rmca_array=Array();
var select2_input;
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
    tr.find('.autocomplete_for_code').show();
    checkBetween();
    $(this).hide();
    table = tr.closest('table');    
    table.find('.between_col').hide();
    table.find('.standard_code_col').show();
  })

  $('#code_search .code_between.next').live('click',function (event)
  {
    event.preventDefault();
    tr = $(this).closest('tr');
    tr.find('.prev').show();
    tr.find('.between_col').show();
    $('#code_search thead .between_col').show();
    tr.find('.autocomplete_for_code').hide();
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
  
  function initCollectionCheck()
 {
				autocomplete_rmca_array=$('.col_check:checked').map(function(){
				return $(this).val();
				}).get();
			
 }
  
  $('.col_check').change(
        function()
        {
            initCollectionCheck();
        }
  );
  
    
    //ftheeten 2018 11 22
    
       var getCodeUrl=function()
       {
            return "<?php echo(url_for('catalogue/codesAutocomplete?'));?>";
       };
       
       $('#select2_codes').select2({
				width: "100%",
                minimumInputLength : 1,
				tags: true,
				tokenSeparators: ['|'],
				  ajax: {
				    //url: getCodeUrl(),
                    transport: function (params, success, failure) {                        
                       
                        if(params.data.term.length>=3)
                        {
                            var $request= $.ajax(
                                 {
                                  dataType: "json",
                                  url:  getCodeUrl(),
                                  data: {
                                        term : params.data.term,
                                        collections: autocomplete_rmca_array.join()
                                  }
                                }
                            );
                        }
                        else
                        {
                             var $request= $.ajax(
                                 {
                                  dataType: "json",
                                  url:  getCodeUrl(),                                  
                                }
                            );
                        }

                        $request.then(success);
                        $request.fail(failure);

                        return $request;
                      },
                      
					processResults: function(data) {
				       var myResults = [];
                        $.each(data, function (index, item) {
                            myResults.push({
                                'id': item.value,
                                'text': item.value
                            });
                        });
                        return {
                            results: myResults
                        };
					}
				  }
				});
       
       
       
      $("form").submit(function (e) {
                var criteria=new Array();
                $.each(
                    $('#select2_codes').select2("data"),
                    function(index, data)
                    {
                       
                        criteria.push(data.text);
                    }
                );               
                $(".select2_code_values").val(criteria.join("|"));
                
            });
            
      //back 
        <?php if( array_key_exists("specimen_search_filters", $_POST)):?>
            <?php $tmpPOST=$_POST["specimen_search_filters"]; ?>
             <?php if( array_key_exists("codes_list", $tmpPOST)):?>               
                <?php $listValues=explode("|",$tmpPOST['codes_list']);?>
                    <?php foreach( $listValues as $val):?>
                        var valueToCopy="<?php print($val); ?>";
                        if(valueToCopy.trim().length>0)
                        {
                            select2SetOption('#select2_codes', valueToCopy, valueToCopy);

                        }
                <?php endforeach;?>
             <?php endif;?>
             <?php if( array_key_exists("exact_codes_list", $tmpPOST)):?>
                <?php if(strtolower($tmpPOST["exact_codes_list"])=="on"):?>
                    $("#specimen_search_filters_exact_codes_list").prop("checked",true);
                <?php else:?>
                    $("#specimen_search_filters_exact_codes_list").prop("checked",false);
                <?php endif;?>
            <?php endif;?>        
        <?php endif;?>
        
        initCollectionCheck();
       

});
</script>
