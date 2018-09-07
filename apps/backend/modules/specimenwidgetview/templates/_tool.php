<ul class="tool">
  <?php if ($form->count()) : ?>
    <?php foreach ($form as $tool) : ?>
      <?php echo ("<li>".$tool->CollectingTools->getTool()."</li>") ; ?>
    <?php endforeach ; ?>
  <?php else : ?>
    <?php echo __("No Tools defined") ; ?>
  <?php endif ; ?>    
</ul>
