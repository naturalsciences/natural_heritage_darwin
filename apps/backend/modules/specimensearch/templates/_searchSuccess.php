<div>
      <?php if($is_specimen_search):?>
        <input type="hidden" name="spec_search" value="<?php echo $is_specimen_search;?>" />
      <?php endif;?>
  <?php if(isset($specimensearch) && $specimensearch->count() != 0 && isset($orderBy) && isset($orderDir) && isset($currentPage)):?>   
    <?php
      if($orderDir=='asc')
        $orderSign = '<span class="order_sign_down">&nbsp;&#9660;</span>';
      else
        $orderSign = '<span class="order_sign_up">&nbsp;&#9650;</span>';
    ?>
    <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
    <?php include_partial('global/pager_info', array('form' => $form, 'pagerLayout' => $pagerLayout, 'container'=> '.spec_results')); ?>

      <table class="spec_results">
        <thead>
          <tr>
            <th><!-- checkbox for selection of records to be removed -->
              <?php if($is_specimen_search):?>
                <?php echo image_tag('checkbox_remove_off.png', array('class'=>'top_remove_but remove_off','alt' =>  __('Keep all elements in list'))) ; ?>
                <?php echo image_tag('checkbox_remove_on.png', array('class'=>'top_remove_but remove_on hidden', 'alt' =>  __('Remove all elements from list'))) ; ?>
              <?php endif;?>
            </th>
            <th class="top_pin_but"><!-- Pin -->
               <?php echo image_tag('white_pin_off.png', array('class'=>'pin_off','alt' =>  __('Cancel this result'))) ; ?>
               <?php echo image_tag('white_pin_on.png', array('class'=>'pin_on', 'alt' =>  __('Save this result'))) ; ?>
            </th>
            <?php $all_columns = $columns->getRawValue() ;?>
            <?php foreach($all_columns as $col_name => $col):?>
              <th class="col_<?php echo $col_name;?>">
                <?php if($col[0] != false):?>
                  <a class="sort" href="<?php echo url_for($s_url.'&orderby='.$col[0].( ($orderBy==$col[0] && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.
                    $currentPage);?>">
                    <?php echo $col[1];?>
                    <?php if($orderBy == $col[0]) echo $orderSign; ?>
                  </a>
                <?php else:?>
                  <?php echo $col[1];?>
                  <?php if($col_name == 'codes') : ?>
                    <?php echo image_tag('blue_expand.png', array('id' => 'display_all_codes','title' => 'Display all codes', 'class'=> 'tree_cmd_td collapsed')); ?>
                    <?php echo image_tag('blue_expand_up.png', array('id' => 'hide_all_codes','title' => 'Hide all codes', 'class'=> 'tree_cmd_td expanded')); ?>
                  <?php endif ; ?>
                <?php endif;?>
              </th>
            <?php endforeach;?>
            <th><!-- actions --></th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($specimensearch as $specimen):?>
            <tr class="rid_<?php echo $specimen->getId()?>">
              <td>
                <?php if($is_specimen_search):?>
                  <?php echo image_tag('checkbox_remove_off.png', array('class'=>'remove_but remove_off','alt' =>  __('Keep all elements in list'))) ; ?>
                  <?php echo image_tag('checkbox_remove_on.png', array('class'=>'remove_but remove_on hidden', 'alt' =>  __('Remove all elements from list'))) ; ?>
                <?php endif;?>
              </td>
              <td class="pin_but <?php if($sf_user->isPinned($specimen->getId(), 'specimen')):?>check<?php else:?>uncheck<?php endif;?>" >
                  <?php echo image_tag('blue_pin_on.png', array('class'=>'pin_on','alt' =>  __('Cancel this result'))) ; ?>
                  <?php echo image_tag('blue_pin_off.png', array('class'=>'pin_off', 'alt' =>  __('Save this result'))) ; ?>
              </td>
              <?php include_partial('result_content_specimen', array( 'specimen' => $specimen, 'codes' => $codes, 'is_specimen_search' => $is_specimen_search)); ?>
              <?php include_partial('result_content_individual', array( 'specimen' => $specimen, 'is_specimen_search' => $is_specimen_search)); ?>
              <?php include_partial('result_content_part', array( 'specimen' => $specimen, 'is_specimen_search' => $is_specimen_search)); ?>
              <td>
              <?php if($sf_user->isAtLeast(Users::ADMIN) || $specimen->getHasEncodingRights()) : ?>
                <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))), 'specimen/edit?id='.$specimen->getId());?>
                <?php echo link_to(image_tag('duplicate.png', array("title" => __("Duplicate"))), 'specimen/new?duplicate_id='.$specimen->getId(), array('class' => 'duplicate_link'));?>
              <?php else : ?>
                <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))), 'specimen/view?id='.$specimen->getId(), array('target' => 'pop'));?>
              </td>
              <?php endif; ?>
            </tr>
        <?php endforeach;?>
      </tbody>
      </table>
      <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
    <?php else:?>
      <?php echo __('No Specimen Matching');?>
    <?php endif;?>
