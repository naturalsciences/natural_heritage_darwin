<legend><?php echo __("You can also use widgets below for this collection") ; ?></legend>
<table>
  <tbody>
    <tr>
      <th><?php echo __('Category') ; ?></th>
      <th><?php echo __('Widget') ; ?></th>
    </tr>
    <?php foreach($widget as $category => $list) : ?>
    <tr>
      <td><?php echo $category ?></td>
      <td><ul>
      <?php foreach($list as $name) : ?>
          <li><?php echo sfOutputEscaper::unescape($name) ; ?></li>
      <?php endforeach ; ?>
      </ul></td>
    </tr>
    <?php endforeach ; ?>
  </tbdoy>
</table>
