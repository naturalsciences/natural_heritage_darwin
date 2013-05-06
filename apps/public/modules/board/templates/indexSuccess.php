<?php slot('title', __('Welcome In DaRWIN'));  ?>

<div class="page">
<?php  include_partial('welcome_'.$sf_user->getCulture(),array('individuals'=>$individuals)) ; ?>
</div>