</div>  
<script type="text/javascript">
$(document).ready(function () {
  check_screen_size() ;
  $(window).resize(function(){
    check_screen_size();
  }); 

function pin(ids, status) {
  var id_part = "";
  if( Object.prototype.toString.call( ids ) === '[object Array]' ) {
    id_part = '/mid/' + ids.join(",");
  } else {
    id_part = '/id/' + ids;
  }
  $.getJSON('<?php echo url_for('savesearch/pin?source=specimen');?>'+ id_part + '/status/' + status,function (data){
    if(data.pinned) {
      $('.pinned_specimens i').text('(' + Object.keys(data.pinned).length + ')');
    }
  });
}
  
/****COL MANAGEMENT ***/
  $('ul.column_menu > li').each(function(){
    hide_or_show($(this));
  });
/****END COL MANAGEMENT ***/

  /**PIN management **/
  $('.spec_results .pin_but').click(function(){
    update_check_uncheck($(this));
    if($(this).hasClass('check')) {
      pin_status = 1;
    }
    else {
      pin_status = 0;
    }
    rid = getIdInClasses($(this).closest('tr'));
    pin(rid, pin_status);
  });

  // Init Top pin state
  if($('.pin_but.check').length == $('.spec_results tbody .pin_but').length) {
    $('.top_pin_but').addClass('check');
  }
  else {
    $('.top_pin_but').addClass('uncheck');
  }

  $('.spec_results .top_pin_but').click(function(){
    update_check_uncheck($(this));
    /** Multiple pin behavior ***/
    if($(this).hasClass('check')) {
      pin_status = 1;
      $('.spec_results tbody tr .pin_but').addClass('check').removeClass('uncheck');
    }
    else {
      pin_status = 0;
      $('.spec_results tbody tr .pin_but').addClass('uncheck').removeClass('check');
    }
    pins = [];
    $('.spec_results tbody tr').each(function(){
      rid = getIdInClasses($(this));
      pins.push(rid);
    });

    pin(pins, pin_status);
  }); 

  /*Remove management*/

  $('.spec_results .top_remove_but').click(function(){
    /** Multiple pin behavior ***/
    if($(this).hasClass('remove_on'))
    {
      $(this).parent().find('.remove_off').removeClass('hidden'); 
      $(this).addClass('hidden');
      $('.spec_results tbody .remove_off').removeClass('hidden');
      $('.spec_results tbody .remove_on').addClass('hidden');
    }
    else
    {
      $(this).parent().find('.remove_on').removeClass('hidden');
      $(this).addClass('hidden');
      $('.spec_results tbody .remove_on').removeClass('hidden');
      $('.spec_results tbody .remove_off').addClass('hidden');
    }
  });

  $('.spec_results tbody .remove_but').click(function(){
    /** Multiple pin behavior ***/
    if($(this).hasClass('remove_on'))
    {
      $(this).parent().find('.remove_off').removeClass('hidden'); 
      $(this).addClass('hidden');
      $('.spec_results thead th .remove_off').removeClass('hidden');
      $('.spec_results thead th .remove_on').addClass('hidden');
    }
    else
    {
      $(this).parent().find('.remove_on').removeClass('hidden');
      $(this).addClass('hidden');
      if($('.spec_results tbody .remove_on').not('.hidden').length == $('.spec_results tbody .remove_on').length)
      {
        $('.spec_results thead th .remove_on').removeClass('hidden');
        $('.spec_results thead th .remove_off').addClass('hidden');
      }
    }
  });
  $('#display_all_codes').click(function() 
  {
    $(this).hide();
    $('#hide_all_codes').show();
    $('td.col_codes li.code_supp').each(function() {
      $(this).removeClass('hidden');    
    });
  });

  $('#hide_all_codes').click(function() 
  {
    $(this).hide();
    $('#display_all_codes').show();
    $('td.col_codes li.code_supp').each(function() {
      $(this).addClass('hidden'); 
    });
  });

});
</script>
