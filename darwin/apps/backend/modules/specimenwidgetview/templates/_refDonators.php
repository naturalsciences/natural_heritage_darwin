<table class="property_values collectors">
  <tbody>
    <?php foreach($Donators as $donator):?>
       <tr><td>
         <a href="<?php echo url_for('people/view?id='.$donator->getPeopleRef()) ; ?>"><?php echo $donator->People->getFormatedName() ; ?></a>
       </td></tr>
    <?php endforeach;?>
  </tbody>
</table>
	
