<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Language');?></th>
      <th><?php echo __('Is Mothertongue');?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($langs as $lang):?>
  <tr>
    <td>
      <?php echo format_language($lang->getLanguageCountry());?>
      <?php if($lang->getPreferredLanguage()):?>
	(<?php echo __('Preferred');?>)
      <?php endif;?>
    </td>
    <td>
       <?php if($lang->getMother()):?>
	<?php echo __('Yes');?>
       <?php endif;?>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
