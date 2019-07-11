(function($) {

   $.catalogue_people = function(element, options) {
      options = $.extend({}, $.fn.catalogue_people.defaultOptions, options);
      $(element).find('>tbody').sortable({
        placeholder: 'ui-state-highlight',
        handle: options['handle'],
        axis: 'y',
        change: function(e, ui) {
              $.fn.catalogue_people.forceCollectorsHelper(e,ui);
            },
        deactivate: function(event, ui) {
              $.fn.catalogue_people.reorder(element);
        }
      });

      $(options['add_button']).click(function(event){
        event.preventDefault();
        var last_position = $('body').scrollTop() ;      
        scroll(0,0) ;
        $(this).qtip({
          id: 'modal',
          content: {
            text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
            title: { button: true, text: options['q_tip_text'] },
            ajax: {
              url: $(this).attr('href'),
              type: 'GET'
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
              fct_update = options['update_row_fct'];
            },
            hide: function(event, api) {
              $('.result_choose').die('click') ;
              fct_update = undefined ;
              scroll(0,last_position) ;
              api.destroy();
            }
          },
          style: 'ui-tooltip-light ui-tooltip-rounded'
        }
          
        );
        return false;
      });      
      return this;
   };
  
  $.fn.catalogue_people = function(options) {
    return this.each(function() {
       (new $.catalogue_people($(this), options));
    });
  };

  $.fn.catalogue_people.forceCollectorsHelper = function (e,ui){
    $(".ui-state-highlight").html("<td colspan='3' style='line-height:"+ui.item[0].offsetHeight+"px'>&nbsp;</td>");
  };
  
  $.fn.catalogue_people.reorder = function(el){
      $(el).find('tr:visible').each(function (index, item){
        $(item).find('input[id$=\"_order_by\"]').val(index+1);
      });
    };
    
  $.fn.catalogue_people.defaultOptions = {
    table_col_num: 3,
    handle: '.spec_ident_collectors_handle',
    add_button: 'a#add_button',
    q_tip_text: 'Choose a Member',
    update_row_fct: undefined
  }

})(jQuery);




