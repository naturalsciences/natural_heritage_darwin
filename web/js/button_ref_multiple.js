var ref_element_id = null;
var ref_element_name = null;
var ref_level_id = '';
var ref_caller_id = '';

function button_ref_multiple_modal(event)
{
    event.preventDefault();
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
                data: {with_js:1} //, name: $(this).parent().parent().find('input.ref_name').val(), level: $('table.classifications_edit').find('select.catalogue_level').val()}
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
            parent_el = api.elements.target.parent().prevAll('.ref_multiple_ids');
            if(parent_el.get( 0 ).nodeName == 'INPUT')
              parent_el.val(ref_element_id);
          }

          scroll(0,last_position) ;
          api.destroy();
        }
      },
      style: 'ui-tooltip-light ui-tooltip-rounded'
    },event);
    return false;
}

/*
function button_ref_clear(event)
{
  $(this).prevAll('.ref_name').text('-').show();
  $(this).prevAll('input').val('');
  $(this).next().find('.but_text .on').removeClass('hidden');
  $(this).next().find('.but_text .off').addClass('hidden');
  $(this).hide();

  $(this).prevAll('.ref_name').prev().trigger('clear');
}
*/
