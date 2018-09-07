<?php if(isset($pagerLayout) && isset($form['rec_per_page'])): ?>
    <div class="pager paging_info">
      <table>
        <tr>
          <td><?php echo image_tag('info2.png');?></td>
          <td>
	    <?php echo format_number_choice('[0]No Results Retrieved|[1]Your query retrieved 1 record|(1,+Inf]Your query retrieved %1% records', array('%1%' =>  $pagerLayout->getPager()->getNumResults()),  $pagerLayout->getPager()->getNumResults()) ?>
	  </td>
          <td><ul><li><?php echo $form['rec_per_page']->renderLabel(); echo $form['rec_per_page']->render(); ?></li></ul></td>
        </tr>
      </table>
    </div>

  <script type="text/javascript">
  $(document).ready(function () {
    $('.publicsearch_form').find(".rec_per_page").change(function (event)
    {
      $(this).closest('form').submit();
    });

    $("a.sort").click(function (event)
    {
      event.preventDefault();
      $('#specimen_search_filters_order_by').val($(this).attr('alt'));

      if( $(this).find('.order_sign_up').length)
        $('#specimen_search_filters_order_dir').val('asc');
      else
        $('#specimen_search_filters_order_dir').val('desc');
      $(this).closest('form').submit();
    });
  
    $(".pager a").click(function (event,data)
    {
      
      if (event.which == null)
       /* IE case */
        button= (event.button < 2) ? "LEFT" : ((event.button == 4) ? "MIDDLE" : "RIGHT");
      else
        /* All others */
       button= (event.which < 2) ? "LEFT" :
                 ((event.which == 2) ? "MIDDLE" : "RIGHT");
      
      if(button == "MIDDLE"){ /*just do nothing */ return true;}
      else event.preventDefault();


      $('#specimen_search_filters_current_page').val($(this).closest('li').attr('data-page'));
      $(this).closest('form').submit();
    });
  });
  </script>
<?php endif; ?>
