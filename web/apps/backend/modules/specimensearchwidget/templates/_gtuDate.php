  <div class="container">
    <table id="gtu_search">
      <thead>
	  <!--added ftheeten RMCA 2016 07 08-->
    
      </thead>
      <tbody>
       
        <tr>
          <th><?php echo $form['gtu_from_date']->renderLabel(); ?></th>
          <th><?php echo $form['gtu_to_date']->renderLabel(); ?></th>
          <th colspan="2"></th>
        </tr>
        <tr>
          <td><?php echo $form['gtu_from_date']->render() ?></td>
          <td><?php echo $form['gtu_to_date']->render() ?></td>
          <td colspan="2"></td>
        </tr>
        
      </tbody>
      <script type="text/javascript">
		//ftheeten 2016 11 23   
                
                $.reverse_year_in_select("#specimen_search_filters_gtu_from_date_year");
                $.reverse_year_in_select("#specimen_search_filters_gtu_to_date_year");
            
        </script>
    </table>
    
  </div>