  <ul>
  <?php foreach($result as $value) : ?>
    <li><?php echo __($field) ; echo (' : '.$value[strtolower($field)]) ; ?></li>
  <?php endforeach ; ?>
  </ul>
