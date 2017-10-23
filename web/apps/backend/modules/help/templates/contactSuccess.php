<?php slot('title', __("Contact"));?>
<div class="page">
  <h1><?php echo __('Contact');?> :</h1>
  <p>
  <?php echo __('You can reach us at <a href="mailto:%mail%">%mail%</a>', array('%mail%'=>$contact['mail']));?>
  </p>
</div>
