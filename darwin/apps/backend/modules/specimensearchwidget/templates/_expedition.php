<table class="catalogue_widget_view">
  <thead>
    <tr>
      <th><?php echo $form['expedition_name']->renderLabel() ?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><div>* as wildcard (begin and end). eg : *polar*</div><?php echo $form['expedition_name']->render() ?></td>
    </tr>
  </tbody>
</table>
<script type="text/javascript">
//ftheeten 2018 08 09
          var url_expedition="<?php echo(url_for('catalogue/expeditionsAutocomplete?'));?>";
          var autocomplete_rmca_array=Array();
          $('.autocomplete_for_expeditions').autocomplete({
                source: function (request, response) {
                    $.getJSON(url_expedition, {
                                term : request.term
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
</script>
