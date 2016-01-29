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
    $("<?php if(! isset($container)) echo ".results_container"; else echo $container;?>").pager({});
  });
  </script>
<?php endif; ?>
