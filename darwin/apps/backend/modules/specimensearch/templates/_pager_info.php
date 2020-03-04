<?php if(isset($pagerLayout) && isset($form['rec_per_page'])): ?>
    <div class="pager paging_info">
      <table>
        <tr>
          <td><?php echo image_tag('info2.png');?></td>
          <td>
	    <?php echo format_number_choice('[0]No Results Retrieved|[1]Your query retrieved  1 record|(1,+Inf]Your query retrieved : <br/>  &#8226; %1% records<br/>   &#8226; %2% distinct records based on I.G. and main collection tag)<br/>   &#8226; %3% physical records (minimal estimate) <br/>  &#8226; %4% physical records (maximal estimate) ', array('%1%' =>  $pagerLayout->getPager()->getNumResults(),  '%2%' =>  $pagerLayout->getPager()->additional_count["count_ig"],  '%3%' =>  $pagerLayout->getPager()->additional_count["count_min"],  '%4%' =>  $pagerLayout->getPager()->additional_count["count_max"]),  $pagerLayout->getPager()->getNumResults()) ?>
	  </td>
          <td><ul><li><?php echo $form['rec_per_page']->renderLabel(); echo $form['rec_per_page']->render(); ?></li></ul></td>
        </tr>
      </table>
    </div>

  <script type="text/javascript">
  $(document).ready(function () {
    $("<?php if(! isset($container)) echo ".results_container"; else echo $container;?>").pager({});
  });
  </script>
<?php endif; ?>
