  <div class="container">
    <table id="gtu_search">
      <thead>
	  <!--added ftheeten RMCA 2016 07 08-->
    
      </thead>
      <tbody>
       
        <tr>
          <th><?php echo $form['gtu_from_date']->renderLabel(); ?></th>
          <th><div class="to_date_group"><?php echo $form['gtu_to_date']->renderLabel(); ?></div></th>
          <th><?php echo __("Precise date"); ?></th>
          <th colspan="2"></th>
        </tr>
        <tr>
          <td><?php echo $form['gtu_from_date']->render() ?></td>
          <td><div class="to_date_group"><?php echo $form['gtu_to_date']->render() ?></div></td>
          <td><?php echo $form['gtu_from_precise']->render() ?></td>
          <td colspan="2"></td>
        </tr>        
      </tbody>
      <!--<script type="text/javascript">
		//ftheeten 2016 11 23   
                
                $.reverse_year_in_select("#specimen_search_filters_gtu_from_date_year");
                $.reverse_year_in_select("#specimen_search_filters_gtu_to_date_year");
            
        </script>-->
    </table>
    
  </div>
  <script>
    $(document).ready(
        function()
        {        
            $(".precise_gtu_date").click(
                function(e)
                {
                   
                   if($(".precise_gtu_date").is(':checked'))
                   {               
                        $("[id^=specimen_search_filters_gtu_from_date]").each( 
                            function(  )
                            {
                               
                                $(".to_date_group").hide();
                                align($("#"+ this.id));
                            }
                        );
                   }
                   else
                   {
                       $(".to_date_group").show();
                       $("[id^=specimen_search_filters_gtu_to_date]").val($("[id^=specimen_search_filters_gtu_from_date] option:first").val());
                   }
                }
            );
            
            function align(ctrl)
            {
                 if($(".precise_gtu_date").is(':checked'))
                   {                 
                        var name_ctrl=ctrl.attr("id");
                        var val_ctrl = ctrl.val();                    
                        name_ctrl=name_ctrl.replace(/\_from_date\_/g, "_to_date_");                   
                        $("#"+name_ctrl+ " option[value=" + val_ctrl +"]").attr('selected','selected');
                    }
            }
            
            $("[id^=specimen_search_filters_gtu_from_date]").change(
                function()
                {  
                    align($(this));
                }
            );
        }
        );
  </script>