<table class="catalogue_widget_view">
  <thead>
    <tr>
      <th><?php echo $form['ig_num']->renderLabel() ?></th>
      <th><?php echo $form['ig_from_date']->renderLabel(); ?></th>
      <th><?php echo $form['ig_to_date']->renderLabel(); ?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php echo $form['ig_num']->render() ?></td>
      <td><?php echo $form['ig_from_date']->render() ?></td>
      <td><?php echo $form['ig_to_date']->render() ?></td>
    </tr>
  </tbody>
   <script type="text/javascript">
		//ftheeten 2016 11 23   
                
                $.reverse_year_in_select("#specimen_search_filters_ig_from_date_year");
                $.reverse_year_in_select("#specimen_search_filters_ig_to_date_year");
            
        </script>
</table>
 
