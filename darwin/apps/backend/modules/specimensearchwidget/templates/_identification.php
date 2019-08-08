<table>
 <thead>
    <tr>
      <td>
        
      </td>
      <td>&nbsp;</td>
    </tr>
  </thead>
  <tbody>
		<tr><th style="width:200px"><?php echo $form['identification_notion_concerned']->renderLabel();?></th><td><?php echo $form['identification_notion_concerned'];?></td></tr>
		<tr><th style="width:200px"><?php echo $form['identification_value_defined']->renderLabel();?></th><td><?php echo $form['identification_value_defined'];?></td></tr>
  <tbody>
</table>
<script type="text/javascript">
$(document).ready(function () {

  
  var url_mineral_identification="<?php echo(url_for('catalogue/identificationAutocomplete?'));?>";
          var autocomplete_rmca_array=Array();
          $('.autocomplete_identification_value').autocomplete({
                source: function (request, response) {
					var notion=$('#specimen_search_filters_identification_notion_concerned').val();
                    $.getJSON(url_mineral_identification, {
                                term : request.term,
								notion : notion
                            } , 
                            function (data) 
                                {
                            response($.map(data, function (value, key) {
                            return value;
                            }));
                    });
                },
                minLength: 2,
                delay: 200
    });

});
</script>