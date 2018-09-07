<table class="property_values collectors">
  <tbody>
    <?php foreach($Collectors as $collector):?>
       <tr><td>
         <a href="<?php echo url_for('people/view?id='.$collector->getPeopleRef()) ; ?>"><?php echo $collector->People->getFormatedName() ; ?></a>
       </td></tr>
    <?php endforeach;?>
  </tbody>
</table>
	
