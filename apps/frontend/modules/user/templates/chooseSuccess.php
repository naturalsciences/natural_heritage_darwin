<?php slot('title', __('Search User'));  ?>        
<div class="page">
<h1><?php echo __('Search User');?></h1>

<script language="javascript">
$(document).ready(function () {
 
    $('.result_choose').live('click',function () {
	el = $(this).closest('tr');
	ref_element_id = getIdInClasses(el);
	ref_element_name = el.find('td.item_name').text();
	$('.result_choose').die('click');
        $('.qtip-button').click();
    });
    $('.result_choose_coll_rights').live('click',function () {
	el = $(this).closest('tr');
	ref_element_id = getIdInClasses(el);
	$info = 'good' ;
	$('.collections_rights tbody tr').each(function() {
	    if($(this).attr('id') == ref_element_id) $info = 'bad' ;
	    if($(this).attr("style") == 'display: none; ') $info = 'good' ;
	});
	if($info == 'good') addCollRightValue(ref_element_id);
    });
});
</script>

  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => true)) ?>

</div>
