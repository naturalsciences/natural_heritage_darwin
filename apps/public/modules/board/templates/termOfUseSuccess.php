<div class="page">
<?php
if($sf_user->getCulture() =='es_ES') $lang='en';
else $lang=$sf_user->getCulture();
?>
<?php  include_partial('TermContent_'.$lang) ; ?>
</div>
