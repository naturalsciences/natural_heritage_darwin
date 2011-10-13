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
    <?php include_partial('global/pager_info', array('form' => $form, 'pagerLayout' => $pagerLayout)); ?>

      <div id="source"><?php 
        if($source=="specimen")
          echo __('Scope : Specimens');
        elseif($source=="individual")
          echo __('Scope : Individuals');
        else
          echo __('Scope : Parts');?></div>
      <table class="spec_results">
        <thead>
          <tr>
            <th><!-- checkbox for selection of records to be removed -->
              <?php if($is_specimen_search):?>
                <?php echo image_tag('checkbox_remove_off.png', array('class'=>'top_remove_but remove_off','alt' =>  __('Keep all elements in list'))) ; ?>
                <?php echo image_tag('checkbox_remove_on.png', array('class'=>'top_remove_but remove_on hidden', 'alt' =>  __('Remove all elements from list'))) ; ?>
              <?php endif;?>
            </th>
            <th><!-- + / - buttons  --></th>
            <th><!-- Pin -->
               <?php echo image_tag('white_pin_off.png', array('class'=>'top_pin_but pin_off','alt' =>  __('Cancel this result'))) ; ?>
               <?php echo image_tag('white_pin_on.png', array('class'=>'top_pin_but pin_on', 'alt' =>  __('Save this result'))) ; ?>
            </th>
            <?php $all_columns = $columns['specimen']->getRawValue() + $columns['individual']->getRawValue() + $columns['part']->getRawValue() ;?>
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
                  <?php elseif($col_name == 'part_codes') : ?>
                    <?php echo image_tag('blue_expand.png', array('id' => 'display_part_codes','title' => 'Display all codes', 'class'=> 'tree_cmd_td collapsed')); ?>
                    <?php echo image_tag('blue_expand_up.png', array('id' => 'hide_part_codes','title' => 'Hide all codes', 'class'=> 'tree_cmd_td expanded')); ?>
                  <?php endif ; ?>
                <?php endif;?>
              </th>
            <?php endforeach;?>
            <th><!-- actions --></th>
          </tr>
        </thead>
        <?php foreach($specimensearch as $unit):?>
          <?php if($source=="specimen")
                {
                  $specimen = $unit;
                  $itemRef = $specimen['id'];
                }
                elseif($source=="individual")
                {
                  $individual = $unit;
                  $specimen = $individual->Specimens;
                  $itemRef = $individual['id'];
                }
                elseif($source=="part")
                {
                  $part = $unit;
                  $individual = $unit->Individual;
                  $specimen = $individual->Specimens;
                  $itemRef = $part['id'];
                }?>
          <tbody>
            <tr class="rid_<?php echo $itemRef?>">

              <td rowspan="2">
                <?php if($is_specimen_search):?>
                  <?php echo image_tag('checkbox_remove_off.png', array('class'=>'remove_but remove_off','alt' =>  __('Keep all elements in list'))) ; ?>
                  <?php echo image_tag('checkbox_remove_on.png', array('class'=>'remove_but remove_on hidden', 'alt' =>  __('Remove all elements from list'))) ; ?>
                <?php endif;?>
              </td>
              <td rowspan="2">
                <?php if($source != 'part'):?>
                  <?php $expandable = ($source=='specimen') ? true:true /** @TODO $specimen->getWithIndividuals() : $specimen->getWithParts()**/;?>
                  <?php if($expandable):?>
                    <?php echo image_tag('blue_expand.png', array('alt' => '+', 'class'=> 'tree_cmd_td collapsed')); ?>
                    <?php echo image_tag('blue_expand_up.png', array('alt' => '-', 'class'=> 'tree_cmd_td expanded')); ?>
                  <?php else:?>
                    <?php echo image_tag('grey_expand.png', array('alt' => '+', 'class'=> 'collapsed')); ?>
                  <?php endif;?>
                <?php endif;?>
              </td>
              <td >
                <?php if($sf_user->isPinned($itemRef, $source)):?>
                  <?php echo image_tag('blue_pin_on.png', array('class'=>'pin_but pin_on','alt' =>  __('Cancel this result'))) ; ?>
                  <?php echo image_tag('blue_pin_off.png', array('class'=>'pin_but pin_off hidden', 'alt' =>  __('Save this result'))) ; ?>
                <?php else:?>
                  <?php echo image_tag('blue_pin_on.png', array('class'=>'pin_but pin_on hidden','alt' =>  __('Cancel this result'))) ; ?>
                  <?php echo image_tag('blue_pin_off.png', array('class'=>'pin_but pin_off', 'alt' =>  __('Save this result'))) ; ?>
                <?php endif;?>
              </td>
              <?php include_partial('result_content_specimen', array('item_ref'=>$itemRef, 'source'=>$source,'specimen' => $specimen, 'codes' => $codes, 'is_specimen_search' => $is_specimen_search)); ?>
              <?php if($source != 'specimen'):?>
                <?php include_partial('result_content_individual', array('item_ref'=>$itemRef, 'individual' => $individual, 'is_specimen_search' => $is_specimen_search)); ?>
              <?php endif;?>
              <?php if($source == 'part'):?>
                <?php include_partial('result_content_part', array('item_ref'=>$itemRef, 'part' => $part, 'codes' => $part_codes, 'is_specimen_search' => $is_specimen_search)); ?>
              <?php endif;?>
              <td rowspan="2">
              <?php if($sf_user->isAtLeast(Users::ADMIN) || $specimen->getHasEncodingRights()) : ?>
                <?php switch($source){
                  case 'specimen':   $e_link = 'specimen/edit?id='.$specimen->getId();
                                     $v_link = 'specimen/view?id='.$specimen->getId();                  
                                     $d_link = 'specimen/new?duplicate_id='.$specimen->getId();break;
                  case 'individual': $e_link = 'individuals/edit?id='.$individual->getId();
                                     $v_link = 'individuals/view?id='.$individual->getId();
                                     $d_link = 'individuals/edit?spec_id='.$specimen->getId().'&duplicate_id='.$individual->getId();break;
                  default:           $e_link = 'parts/edit?id='.$part->getId();
                                     $v_link = 'parts/view?id='.$part->getId();
                                     $d_link = 'parts/edit?indid='.$individual->getId().'&duplicate_id='.$part->getId();break;              
                  };?>
                  <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))), $e_link);?>
                  <?php echo link_to(image_tag('duplicate.png', array("title" => __("Duplicate"))), $d_link, array('class' => 'duplicate_link'));?>
              <?php else : ?>

                <?php switch($source){
                  case 'specimen':   $v_link = 'specimen/view?id='.$specimen->getId();break;
                  case 'individual': $v_link = 'individuals/view?id='.$specimen->getIndividualRef();break;
                  default:           $v_link = 'parts/view?id='.$specimen->getPartRef();break;
                  };?>
              <?php endif ; ?>
              <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),$v_link,array('target' => 'pop'));?>
              </td>
            </tr>

            <?php if($source == 'specimen' && $specimen->getWithIndividuals()):?>
              <tr id="tr_individual_<?php echo $specimen->getId();?>" class="ind_row sub_row">
                <td colspan="14"> 
                  <div id="container_individual_<?php echo $specimen->getId();?>" class="tree"></div>
                  <script type="text/javascript">
                    $(document).ready(function () {
                    $('tr.rid_<?php echo $specimen->getId(); ?> img.collapsed').click(function() 
                    {
                      $(this).hide();
                      $(this).siblings('.expanded').show();
                      $.get('<?php echo url_for("specimensearch/individualTree?id=".$specimen->getId()) ;?>',function (html){
                              $('#container_individual_<?php echo $specimen->getId();?>').html(html).slideDown();
                              });
                    });  
                    $('tr.rid_<?php echo $specimen->getId(); ?> img.expanded').click(function() 
                    {
                      $(this).hide();
                      $(this).siblings('.collapsed').show();
                      $('#container_individual_<?php echo $specimen->getId();?>').slideUp();
                    });
                  });
                  </script>
                </td>
              </tr>
            <?php elseif($source == 'specimen'):?>
              <tr class="ind_row sub_row"><td colspan="14"></td></tr>
            <?php elseif($source == 'individual' /** @TODO  && $specimen->getWithParts()*/):?>
              <tr id="tr_part_<?php echo $individual->getId();?>" class="part_row sub_row">
                <td colspan="14"> 
                  <div id="container_part_<?php echo $individual->getId();?>" class="tree"></div>
                  <script type="text/javascript">
                  $(document).ready(function () {
                    $('tr.rid_<?php echo $individual->getId(); ?> img.collapsed').click(function() 
                    {
                      $(this).hide();
                      $(this).siblings('.expanded').show();
                      $.get('<?php echo url_for("specimensearch/partTree?id=".$individual->getId()) ;?>',function (html){
                              $('#container_part_<?php echo $individual->getId();?>').html(html).slideDown();
                              });
                    });  
                    $('tr.rid_<?php echo $individual->getId(); ?> img.expanded').click(function() 
                    {
                      $(this).hide();
                      $(this).siblings('.collapsed').show();
                      $('#container_part_<?php echo $individual->getId();?>').slideUp();
                    });
                  });
                  </script>
                </td>
              </tr>
            <?php else: // if source = individual but with no parts ?>
              <tr class="part_row sub_row">
                <td colspan="14"></td>
              </tr>
            <?php endif;?>
          </tbody>
        <?php endforeach;?>
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
/****COL MANAGEMENT ***/
  $('ul.column_menu > li > ul > li').each(function(){
    hide_or_show($(this));
  });
  initIndividualColspan() ;
