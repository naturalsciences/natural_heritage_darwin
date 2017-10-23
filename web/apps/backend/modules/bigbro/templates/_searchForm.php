<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

  <?php echo form_tag('bigbro/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'search_form','id'=>'bigbro_filter'));?>
  <div class="container">
    <table class="search" id="<?php echo ($is_choose)?'search_and_choose':'search' ?>">
      <thead>
        <tr>
          <th><?php echo $form['from_date']->renderLabel() ?></th>
          <th><?php echo $form['to_date']->renderLabel() ?></th>
          <th><?php echo $form['user_ref']->renderLabel(); ?></th>
          <th><?php echo $form['referenced_relation']->renderLabel(); ?></th>
          <th><?php echo $form['action']->renderLabel(); ?></th>
          <th><?php echo $form['record_id']->renderLabel(); ?></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['from_date']->render() ?></td>
          <td><?php echo $form['to_date']->render() ?></td>
          <td><?php echo $form['user_ref']->render() ?></td>
          <td><?php echo $form['referenced_relation']->render() ?></td>
          <td><?php echo $form['action']->render() ?></td>
          <td><?php echo $form['record_id']->render() ?></td>
          <td><input class="search_submit" type="submit" name="search" value="<?php echo __('Search'); ?>" /></td>
        </tr>
      </tbody>
    </table>
    <div class="search_results">
      <div class="search_results_content">
      </div>
    </div>
  </div>
</form>
