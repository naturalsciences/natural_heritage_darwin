<input type="button" name="save" id="save_search" value="<?php echo __('Save this search'); ?>" class="save_search">

<script  type="text/javascript">
$(document).ready(function () {

  $("#save_search").click(function(event){
    event.preventDefault();
    column_str = '';
    if( $('ul.column_menu .col_switcher :checked').length) {
      column_str = getSearchColumnVisibilty();
    } else {
      column_str = $('#specimen_search_filters_col_fields').val();
    }
    var last_position = $(window).scrollTop();
    scroll(0,0) ;

    $('form.specimensearch_form select.double_list_select-selected option').attr('selected', 'selected');
    $("#save_search").qtip({
        id: 'modal',
        content: {
          text: '<img src="/images/loader.gif" alt="loading"> loading ...',
          title: { button: true, text: '<?php echo __('Save your search')?>' },
          ajax: {
            url: '<?php echo url_for("savesearch/saveSearch");?>/source/specimen/cols/' + encodeURI(column_str),
            type: 'POST',
            data: $('form.specimensearch_form').serialize()
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
          }
        },
        style: 'ui-tooltip-light ui-tooltip-rounded'
      });
    return false;
 });
});
</script>