/****END COL MANAGEMENT ***/

  /**PIN management **/
  $('.spec_results .pin_but').click(function(){
    if($(this).hasClass('pin_on'))
    {
      $(this).parent().find('.pin_off').removeClass('hidden'); 
      $(this).addClass('hidden') ;
      pin_status = 0;
    }
    else
    {
      $(this).parent().find('.pin_on').removeClass('hidden');
      $(this).addClass('hidden') ;
      pin_status = 1;
    }
    rid = getIdInClasses($(this).closest('tr'));
    $.get('<?php echo url_for('savesearch/pin?source='.$source);?>/id/' + rid + '/status/' + pin_status,function (html){});
  });

  if($('.spec_results tbody .pin_on').not('.hidden').length == $('.spec_results tbody .pin_on').length)
  {
      $('.top_pin_but').parent().find('.pin_on').removeClass('hidden');
      $('.top_pin_but').parent().find('.pin_off').addClass('hidden') ;
  }
  else
  {
      $('.top_pin_but').parent().find('.pin_off').removeClass('hidden');
      $('.top_pin_but').parent().find('.pin_on').addClass('hidden') ;
  }
  
  $('.spec_results .top_pin_but').click(function(){
    /** Multiple pin behavior ***/
    if($(this).hasClass('pin_on'))
    {
      $(this).parent().find('.pin_off').removeClass('hidden'); 
      $(this).addClass('hidden') ;
      pin_status = 0;
    }
    else
    {
      $(this).parent().find('.pin_on').removeClass('hidden');
      $(this).addClass('hidden') ;
      pin_status = 1;
    }
    pins = '';
    $('.spec_results tbody tr').not('.sub_row').each(function(){
      rid = getIdInClasses($(this));
      if(pins == '')
        pins = rid;
      else
        pins += ','+rid;
    });

    if(pin_status == 0)
    {
        $('.spec_results tbody tr .pin_off').removeClass('hidden');
        $('.spec_results tbody tr .pin_on').addClass('hidden') ;
    }
    else
    {
        $('.spec_results tbody tr .pin_off').addClass('hidden');
        $('.spec_results tbody tr .pin_on').removeClass('hidden') ;
    }
    $.get('<?php echo url_for('savesearch/pin?source='.$source);?>/mid/' + pins + '/status/' + pin_status,function (html){});
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

  $('#display_part_codes').click(function() 
  {
    $(this).hide();
    $('#hide_part_codes').show();
    $('td.col_part_codes li.code_supp').each(function() {
      $(this).removeClass('hidden');    
    });
  });  
  $('#hide_part_codes').click(function() 
  {
    $(this).hide();
    $('#display_part_codes').show();
    $('td.col_part_codes li.code_supp').each(function() {
      $(this).addClass('hidden'); 
    });
  });
});
</script>
