(function($){
    $.duplicatable = function(el, options){

        var base = this;
        base.$el = $(el);
        base.el = el;

        base.$el.data("duplicatable", base);

        base.init = function(){
            base.options = $.extend({},$.duplicatable.defaultOptions, options);

            if(base.options['duplicate_binding_type'] == "live")
            {
              $(base.options['duplicate_link']).live('click',base.duplicateItem);
            }
            else
            {
              $(base.options['duplicate_link']).click(base.duplicateItem);
            }
        };

        base.duplicateItem = function(event)
        {
          self = this;
          var last_position = $('body').scrollTop() ;
          scroll(0,0);
          event.preventDefault();
          $(this).qtip({
            content: {
              title: $(self).text(),
              ajax: {
                url: base.options['duplicate_href'],
                type: 'GET'
              },
              text: ' '
            },
            show: {
              ready: true,
              delay: 0,
              modal: {
                on: true,
                blur: false
              }
            },
            hide: {
              event: 'close_modal',
              target: $('body')
            },
            position: {
              my: 'top center',
              at: 'top center',
              adjust:{
                y: 250 // option set in case of the qtip become too big
              },
              target: $(document.body)
            },
            style: ' ui-tooltip-rounded ui-tooltip-dialogue',
            events: {
              hide: function(event, api) {
                if (element_name == null)
                {
                  scroll(0,last_position);
                  return false ;
                }
                else
                {
                  new_link = $(self).attr('href') + element_name ;
                  document.location.href = new_link;
                }

              }
            }
            });

          };

        base.init();
    };

    $.duplicatable.defaultOptions = {
       duplicate_link: "a.duplicate_link",
       duplicate_href: "",
       duplicate_binding_type: "normal"
    };

    $.fn.duplicatable = function(options){
        return this.each(function(){
            (new $.duplicatable(this, options));
        });
    };

})(jQuery);

(function($){
    $.catalogue = function(el, options){
        // To avoid scope issues, use 'base' instead of 'this'
        // to reference this class from internal events and functions.
        var base = this;
        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data("catalogue", base);

        base.init = function(){
            base.options = $.extend({},$.catalogue.defaultOptions, options);
            $(base.options['link_catalogue']).live('click', base.catalogueLinkEdit);
            $(base.options['delete_link']).live('click', base.deleteItem);
            // Put your initialization code here
        };

        base.catalogueLinkEdit = function(event)
        {
          event.preventDefault();
          var last_position = $('body').scrollTop() ;
          var style = 'dialog-modal-edit';
          if($(this).attr('information')) style = 'dialog-modal-view' ;
          scroll(0,0);
          $(this).qtip({
            id: 'modal',
            content: {
              text: '<img src="/images/loader.gif" alt="loading"> loading ...',
              title: { button: true, text: $(this).attr('title') },
              ajax: {
                url: $(this).attr('href'),
                      type: 'get'
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
                $('body').data('widgets_screen').refreshWidget(event, api.elements.target);
                scroll(0,last_position);
                api.destroy();
              }
            },
            style: 'ui-tooltip-light ui-tooltip-rounded '+style
          },event);
        };


        base.deleteItem = function(event)
        {
          event.preventDefault();
          if(confirm($(this).attr('title')))
          {
            currentElement = $(this);
            removeError($(this));
            $.ajax({
              url: $(this).attr('href'),
              success: function(html)
              {
                if(html == "ok" )
                {
                  $('body').data('widgets_screen').refreshWidget(event, currentElement);
                }
                else
                {
                  addError(html, currentElement); //@TODO:change this!
                }
              },
              error: function(xhr)
              {
                addError('Error!  Status = ' + xhr.status);
              }
            });
          }
        };

        // Run initializer
        base.init();
    };

    $.catalogue.defaultOptions = {
      link_catalogue: "a.link_catalogue",
      delete_link: "a.widget_row_delete"
    };

    $.fn.catalogue = function(options){
        return this.each(function(){
            (new $.catalogue(this, options));
        });
    };

})(jQuery);


/******************************************
 * TO BE REMOVED
 * *****************************************/
function addError(html, element)
{
  $(element).closest('.widget_content').find('.error_list li').text(html);
  $(element).closest('.widget_content').find('.error_list').show();
}

function removeError(element)
{
  $(element).closest('.widget_content').find('.error_list').hide();
  $(element).closest('.widget_content').find('.error_list li').text(' ');
}

/*****************************************
 * END TO BE REMOVED
 * *****************************************/


