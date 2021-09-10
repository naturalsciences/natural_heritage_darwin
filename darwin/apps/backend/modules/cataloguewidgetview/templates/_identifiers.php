<?php use_helper('Text');?>
<table class="catalogue_table_view">
  <thead>
    <tr>
      <th><?php echo __('Protocol');?></th>
      <th><?php echo __('Value');?></th>
	  <th><?php echo __('Link');?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($identifiers as $ident):?>
  <tr>
    <td>
      <?php echo $ident->getProtocol();?>
    </td>
    <td>
      <?php echo $ident->getValue();?>
    </td>
	<td>
		
		<?php if(array_key_exists(strtolower($ident->getProtocol()), Identifiers::getURLService())):?>
		<a target="_blank" 
	  href="<?php print(Identifiers::getURLService()[strtolower($ident->getProtocol())].$ident->getValue());?>">
	    <?php print(Identifiers::getURLService()[strtolower($ident->getProtocol())].$ident->getValue());?></a>
		<?php endif;?>
	</td>
	<td>	
	</td>
  </tr>
  <?php endforeach;?>
  </tbody>
</table>
