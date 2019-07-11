<label><?php echo __('Save my pinned specimens');?></label>
<select id="save_specs_choice">
    <option value="" selected="selected"></option>
    <optgroup label="<?php echo __('New');?>">
      <option value="create"><?php echo __('To a new list');?></option>
    </optgroup>
    <optgroup label="<?php echo __('Existing');?>">
      <?php foreach($spec_lists as $list):?>
        <option value="<?php echo $list->getId();?>"><?php echo $list->getName();?></option>
      <?php endforeach;?>
    </optgroup>
</select>
<input type="button" name="save" id="save_specs" class="save_search" value="<?php echo __('Go'); ?>">
<script  type="text/javascript">
$(document).ready(function () {

  $("#save_specs").click(function(event){
    event.preventDefault();
    if($('#save_specs_choice').val()=="") return;
    column_str = '';
    if( $('ul.column_menu .col_switcher :checked').length)
    {
      column_arr = []
      $('ul.column_menu .col_switcher :checked').each(function(){
        column_arr.push($(this).val());
      });
      column_str = column_arr.join('|');
    }
    else
    {
      column_str = $('#specimen_search_filters_col_fields').val();
    }

    var last_position = $(window).scrollTop();
    scroll(0,0) ;

    $('form.search_form select.double_list_select-selected option').attr('selected', 'selected');
    $("#save_specs").qtip({
      id: 'modal',
      content: {
        text: '<img src="/images/loader.gif" alt="loading"> loading ...',
        title: { button: true, text: '<?php echo __('Save your specimens')?>' },
        ajax: {
          url: '<?php echo url_for('savesearch/saveSearch?type=pin&source=specimen');?>/cols/' + encodeURI(column_str) + '/list_nr/' + $('#save_specs_choice').val(),
          type: 'POST'
        }
      },
      position: {
        my: 'top center',
        at: 'top center',
        adjust:{
          y: 250 // option set in case of the qtip become too big
        },
        target: $(document.body),
      },

      show: {
        ready: true,
        delay: 0,
        event: event.type,
        solo: true,
        modal: {
          on: true,
          blur: false
        },
      },
      hide: {
        event: 'close_modal',
        target: $('body')
      },
      events: {
        hide: function(event, api) {
          scroll(0,last_position);
          api.destroy();
          if(typeof(spec_list_saved) !='undefined' && spec_list_saved !=null)
            window.location.href = '<?php echo url_for('specimensearch/search');?>/search_id/' + spec_list_saved;
        }
      },
      style: 'ui-tooltip-light ui-tooltip-rounded'
    });
    return false;
 });
});
</script>
