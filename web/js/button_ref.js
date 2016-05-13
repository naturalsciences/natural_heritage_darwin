var ref_element_id = null;
var ref_element_name = null;
var ref_level_id = '';
var ref_caller_id = '';
var data_field_to_clean = '';

function button_ref_modal(event)
{
    event.preventDefault();
    if($(this).attr('data-field-to-clean') != undefined) {
      if ($(this).attr('data-field-to-clean').length) {
        data_field_to_clean = $(this).attr('data-field-to-clean');
      }
    }
    var last_position = $(window).scrollTop();
    scroll(0,0) ;
    $(this).parent().parent().find('input[type="hidden"]').trigger({ type:"loadref"});
    $(this).qtip({
      id: 'modal',
      content: {
        text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
        title: { button: true, text: $(this).parent().attr('title') },
        ajax: {
          url: $(this).attr('href'),
                type: 'GET',
                // Take name in input if set
                data: {with_js:1, name: $(this).parent().parent().find('input.ref_name').val(), level: $('table.classifications_edit').find('select.catalogue_level').val()}
        }
      },
      position: {
        my: 'top center',
        at: 'top center',
        adjust:{
          y: 250 // option set in case of the qtip become too big
        },
        target: $(document.body)
      },

      show: {
        ready: true,
        delay: 0,
        event: event.type,
        solo: true,
        modal: {
          on: true,
          blur: false
        }
      },
      hide: {
        event: 'close_modal',
        target: $('body')
      },
      events: {
        show: function () {
          ref_element_id = null;
          ref_element_name = null;
        },
        hide: function(event, api) {
          if(ref_element_id != null && ref_element_name != null)
          {
            parent_el = api.elements.target.parent().prevAll('.ref_name');
            if(parent_el.get( 0 ).nodeName == 'INPUT')
              parent_el.val(ref_element_name);
            else
              parent_el.text(ref_element_name);
            parent_el.prev().val(ref_element_id);
            api.elements.target.parent().prevAll('.ref_clear').removeClass('hidden').show();
            api.elements.target.find('.off').removeClass('hidden');
            api.elements.target.find('.on').addClass('hidden');
            parent_el.prev().trigger('change');
            if (data_field_to_clean !== '') {
              if ($('.'+data_field_to_clean).length) {
                $('.'+data_field_to_clean).val('');
              }
            }
          }
          scroll(0,last_position) ;
          api.destroy();
        }
      },
      style: 'ui-tooltip-light ui-tooltip-rounded'
    },event);
    return false;
}

function button_ref_clear(event)
{
  $(this).prevAll('.ref_name').text('-').show();
  $(this).prevAll('input').val('');
  $(this).next().find('.but_text .on').removeClass('hidden');
  $(this).next().find('.but_text .off').addClass('hidden');
  $(this).hide();

  $(this).prevAll('.ref_name').prev().trigger('clear');
}
