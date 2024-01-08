<tr>
  <td>
  <table>
   <tr>
	  <td><?php echo $form['maintenance_or_workflow_people_ref']->renderError();?></td>
	  <td><?php echo $form['maintenance_or_workflow_maintenance_people_ref']->renderError();?></td>
      <td><?php echo $form['maintenance_or_workflow_from_date']->renderError();?></td>
      <td></td>
      <td><?php echo $form['maintenance_or_workflow_to_date']->renderError();?></td>
      <td><?php echo $form['workflow_statuses']->renderError();?></td>
	   <td><?php echo $form['maintenance_action_observation']->renderError();?></td>
      <td></td>
   </tr>
  </table>
  <table>
  
    <thead>
   <tr>
      <th><?php echo $form['workflow_statuses']->renderLabel(); ?></th>
      <th><?php echo $form['maintenance_action_observation']->renderLabel(); ?></th>  		  
   </tr>
  </thead>
   <tbody>
    <tr>
      <td><?php echo $form['workflow_statuses'];?></td>   
	  <td><?php echo $form['maintenance_action_observation']; ?></td>  	  
    </tr>
	
   <tr>
      <th><?php echo $form['maintenance_or_workflow_people_ref']->renderLabel(); ?></th> 
	  <th><?php echo $form['maintenance_or_workflow_maintenance_people_ref']->renderLabel(); ?></th> 
	  
   </tr>
   <tr>
      <td><?php echo $form['maintenance_or_workflow_people_ref']; ?></td> 
	  <td><?php echo $form['maintenance_or_workflow_maintenance_people_ref']; ?></td> 
	   	  
   </tr>
   <tr>
      <th><?php echo $form['maintenance_or_workflow_from_date']->renderLabel(); ?></th> 
	  <th><?php echo $form['maintenance_or_workflow_to_date']->renderLabel(); ?> </th>  	  
   </tr>
   <tr>
      <td><?php echo $form['maintenance_or_workflow_from_date']; ?></td> 
	  <td><?php echo $form['maintenance_or_workflow_to_date']; ?></td>    	  
   </tr>
   </table>
   </td>
</tr>
   