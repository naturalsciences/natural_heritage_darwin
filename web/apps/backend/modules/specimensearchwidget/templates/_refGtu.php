  <div class="container">
    <table id="gtu_search">
      <thead>
	  <!--added ftheeten RMCA 2015 01 07-->
    <tr>
      <td colspan="4">
		Boolean Selector:<?php echo $form['gtu_boolean'];?></td>
     </td>
	</tr>
        <tr>
          <th colspan="4"><?php echo $form['gtu_code']->renderLabel() ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="4"><?php echo $form['gtu_code']->render() ?></td>
        </tr>
        <tr>
          <th colspan="2"><?php echo $form['tags']->renderLabel() ?></th>
          <th colspan="2"></th>
        </tr>
        <?php foreach($form['Tags'] as $i=>$form_value):?>
          <?php include_partial('specimensearch/andSearch',array('form' => $form['Tags'][$i], 'row_line'=>$i));?>
        <?php endforeach;?>
        <tr class="and_row">
          <td colspan="3"></td>
          <td><?php echo image_tag('add_blue.png');?><a href="<?php echo url_for('specimensearch/andSearch');?>" class="and_tag"><?php echo __('And'); ?></a></td>
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
    </script>
  </div>