/** CATALOGUE LEVEL CHECKING ****/
(function($){
    $.catalogue_level = function(el, options){
        // To avoid scope issues, use 'base' instead of 'this'
        // to reference this class from internal events and functions.
        var base = this;

        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data("catalogue_level", base);

        base.init = function(){
            base.options = $.extend({},$.catalogue_level.defaultOptions, options);
            $(base.options['level_ref']).change(base.changeLevel);
            $(base.options['parent_ref']).bind('loadref',base.beforeSearchParent);
            $(base.options['parent_ref']).change(base.changeParent);
        };

        base.changeLevel = function(event)
        {
          event.preventDefault();
          if ($(this).val() == '')
          {
            base.$el.fadeOut();
          }
          else
          {
            base.$el.fadeIn();
            if (base.options['search_url'].length)
            {
              base.checkUpperLevel();
            }
          }
        };

        base.checkUpperLevel = function()
        {
          parent_item = $(base.options['parent_ref']); //base.$el.find('input[id$=\"_parent_ref\"]');
          $.ajax({
            url: base.options['search_url'] +'/level_id/' + $(base.options['level_ref']).val() + '/parent_id/' + $(base.options['parent_ref']).val() ,
            success: function(html)
            {
              switch (html)
              {
                case "top":
                  $(base.options['button_ref']).fadeOut();
                  $(base.options['message_ref']).fadeOut();
                  $(base.options['parent_ref']).val("");
                  $(base.options['parent_name']).html("-");
                  break;
                case "not ok":
                  $(base.options['button_ref']).fadeIn();
                  $(base.options['message_ref']).fadeIn();
                  break;
                default:
                  $(base.options['button_ref']).fadeIn();
                  $(base.options['message_ref']).fadeOut();
                  break;
              }
            }
          });
        }

        // Called just before the showing of the parent_ref chooser
        base.beforeSearchParent = function(event)
        {
          ref_level_id = $(base.options['level_ref']).val()
          if (ref_level_id.length)
          {
            ref_level_id = '/level/'+ref_level_id;
          }

          ref_caller_id = '';
          if (base.options['current_id'] != undefined && base.options['current_id'] != '')
          {
            ref_caller_id = '/caller_id/' + base.options['current_id'];
          }

          button_ref = $(base.options['button_ref']).find('.but_text');
          if(button_ref.data('href') == null)
          {
            button_ref.data('href', button_ref.attr('href'));
          }
          button_ref.attr('href', button_ref.data('href') + ref_level_id + ref_caller_id);
        };

        base.changeParent = function(event)
        {
          base.checkUpperLevel();
        };

        // Run initializer
        base.init();
    };

    $.catalogue_level.defaultOptions = {
        level_ref: 'select.catalogue_level',
        button_ref:  'div[id$=\"_parent_ref_button\"]',
        parent_name: 'div[id$=\"_parent_ref_name\"]',
        parent_ref: 'tr#parent_ref input[type="hidden"]',
        message_ref: 'div[id$=\"_parent_ref_warning\"]',
        current_id: '',
        search_url: ''
    };

    $.fn.catalogue_level = function(options){
        return this.each(function(){
            (new $.catalogue_level(this, options));
        });
    };

})(jQuery);


/**********************************
 *  Modal screen
 * ***************************/

(function($){
    $.modal_screen = function(el, options){
        // To avoid scope issues, use 'base' instead of 'this'
        // to reference this class from internal events and functions.
        var base = this;

        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;

        // Add a reverse reference to the DOM object
        base.$el.data("modal_screen", base);

        base.init = function(){
            base.options = $.extend({},$.modal_screen.defaultOptions, options);
            base.$el.submit(base.onSubmit);
            base.$el.find(base.options['delete_button']).click(base.deleteRecord);
            base.$el.find(base.options['cancel_button']).click(base.cancelButton);
            // Put your initialization code here
        };

        base.onSubmit = function(event){
          event.preventDefault();
          form_el = $(this);
          form_el.find('input[type=submit]').attr('disabled','disabled');
          hideForRefresh( form_el.parent() );
          $.ajax({
            type: "POST",
            url: form_el.attr('action'),
            data: form_el.serialize(),
            success: function(html){
              if(html == 'ok')
              {
                $('body').trigger('close_modal');
              }
              form_el.parent().before(html).remove();
            }
          });
        };

        base.cancelButton = function (event)
        {
            event.preventDefault();
            $('body').trigger('close_modal');
        };

        base.deleteRecord = function (event)
        {
          event.preventDefault();
          if(confirm($(this).attr('title')))
          {
            hideForRefresh( base.$el );
            currentElement = $(this);
            removeError($(this));
            $.ajax({
              url: $(this).attr('href'),
              success: function(html)
              {
                if(html == "ok" )
                {
                  $('body').trigger('close_modal');
                }
                else
                {
                  addError(html, currentElement); //@TODO:change this!
                }
              },
              error: function(xhr)
              {
                addError('Error!  Status = ' + xhr.status);
              }
            });
          }
        };
        // Run initializer
        base.init();
    };

    $.modal_screen.defaultOptions = {
        delete_button: '.delete_button',
        cancel_button: '.cancel_qtip'
    };

    $.fn.modal_screen = function(options){
        return this.each(function(){
            (new $.modal_screen(this, options));
        });
    };

})(jQuery);
