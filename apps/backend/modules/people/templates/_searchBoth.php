<ul class="tab_choice">
  <li class="both_search_people"><?php echo __('People');?></li>
  <li class="both_search_institutions"><?php echo __('Institution');?></li>
</ul>
<div class="search_box search_catalogue_people_both">
  
</div>

<script language="javascript">

$(document).ready(function () {
    $('.search_box').slideDown();
  $('.both_search_people').not('.activated').click( function(event)
  {
    event.preventDefault();
    $('.result_choose').die('click');
    $(".search_box").html('<img src="/images/loader.gif" />');
    $('.tab_choice .activated').removeClass('activated');
    $('.both_search_people').addClass('activated');

    $.ajax({
      type: "POST",
      url: '<?php echo url_for('people/choose?with_js=1' . ($is_choose?'&is_choose=1' : '') );?>',
      data: {name:'<?php echo $sf_params->get('name'); ?>'},
      success: function(html){
        $('.search_box').html(html);
      }
    });
  });
  $('.both_search_people').trigger('click');
  $('.both_search_institutions').not('.activated').click( function(event)
  {
    event.preventDefault();
    $('.result_choose').die('click');
    $(".search_box").html('<img src="/images/loader.gif" />');
    $('.tab_choice .activated').removeClass('activated');

    $('.both_search_institutions').addClass('activated');

    $.ajax({
      type: "POST",
      url: '<?php echo url_for('institution/choose?with_js=1' . ($is_choose?'&is_choose=1' : '') );?>',
      data: {name:'<?php echo $sf_params->get('name'); ?>'},
      success: function(html){
        $('.search_box').html(html);
      }
    });
  });
});

</script> 
