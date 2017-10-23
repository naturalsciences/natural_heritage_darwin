<?php foreach($form['Identifications'] as $ident):?>
<tbody class="spec_ident_data">
  <tr class="spec_ident_data">
    <td colspan="2">
      <?php echo $ident['notion_date']->render();?>
    </td>
    <td>
      <?php echo $ident['notion_concerned']->getValue();?>
    </td>
    <td>
      <?php echo $ident['value_defined']->getValue();?>
    </td>
    <td>
      <?php echo $ident['determination_status']->getValue();?>
    </td>
    <td>
      <ul class="tool">
      <?php foreach($ident['Identifiers'] as $people):?>
         <?php echo ("<li>".$people['people_ref']->renderLabel()."</li>") ; ?>
      <?php endforeach ; ?>
      </ul>
    </td>
  </tr>
</tbody>
<?php endforeach ; ?>
