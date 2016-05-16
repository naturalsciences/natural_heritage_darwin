<?php slot('title', __('Specimens Search Result'));  ?>
<?php use_javascript('double_list.js');?>
<?php include_partial('result_cols', array('columns' => $columns, 'field_to_show' => $field_to_show));?>

<div class="encoding">
  <?php include_stylesheets_for_form($form) ?>
  <?php include_javascripts_for_form($form) ?>
  <div class="page" id="search_div">
    <h1 id="title"><?php echo __('Specimens Search Result');?></h1>
    <?php echo form_tag('specimensearch/search'.( isset($is_choose) ? '?is_choose='.$is_choose : '') , array('class'=>'specimensearch_form','id'=>'specimen_filter'));?>
      <ul id="intro" class="hidden">
        <?php 
        // Render all the form fields as hidden input if possible. if the value is an array or and object render them as usual
        foreach($form as $row)
        { 
          $w = new sfWidgetFormInputHidden();
          $attributes = $form->getWidget($row->getName())->getAttributes();
          if(is_string($row->getValue()) || is_null($row->getValue()))
            echo '<li>'.$w->render( $form->getWidgetSchema()->generateName($row->getName()),$row->getValue(),$attributes).'</li>';
          else
            echo '<li>'.$row.'</li>';
        }?>
      </ul>
      <div class="search_results">
        <div class="search_results_content">
          <?php include_partial('searchSuccess',
                                array('specimensearch' => $specimensearch,
                                      'codes' => $codes,
                                      'loans' => $loans,
                                      'form' => $form, 
                                      'orderBy' => $orderBy,
                                      's_url' => $s_url,
                                      'orderDir' => $orderDir,
                                      'currentPage' => $currentPage,
                                      'pagerLayout' => $pagerLayout,
                                      'is_specimen_search' => $is_specimen_search,
                                      'columns' => $columns,
                                     )
                               ); ?>
        </div>
      </div>
      <?php if(isset($is_pinned_only_search)):?>
        <input type="hidden" name="pinned" value="true" />
      <?php endif;?>
      <script  type="text/javascript">
        $(document).ready(function () {

          $('form#specimen_filter select.double_list_select-selected option').attr('selected', 'selected');
          $('body').duplicatable({
            duplicate_href: '<?php echo url_for('specimen/confirm');?>',
            duplicate_binding_type: 'live'
          });

          $("#criteria_butt").click(function(){
            // Reselect all double list options that should be selected to be taken in account in the form submit
            // Submit the form with criteria = 1 -> telling we request the index template
            $('#specimen_filter').attr('action','<?php echo url_for('specimensearch/search?criteria=1');?>').submit();
          });
        <?php if($is_specimen_search):?>
          $('#del_from_spec').click(function(){
            pins_array = new Array();
            $('.remove_spec:checked').each(function(){
              pins_array.push( $(this).val() );
            });
            if(pins_array.length == 0) {
              alert("<?php echo __('You must select at least one specimen.');?>");
            }
            else {
              if(confirm('<?php echo addslashes(__('Are you sure?'));?>'))
              {
                $.get('<?php echo url_for('savesearch/removePin?search='.$is_specimen_search);?>/ids/' + pins_array.join(',') ,function (html){
                  for(var i = 0; i < pins_array.length; i++) {
                    $('.rid_' + pins_array[i]).remove();
                  }
                  if($('.spec_results tbody tr').length == 0) {
                    location.reload();
                  }
                });
              }
            }
          });
        <?php endif;?>
          $('#export_spec').click(function(event){
            $('form.specimensearch_form').attr('action', $('form.specimensearch_form').attr('action') + '/export/csv');
            $('form.specimensearch_form').submit();
          });

        });
      </script>
    </form>
      <div class="check_right" id="save_button"> 
        <a href="<?php echo url_for('specimen/confirm') ; ?>" class="hidden"></a>
        <?php include_partial('savesearch/saveSpec', array('spec_lists'=>$spec_lists));?>

        <?php if(! $is_specimen_search):?>
          <?php include_partial('savesearch/saveSearch');?>
        <?php endif;?>
      </div>
      <?php if(!isset($is_pinned_only_search) && ! $is_specimen_search):?>
        <input type="button" id="criteria_butt" class="save_search" value="<?php echo __('Back to criteria'); ?>">
      <?php elseif(! isset($is_pinned_only_search) && $is_specimen_search):?>
        <input type="button" id="del_from_spec" class="save_search" value="<?php echo __('Remove selected'); ?>">
      <?php endif;?>
      <input type="button" id="export_spec" class="save_search" value="<?php echo __('Export');?>" />
  
  </div>
</div>
<script type="text/javascript">
  $('.loans_info').click(function()
  {
    var loan_id = $(this).attr('id');
    item_row=$(this).closest('tr');
    if(item_row.find('#'+loan_id+'_list').is(":hidden"))
    {
      item_row.find('#'+loan_id+'_list').slideDown();
    }
    else {
      $('#'+loan_id+'_list').slideUp();
    }
  });
</script>
