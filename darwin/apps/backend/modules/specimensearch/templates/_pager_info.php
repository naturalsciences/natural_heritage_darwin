<?php if(isset($pagerLayout) && isset($form['rec_per_page'])): ?>
    <div class="pager paging_info">
      <table>
        <tr>
          <td><?php echo image_tag('info2.png');?></td>
          <td>
	    <?php echo format_number_choice('[0]No Results Retrieved|[1]Your query retrieved 1 record|(1,+Inf]Your query retrieved: <br/> %1% specimen parts<br/> %2% distinct database records<br/> %3% distinct database I.G.<br> Between %4% and %5% physical specimens', array('%1%' =>  $pagerLayout->getPager()->getNumResults(), '%2%' =>  $pagerLayout->getPager()->additional_count["distinct_records"], '%3%' =>  $pagerLayout->getPager()->additional_count["count_ig"], '%4%' =>  $pagerLayout->getPager()->additional_count["sum_specimen_count_min"], '%5%' =>  $pagerLayout->getPager()->additional_count["sum_specimen_count_max"]),  $pagerLayout->getPager()->getNumResults()) ?>
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
