<table class="catalogue_widget_view">
  <thead>
    <tr>
      
      <th><?php echo $form['in_loan']->renderLabel() ?></th>
       <th><?php echo $form['loan_is_closed']->renderLabel() ?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php echo $form['in_loan']->render() ?></td>
      <td><?php echo $form['loan_is_closed']->render() ?></td>
    </tr>
  </tbody>
</table>