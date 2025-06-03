<?php echo form_tag('gtucountry/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition'));?>

<?php if (!$form->getObject()->isNew()): ?>
	<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
<table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
	  <tr>
		 <th class="top_aligned"><?php echo $form['iso3166']->renderLabel() ?></th>
		<td>
          <?php echo $form['iso3166']->renderError() ?>
          <?php echo $form['iso3166'] ?>
        </td>
	  </tr>
	  <tr>
		 <th class="top_aligned"><?php echo $form['name_en']->renderLabel() ?></th>
		<td>
          <?php echo $form['name_en']->renderError() ?>
          <?php echo $form['name_en'] ?>
        </td>
	  </tr>
	  <tr>
		 <th class="top_aligned"><?php echo $form['lang_name_1']->renderLabel() ?></th>
		<td>
          <?php echo $form['lang_name_1']->renderError() ?>
          <?php echo $form['lang_name_1'] ?>
        </td>
		<th class="top_aligned"><?php echo $form['name_1']->renderLabel() ?></th>
		<td>
          <?php echo $form['name_1']->renderError() ?>
          <?php echo $form['name_1'] ?>
        </td>
	  </tr>
	   <tr>
		 <th class="top_aligned"><?php echo $form['lang_name_2']->renderLabel() ?></th>
		<td>
          <?php echo $form['lang_name_2']->renderError() ?>
          <?php echo $form['lang_name_2'] ?>
        </td>
		<th class="top_aligned"><?php echo $form['name_2']->renderLabel() ?></th>
		<td>
          <?php echo $form['name_2']->renderError() ?>
          <?php echo $form['name_2'] ?>
        </td>
	  </tr>
	  <tr>
		 <th class="top_aligned"><?php echo $form['lang_name_3']->renderLabel() ?></th>
		<td>
          <?php echo $form['lang_name_3']->renderError() ?>
          <?php echo $form['lang_name_3'] ?>
        </td>
		<th class="top_aligned"><?php echo $form['name_3']->renderLabel() ?></th>
		<td>
          <?php echo $form['name_3']->renderError() ?>
          <?php echo $form['name_3'] ?>
        </td>
	  </tr>
	   <tr>
		 <th class="top_aligned"><?php echo $form['lang_name_4']->renderLabel() ?></th>
		<td>
          <?php echo $form['lang_name_4']->renderError() ?>
          <?php echo $form['lang_name_4'] ?>
        </td>
		<th class="top_aligned"><?php echo $form['name_4']->renderLabel() ?></th>
		<td>
          <?php echo $form['name_4']->renderError() ?>
          <?php echo $form['name_4'] ?>
        </td>
	  </tr>
	  
	  <tr>
		 <th class="top_aligned"><?php echo $form['from_date']->renderLabel() ?></th>
		<td>
          <?php echo $form['from_date']->renderError() ?>
          <?php echo $form['from_date'] ?>
        </td>
		<th class="top_aligned"><?php echo $form['to_date']->renderLabel() ?></th>
		<td>
          <?php echo $form['to_date']->renderError() ?>
          <?php echo $form['to_date'] ?>
        </td>
	  </tr>
	 <tr>
		 <th class="top_aligned"><?php echo $form['lang_historical_name_1']->renderLabel() ?></th>
		<td>
          <?php echo $form['lang_historical_name_1']->renderError() ?>
          <?php echo $form['lang_historical_name_1'] ?>
        </td>
		<th class="top_aligned"><?php echo $form['historical_name_1']->renderLabel() ?></th>
		<td>
          <?php echo $form['historical_name_1']->renderError() ?>
          <?php echo $form['historical_name_1'] ?>
        </td>
	  </tr>
	   <tr>
		 <th class="top_aligned"><?php echo $form['lang_historical_name_2']->renderLabel() ?></th>
		<td>
          <?php echo $form['lang_historical_name_2']->renderError() ?>
          <?php echo $form['lang_historical_name_2'] ?>
        </td>
		<th class="top_aligned"><?php echo $form['historical_name_2']->renderLabel() ?></th>
		<td>
          <?php echo $form['historical_name_2']->renderError() ?>
          <?php echo $form['historical_name_2'] ?>
        </td>
	  </tr>
	  <tr>
		 <th class="top_aligned"><?php echo $form['lang_historical_name_3']->renderLabel() ?></th>
		<td>
          <?php echo $form['lang_historical_name_3']->renderError() ?>
          <?php echo $form['lang_historical_name_3'] ?>
        </td>
		<th class="top_aligned"><?php echo $form['historical_name_3']->renderLabel() ?></th>
		<td>
          <?php echo $form['historical_name_3']->renderError() ?>
          <?php echo $form['historical_name_3'] ?>
        </td>
	  </tr>
	   <tr>
		 <th class="top_aligned"><?php echo $form['lang_historical_name_4']->renderLabel() ?></th>
		<td>
          <?php echo $form['lang_historical_name_4']->renderError() ?>
          <?php echo $form['lang_historical_name_4'] ?>
        </td>
		<th class="top_aligned"><?php echo $form['historical_name_4']->renderLabel() ?></th>
		<td>
          <?php echo $form['historical_name_4']->renderError() ?>
          <?php echo $form['historical_name_4'] ?>
        </td>
	  </tr>
	   <th class="top_aligned"><?php echo $form['comments']->renderLabel() ?></th>
		<td>
          <?php echo $form['comments']->renderError() ?>
          <?php echo $form['comments'] ?>
        </td>
	  </tr>
	 </tbody>
</table>	 
<table>
    <tfoot>
      <tr>
        <td>
          <?php echo $form->renderHiddenFields(true) ?>

         
           
			<input id="submit" type="submit" value="<?php echo __('Save');?>" />
           
         

          &nbsp;<a href="<?php echo url_for('gtucountry/index') ?>"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to(__('Delete'), 'gtucountry/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
         
        </td>
      </tr>
    </tfoot>
  </table>
</form>
