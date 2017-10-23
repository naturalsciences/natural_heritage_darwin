    <?php include_stylesheets_for_form($form) ?>
    <?php include_javascripts_for_form($form) ?>
    
    <?php echo $form->renderGlobalErrors() ?>

    <table>
    <tbody>
      <tr>
            <th><?php echo $form['category']->renderLabel();?></th>
            <td>
              <?php echo $form['category']->renderError() ?>
              <?php echo $form['category'];?>
            </td>
      </tr>
      <tr>
            <th><?php echo $form['action_observation']->renderLabel();?></th>
            <td>
              <?php echo $form['action_observation']->renderError() ?>
              <?php echo $form['action_observation'];?>
            </td>
      </tr>
      <tr>
            <th><?php echo $form['modification_date_time']->renderLabel();?></th>
            <td>
              <?php echo $form['modification_date_time']->renderError() ?>
              <?php echo $form['modification_date_time'];?>
            </td>
      </tr>
      <tr>
            <th><?php echo $form['people_ref']->renderLabel();?></th>
            <td>
              <?php echo $form['people_ref']->renderError() ?>
              <?php echo $form['people_ref'];?>
            </td>
      </tr>
      <tr>
            <th><?php echo $form['description']->renderLabel();?></th>
            <td>
              <?php echo $form['description']->renderError() ?>
              <?php echo $form['description'];?>
            </td>
      </tr>
    </tbody>
   </table>
