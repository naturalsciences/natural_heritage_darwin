<div class="page">
    <h1><?php echo __('Choose a Taxonomy');?></h1>
<script language="javascript">
$(document).ready(function () {
    $('.result_choose').live('click', result_choose);
});
</script>
    <?php include_partial('choose', array('searchForm' => $searchForm,'is_choose' => true)) ?>
</div>
