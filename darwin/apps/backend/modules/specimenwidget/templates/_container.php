<table>
  <tr>
	<th><?php echo $form['surnumerary']->renderLabel();?></th>
	<td><?php echo $form['surnumerary']->render() ?></td>
  </tr>

  <tr>
	<th><?php echo $form['container']->renderLabel();?></th>
	<td><?php echo $form['container']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['container_type']->renderLabel();?></th>
	<td><?php echo $form['container_type']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['container_storage']->renderLabel();?></th>
	<td><?php echo $form['container_storage']->render() ?></td>
  </tr>
  <tr>
	<th><?php echo $form['sub_container']->renderLabel();?></th>
	<td><?php echo $form['sub_container']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['sub_container_type']->renderLabel();?></th>
	<td><?php echo $form['sub_container_type']->render() ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php echo $form['sub_container_storage']->renderLabel();?></th>
	<td><?php echo $form['sub_container_storage']->render() ?></td>
  </tr>
  <!--JMHerpers 18-01-2023
  <tr>
	<th class="top_aligned">-----DNA----</th>
	<td></td>
  </tr>
  <tr>
	<th class="top_aligned"><? php echo $form['DNA_box']->renderLabel();?></th>
	<td><? php echo $form['DNA_box']->render() ?></td>
  </tr>
    <tr>
	<th class="top_aligned"><? php echo $form['DNA_HPos']->renderLabel();?></th>
	<td><? php echo $form['DNA_HPos']->render() ?></td>
  </tr>
    <tr>
	<th class="top_aligned"><? php echo $form['DNA_VPos']->renderLabel();?></th>
	<td><? php echo $form['DNA_VPos']->render() ?></td>
  </tr>
    <tr>
	<th class="top_aligned"><? php echo $form['DNA_Tag']->renderLabel();?></th>
	<td><? php echo $form['DNA_Tag']->render() ?></td>
  </tr>
    <tr>
	<th class="top_aligned"><? php echo $form['DNA_Tube']->renderLabel();?></th>
	<td><? php echo $form['DNA_Tube']->render() ?></td>
  </tr>
    <tr>
	<th class="top_aligned"><? php echo $form['DNA_Notes']->renderLabel();?></th>
	<td><? php echo $form['DNA_Notes']->render() ?></td>
  </tr>-->
</table>

<script type="text/javascript">
$(document).ready(function () {
    $('select[name$="[container_type]"]').change(function() {
      parent_el = $(this).closest('.widget');
      $.get("<?php echo url_for('specimen/getStorage');?>/item/container/type/"+$(this).val(), function (data) {
              parent_el.find('select[name$="[container_storage]"]').html(data);
            });
    });

    $('select[name$="[sub_container_type]"]').change(function() {
      parent_el = $(this).closest('.widget');
      $.get("<?php echo url_for('specimen/getStorage');?>/item/sub_container/type/"+$(this).val(), function (data) {
             parent_el.find('select[name$="[sub_container_storage]"]').html(data);
            });
    });
});
</script>
