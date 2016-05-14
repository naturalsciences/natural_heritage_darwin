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
                <label><input type="checkbox" class="top_remove_spec" /></label>
              <?php endif;?>
            </th>
            <th><!-- Pin -->
              <label class="top_pin"><input type="checkbox" /></label>
            </th>
            <?php $all_columns = $columns->getRawValue() ;?>
            <?php foreach($all_columns as $col_name => $col):?>
              <th class="col_<?php echo $col_name;?>">
                <?php if($col[0] !== false):?>
                  <a class="sort" href="<?php echo url_for($s_url.'&orderby='.$col[0].( ($orderBy==$col[0] && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.
                    $currentPage);?>">
                    <?php echo $col[1];?>
                    <?php if($orderBy == $col[0]) echo $orderSign; ?>
                  </a>
                <?php else:?>
                  <?php echo $col[1];?>
                  <?php if($col_name == 'codes') : ?>
                    <!-- Codes --><label class="top_code"><input type="checkbox" /></label>
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
                  <label><input type="checkbox" class="remove_spec" value="<?php echo $specimen->getId()?>"/></label>
                <?php endif;?>
              </td>
              <td>
                <label class="pin"><input type="checkbox" value="<?php echo $specimen->getId();?>" <?php if($sf_user->isPinned($specimen->getId(), 'specimen')):?>checked="checked"<?php endif;?> /></label>
              </td>
              <?php include_partial('result_content_specimen', array( 'specimen' => $specimen, 'codes' => $codes, 'is_specimen_search' => $is_specimen_search)); ?>
              <?php include_partial('result_content_individual', array( 'specimen' => $specimen, 'is_specimen_search' => $is_specimen_search)); ?>
              <?php include_partial('result_content_part', array( 'specimen' => $specimen, 'is_specimen_search' => $is_specimen_search, 'loans' => $loans)); ?>
              <td>
              <?php if($sf_user->isAtLeast(Users::ADMIN) || $specimen->getHasEncodingRights()) : ?>
                <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))), 'specimen/edit?id='.$specimen->getId());?>
                <?php echo link_to(image_tag('duplicate.png', array("title" => __("Duplicate"))), 'specimen/new?duplicate_id='.$specimen->getId(), array('class' => 'duplicate_link'));?>
              <?php endif; ?>
                <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))), 'specimen/view?id='.$specimen->getId(), array('target' => 'pop'));?>
              </td>
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

function pin(ids, status) {
  var id_part = "";
  if( Object.prototype.toString.call( ids ) === '[object Array]' ) {
    id_part = '/mid/' + ids.join(",");
  } else {
    id_part = '/id/' + ids;
  }
  $.getJSON('<?php echo url_for('savesearch/pin?source=specimen');?>'+ id_part + '/status/' + ( status ? '1':'0'),function (data){
    if(data.pinned) {
      $('.pinned_specimens i').text('(' + Object.keys(data.pinned).length + ')');
    }
  });
}

$(document).ready(function () {
  //Init screen size
  check_screen_size();

  //Init resize of screen
  $(window).resize(check_screen_size);

  //Init columns visibilty
  $('ul.column_menu .col_switcher :not(:checked)').each(function(){
    $('.col_' + $(this).val()).hide();
  });

  //Init custom checkbox
  $('input[type=checkbox], input[type=radio]').not('label.custom-label input').customRadioCheck();
  
  // Init Top pin state
  if($('.pin :checked').length == $('.pin :checkbox').length) {
    $('.top_pin :checkbox').attr('checked','checked').trigger('update');
  }
  else {
    $('.top_pin :checkbox').attr('checked',false).trigger('update');
  }

  
  //Pin a specimen
  $('.spec_results .pin :checkbox').change(function(){
     pin($(this).val(), $(this).is(':checked'));
  });

  // Check all pin's on the page
  $('.spec_results .top_pin :checkbox').click(function(){
    $('.spec_results .pin :checkbox').attr('checked', $(this).is(':checked')).trigger('update');
    pins = [];
    $('.spec_results .pin :checkbox').each(function(){
      pins.push($(this).val());
    });
    pin(pins, $(this).is(':checked'));
  }); 

  // Check all "remove specimen" for saved spec search
  $('.spec_results .top_remove_spec').click(function(){
    $('.remove_spec').attr('checked', $(this).is(':checked')).trigger('change');
  });
  
  // Hide or Show more than 2 codes 
  $('.top_code :checkbox').change(function(){
    if($(this).is(':checked'))
     $('.code_supp').removeClass('hidden')
    else
     $('.code_supp').addClass('hidden')
  });
});
</script>
