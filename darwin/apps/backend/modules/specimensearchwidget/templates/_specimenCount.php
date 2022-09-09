<table>
<!--ftheeten 2016 06 22-->
  <tr>
    <td></td>
    <td><b>from</b></td>
    <td><b>to</b></td>
  <tr>
  <tr>
	<th><?php echo $form['specimen_count_min']->renderLabel();?></th>
	<td><?php echo $form['specimen_count_min']->render() ?></td>
    <td><?php echo $form['specimen_count_max']->render() ?></td>
  </tr>


<!--ftheeten 2016 06 22-->
  <tr>
	<th><?php echo $form['specimen_count_males_min']->renderLabel();?></th>
	<td><?php echo $form['specimen_count_males_min']->render() ?></td>
    <td><?php echo $form['specimen_count_males_max']->render() ?></td>
  </tr>


<!--ftheeten 2016 06 22-->
  <tr>
	<th><?php echo $form['specimen_count_females_min']->renderLabel();?></th>
	<td><?php echo $form['specimen_count_females_min']->render() ?></td>
    <td><?php echo $form['specimen_count_females_max']->render() ?></td>
  </tr>

<!--ftheeten 2016 06 22-->
  <tr>
	<th><?php echo $form['specimen_count_juveniles_min']->renderLabel();?></th>
	<td><?php echo $form['specimen_count_juveniles_min']->render() ?></td>
    <td><?php echo $form['specimen_count_juveniles_max']->render() ?></td>
  </tr>

 


</table>