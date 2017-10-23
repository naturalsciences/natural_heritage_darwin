var ref_element_id = null;
var ref_element_name = null;
var ref_level_id = '';
var ref_caller_id = '';

function collection_add_user(event){
   var last_position = $('body').scrollTop() ;
    scroll(0,0) ;
    referer = 'collection' ;
    $(this).qtip({
      id: 'modal',
      content: {
        text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
        title: { button: true, text: $(this).attr('name') },
        ajax: {
          url: $(this).attr('href'),
          type: 'GET'
        }
      },
      position: {
        my: 'center',
        at: 'center',
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
          $('.result_choose').die('click') ;
          scroll(0,last_position) ;
          api.destroy();
        }
      },
      style: 'ui-tooltip-light ui-tooltip-rounded'
    });
    return false;
}

function collection_add_rights(event){
   // if ($(this).attr('id') == 'widget') { min_width = 476 } else { min_width = 876 }
    var last_position = $('body').scrollTop() ;
    scroll(0,0) ;
    $(this).qtip({
      id: 'modal',
      content: {
        text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
        title: { button: true, text: $(this).attr('name') },
        ajax: {
          url: $(this).attr('href'),
                 type: 'GET'
        }
      },
      position: {
        my: 'center',
        at: 'center',
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
          scroll(0,last_position) ;
          api.destroy();
        }
      },
      style: 'ui-tooltip-light ui-tooltip-rounded dialog-modal-edit'
      });
    return false;
 }


function addCollRightValue(user_ref)
{
   hideForRefresh('#users_filter');
  $.ajax(
  {
    type: "GET",
    url: $('div#user_right a.hidden').attr('href')+ (0+$('table#user_right tbody tr').length)+'/user_ref/'+user_ref,
    success: function(html)
    {
      $('table#user_right tbody').append(html);
      $('table#user_right tbody tr:last').attr("id" , user_ref) ;
      showAfterRefresh('#users_filter');
    }
  });
  return false;
}

function detachCollRightValue()
{
  parent_el = $(this).closest('tr');
  $(parent_el).hide();
  $(parent_el).removeAttr('id') ;
  $(parent_el).find('input[type=hidden]:first').val('') ;
}
