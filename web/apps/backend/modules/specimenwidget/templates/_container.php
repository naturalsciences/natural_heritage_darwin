<table>
 <tr>
	<th><?php /*echo $form['surnumerary']->renderLabel();*/?></th>
	<td><?php /*echo $form['surnumerary']->render()*/ ?></td>
  </tr>

  <tr>
	<th><?php /*echo $form['container']->renderLabel();*/?></th>
	<td><?php /*echo $form['container']->render()*/ ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php /*echo $form['container_type']->renderLabel();*/?></th>
	<td><?php /*echo $form['container_type']->render()*/ ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php /*echo $form['container_storage']->renderLabel();*/?></th>
	<td><?php /*echo $form['container_storage']->render()*/ ?></td>
  </tr>
  <tr>
	<th><?php /*echo $form['sub_container']->renderLabel();*/?></th>
	<td><?php /*echo $form['sub_container']->render()*/ ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php /*echo $form['sub_container_type']->renderLabel();*/?></th>
	<td><?php /*echo $form['sub_container_type']->render()*/ ?></td>
  </tr>
  <tr>
	<th class="top_aligned"><?php /*echo $form['sub_container_storage']->renderLabel();*/?></th>
	<td><?php /*echo $form['sub_container_storage']->render()*/ ?></td>
  </tr>
</table>

<script type="text/javascript">
$(document).ready(function () {
    /*$('select[name$="[container_type]"]').change(function() {
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
    });*/
});
</script>
