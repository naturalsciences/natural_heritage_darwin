<ul class="tool">
  <?php if ($form->count()) : ?>
    <?php foreach ($form as $method) : ?>
      <?php echo ("<li>".$method->CollectingMethods->getMethod()."</li>") ; ?>
    <?php endforeach ; ?>
  <?php else : ?>
    <?php echo __("No Methods defined") ; ?>
  <?php endif ; ?>
</ul>
