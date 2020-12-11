<?php use_helper('Text');?>
<table class="catalogue_table">
  <thead>
    <tr>
      <th><?php echo __('Protocol');?></th>
      <th><?php echo __('Value');?></th>
	  <th><?php echo __('Link');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($identifiers as $identifier):?>
  <tr>
    <td>
      <a class="link_catalogue" title="<?php echo __('Edit Identifier');?>"
	  href="<?php echo url_for('user/identifier?table=users&cid='.$identifier->getId().'&id='.$eid); ?>">
	    <?php print($identifier->getProtocol());?>
      </a>
    </td>
	<td>
      <?php echo auto_link_text( nl2br( $identifier->getValue() ));?>
    </td>
	<td>
		<?php if(array_key_exists(strtolower($identifier->getProtocol()), Identifiers::getURLService())):?>
		<a target="_blank" 
	  href="<?php print(Identifiers::getURLService()[strtolower($identifier->getProtocol())].$identifier->getValue());?>">
	    <?php print(Identifiers::getURLService()[strtolower($identifier->getProtocol())].$identifier->getValue());?></a>
		<?php endif;?>
	</td>
    
    <td class="widget_row_delete">
	<?php if($sf_user->isAtLeast(Users::MANAGER)) : ?>
      <a class="widget_row_delete" href="<?php echo url_for('catalogue/deleteRelated?table=identifiers&id='.$identifier->getId());?>" title="<?php echo __('Delete Identifier') ?>"><?php echo image_tag('remove.png'); ?>
      </a>
	  <?php endif ; ?>
    </td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>

<br />
<?php if($sf_user->isAtLeast(Users::MANAGER)) : ?>
<?php echo image_tag('add_green.png');?><a title="<?php echo __('Add Identifier');?>" class="link_catalogue" href="<?php echo url_for('user/identifier?table=users&id='.$eid);?>"><?php echo __('Add');?></a>
<?php endif ; ?>
