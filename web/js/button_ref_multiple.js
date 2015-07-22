var ref_element_id = null;
var ref_element_name = null;
var ref_level_id = '';
var ref_caller_id = '';

(function($) {

    /*
    Defines a new callback function that will fill in the options property
    with the merge of the defaultOptions and the options passed as parameter
    It will define also an event click associated to the content of the option
    'add_button' that will display a qtip for a multiselection
    At the end, return the object self
     */
    $.button_ref_multiple = function(element, options) {
        $.fn.button_ref_multiple.options = $.extend({}, $.fn.button_ref_multiple.defaultOptions, options);
        /*
         Define the behavior when the element associated to the add_button option is clicked
         */
        $($.fn.button_ref_multiple.options['add_button']).click(function(event)
        {
            event.preventDefault();
            var last_position = $(window).scrollTop();
            scroll(0,0) ;
            //$(this).parent().parent().find('input[type="hidden"]').trigger({ type:"loadref"});
            $(this).qtip({
                id: 'modal',
                content: {
                    text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
                    title: { button: true, text: $.fn.button_ref_multiple.options['q_tip_text'] },
                    ajax: {
                        url: $(this).attr('href'),
                        type: 'GET',
                        data: {with_js:1}
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
                        fct_update = $.fn.button_ref_multiple.options['update_row_fct'];
                    },
                    hide: function(event, api) {
                        $('.result_choose').die('click') ;
                        fct_update = undefined ;
                        scroll(0,last_position) ;
                        api.destroy();
                    }
                },
                style: 'ui-tooltip-light ui-tooltip-rounded'
            },event);
            return false;
        });
        return this;
    };
    /*
    Make this button_ref_multiple object a callable function waiting for options to be passed
    Will return a button_ref_multiple object with options filled
     */
    $.fn.button_ref_multiple = function(options) {
        return this.each(function() {
            (new $.button_ref_multiple($(this), options));
        });
    };
    /*
    Set the defaultOptions properties of the object
     */
    $.fn.button_ref_multiple.defaultOptions = {
        table_col_num: 3,
        add_button: 'a.but_text_multiple',
        q_tip_text: 'Choose a catalogue unit',
        update_row_fct: undefined,
        ids_list_target_input_id: "",
        names_list_target_table_id:""
    }

    /*
    Store more permanently the options for the life of javascript object
     */
    $.fn.button_ref_multiple.options = {}

    /*
    @ToDo To define the behavior... not functionnal for the moment
     */
    $.fn.button_ref_multiple.addEntry = function(chosen_ref,chosen_name) {
        $($.fn.button_ref_multiple.options['ids_list_target_input_id']).val(chosen_ref);
        console.log($($.fn.button_ref_multiple.options['ids_list_target_input_id']).val());
    }

})(jQuery);
