<?php slot('title', __('Welcome In DaRWIN'));  ?>

<div class="page">
<?php
if($sf_user->getCulture() =='es_ES') $lang='en';
else $lang=$sf_user->getCulture();
?>
<?php  include_partial('welcome_'.$lang,array('specimens'=> $specimens)) ; ?>
</div>
