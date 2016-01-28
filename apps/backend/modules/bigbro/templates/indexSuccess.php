<?php slot('title', __('Big Brother'));  ?>        

<div class="page big_brother">
<h1><?php echo __('Hi Big Brother... I\'m Watching You!');?></h1>

    <?php include_partial('searchForm', array('form' => $form, 'is_choose' => false)) ?>
</div>

<script language="javascript">
$(document).ready(function () {
  $('.big_brother').choose_form({});
  $('#bigbro_filter').submit();
});
</script>
