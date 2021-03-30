<div>
<table id="code_search" class="full_size">
  <thead>
    <tr>
      <td colspan="6">
		Boolean Selector:<?php echo $form['code_boolean'];?></td>
     </td>    
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
</div>
<div>
<table class="full_size">
    <thead>
     <!--ftheeten 2018 11 22--->
        <tr>
            <th><?php echo $form['codes_list']->renderLabel();?> : </th>
            <th style="font-style: italic;"><?php echo $form['exact_codes_list']->renderLabel();?> :</th>
			<th style="font-style: italic;"><?php echo $form['uuid']->renderLabel();?> :</th>
          </tr>
     </thead>
     <tbody>
	     <tr><td>Enter ";" to provide several values (with trailing ";")</td> </tr>
        <tr>
           <td><select name="select2_codes" id="select2_codes" multiple="multiple"></select><?php echo $form['codes_list'];?></td>        
            <td><?php echo $form['exact_codes_list'];?></td>
			<td><?php echo $form['uuid'];?></td> 			
        </tr>
      <!---->
  </tbody>
</table>
</div>
 
<script  type="text/javascript">



function checkBetween()
{
  if( $('#code_search tbody .between_col:visible').length)
  {
    $('#code_search thead .between_col').show();
    //console.log("between");
    //$('#code_search thead .autocomplete_for_code').hide();
  }
  else
  {
    $('#code_search thead .between_col').hide();
    //    console.log("regular");
    //$('#code_search thead .autocomplete_for_code').show();
  }
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
        $('#code_search > tbody tr:not(.and_row):last .standard_code_col').show();
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
    console.log("SHOW");
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
    console.log("HIDE");
    tr.find('.autocomplete_for_code').hide();
    $(this).hide();
    //table = tr.closest('table');    
    //table.find('.standard_code_col').hide();
    
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
  
  var getCodeUrl=function()
       {
            return "<?php echo(url_for('catalogue/codesAutocomplete?'));?>";
       };
       
       $('#select2_codes').select2({
				width: "100%",
                minimumInputLength : 1,
				tags: true,
				tokenSeparators: [';'],
				  ajax: {
				    //url: getCodeUrl(),
					delay: 1000,
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
