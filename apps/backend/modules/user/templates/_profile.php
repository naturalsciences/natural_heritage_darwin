	  <tr>
	    <?php if (isset($form['title'])) : ?>
	      <th><?php echo $form['title']->renderLabel() ?></th>
	      <td>
		<?php echo $form['title']->renderError() ?>
		<?php echo $form['title'] ?>
	      </td>
	    <?php else : ?>
	      <th><?php echo $form['sub_type']->renderLabel() ?></th>
	      <td>
		<?php echo $form['sub_type']->renderError() ?>
		<?php echo $form['sub_type'] ?>
	      </td>
	    <?php endif ;?>
	  </tr>
	  <tr>
	    <th><?php echo $form['given_name']->renderLabel(isset($form['title'])?__('Given name'):__('Abreviation')) ?>
		<?php echo $form->renderGlobalErrors() ?>
	    </th>
	    <td>
	      <?php echo $form['given_name']->renderError() ?>
	      <?php echo $form['given_name'] ?>
	    </td>
	  </tr>
	  <tr>
	    <th><?php echo $form['family_name']->renderLabel(isset($form['title'])?'Family name':'Name') ?></th>
	    <td>
	      <?php echo $form['family_name']->renderError() ?>
	      <?php echo $form['family_name'] ?>
	    </td>
	  </tr>
	  <tr>
	    <th><?php echo $form['additional_names']->renderLabel() ?></th>
	    <td>
	      <?php echo $form['additional_names']->renderError() ?>
	      <?php echo $form['additional_names'] ?>
	    </td>
	  </tr>
	  <?php if (isset($form['title'])) : ?>
	  <tr>
	    <th><?php echo $form['gender']->renderLabel() ?></th>
	    <td>
	      <?php echo $form['gender']->renderError() ?>
	      <?php echo $form['gender'] ?>
	    </td>
	  </tr>
	  <?php endif ; ?>
	  <tr>
	    <th><?php echo $form['birth_date']->renderLabel() ?></th>
	    <td>
	      <?php echo $form['birth_date']->renderError() ?>
	      <?php echo $form['birth_date'] ?>
	    </td>
	  </tr> 
