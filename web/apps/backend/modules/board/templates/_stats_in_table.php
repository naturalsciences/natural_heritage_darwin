  <table>
    <thead>
      <tr>
        <?php foreach($fields as $field) : ?>
          <th><?php echo __($field) ; ?></th>
        <?php endforeach ; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($result as $value) : ?>
      <tr>
        <?php foreach($fields as $field) : ?>
          <td><?php echo ($value[strtolower($field)]) ; ?></td>
        <?php endforeach ; ?>    
      </tr>
      <?php endforeach ; ?>
    </tbody>
  </table>

