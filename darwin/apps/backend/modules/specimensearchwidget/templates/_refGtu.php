  <div class="container">
    <table id="gtu_search">
      <thead>
        <tr>
          <th colspan="4"><?php echo $form['gtu_code']->renderLabel() ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="4"><?php echo $form['gtu_code']->render() ?></td>
        </tr>
        <tr>
          <th><?php echo $form['gtu_from_date']->renderLabel(); ?></th>
          <th><div class="to_date_group"><?php echo $form['gtu_to_date']->renderLabel(); ?></div></th>
          <th colspan="2"><?php echo __("Precise date"); ?></th>
         
        </tr>
        <tr>
          <td><?php echo $form['gtu_from_date']->render() ?></td>
          <td><div class="to_date_group"><?php echo $form['gtu_to_date']->render() ?><div></td>
          <td colspan="2"><?php echo $form['gtu_from_precise']->render() ?></td>
          
        </tr>
        <tr>
          <th colspan="3"><?php echo $form['tags']->renderLabel() ?><?php print(__(" (use * to find part of words. <br/>EG. : - '*mer*' will match 'Erpe-Mere', 'Merksplas', 'Mer du Nord', etc...<br/> - 'mer' will match the word 'mer' like in 'Mer du Nord', 'Mer Egée', 'Mer Méditerranée' etc...)"))?></th>
          <th colspan="1"></th>
        </tr>
        <?php foreach($form['Tags'] as $i=>$form_value):?>
          <?php include_partial('specimensearch/andSearch',array('form' => $form['Tags'][$i], 'row_line'=>$i));?>
        <?php endforeach;?>
        <tr class="and_row">
          <td colspan="3"></td>
          <td><a href="<?php echo url_for('specimensearch/andSearch');?>" class="and_tag"><?php echo image_tag('add_blue.png');?></a><?php echo $form['tag_boolean']->render(); ?></td>
        </tr>
      </tbody>
    </table>
    <script  type="text/javascript">
      var num_fld = 1;
      $('.and_tag').click(function()
      {
        hideForRefresh('#refGtu');
        $.ajax({
          type: "GET",
          url: $(this).attr('href') + '/num/' + (num_fld++) ,
          success: function(html)
          {
            $('table#gtu_search > tbody .and_row').before(html);
            showAfterRefresh('#refGtu');
          }
        });
        return false;
      });    

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
  </div>