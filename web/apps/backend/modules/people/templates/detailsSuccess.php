<table class="details_info">
  <tr>
    <td class="details_addr">
      <h3><?php echo __('Address');?> :</h3>
      <ul>
      <?php foreach($item->PeopleAddresses as $address):?>
	<li><?php echo $address->getCountry();?></li>
      <?php endforeach;?>
      </ul>
    </td>

    <td class="details_comm">
      <h3><?php echo __('Communication Means');?> :</h3>
      <ul>
      <?php foreach($item->PeopleComm as $comm):?>
	  <li><?php echo $comm->getCommType();?> - <?php echo $comm->getEntry();?></li>
      <?php endforeach;?>
      </ul>
    </td>

    <td class="details_lang">
      <h3><?php echo __('Language');?> :</h3>
      <ul>
      <?php foreach($item->PeopleLanguages as $lang):?>
	  <li>
	    <?php echo format_language($lang->getLanguageCountry());?> 
	    <?php if($lang->getMother()):?>
	      <?php echo __('(mother)');?>
	    <?php endif;?>
	    <?php if($lang->getPreferredLanguage()):?>
	      <?php echo __('(preferred)');?>
	    <?php endif;?>
	  </li>
      <?php endforeach;?>
      </ul>
    </td>

    <td class="details_rel">
      <h3><?php echo __('Relationships');?> :</h3>
      <ul>
      <?php foreach($relations as $rel):?>
	  <li><?php echo $rel->Parent;?></li>
      <?php endforeach;?>
      </ul>
    </td>
  </tr>
</table>
