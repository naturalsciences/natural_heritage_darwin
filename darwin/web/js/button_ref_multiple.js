var ref_element_id = null;
var ref_element_name = null;
var ref_level_name = null;
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
        $($.fn.button_ref_multiple.options['add_button']).off("click").on("click",function(event)
        {
            event.preventDefault();
            var last_position = $(window).scrollTop();
            scroll(0,0) ;
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
                        ref_level_name = null;
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
        names_list_target_table_id:"",
        partial_url:"",
        attached_field_id:""
    };

    /*
    Store more permanently the options for the life of javascript object
     */
    $.fn.button_ref_multiple.options = {};

    $.fn.button_ref_multiple.addEntry = function(chosen_ref,chosen_name,chosen_level) {
        var ids_list = $($.fn.button_ref_multiple.options['ids_list_target_input_id']).val().split(',').filter(function(n){return (n != undefined && n != "")});
        var chosen_ref_in_ids_list_index = ids_list.indexOf(chosen_ref);
        if (chosen_ref_in_ids_list_index == -1) {
            ids_list.push(chosen_ref);
            $($.fn.button_ref_multiple.options['ids_list_target_input_id']).val(ids_list.toString());
            $.ajax({
                type: "POST",
                url: $.fn.button_ref_multiple.options['partial_url'],
                data: {
                        field_id:$.fn.button_ref_multiple.options['attached_field_id'],
                        row_data:[{"id":chosen_ref,"name":chosen_name,"level_name":chosen_level}]
                      },
                success: function(html){
                    $(html).appendTo($.fn.button_ref_multiple.options['names_list_target_table_id']+' table tbody');
                    $($.fn.button_ref_multiple.options['names_list_target_table_id']).removeClass('hidden');
                }
            });
        }
    };

    $.fn.button_ref_multiple.removeEntry = function(row_id) {
        var temp_array = row_id.split('_');
        var target_value = temp_array.pop().toString();
        var target_id = temp_array.join('_');
        var ids_list = $('#'+target_id).val().split(',').filter(function(n){return (n != undefined && n != "")});
        var ids_count = ids_list.length;
        var id_to_remove_in_ids_list_index = ids_list.indexOf(target_value);
        if (id_to_remove_in_ids_list_index != -1) {
            ids_list.splice(id_to_remove_in_ids_list_index,1);
            $('#'+target_id).val(ids_list.toString());
            if(ids_count == 1) {
                $('div#'+target_id+'_result_table').addClass('hidden');
            }
            $('tr#'+row_id).remove();
        }
    };

    $.fn.button_ref_multiple.replaceControl = function (event) {
        event.preventDefault();
        if(event.handled !== true) {
            var nearest_tr_class = $('#' + event.data.control_to_replace).closest('tr').attr('class');
            $.ajax({
                type: "POST",
                url: event.data.replacement_url,
                data: {
                    name: event.data.replacement_url_name_param,
                    widgetButtonRefMultipleRefresh: event.data.widget_button_ref_multiple_refresh,
                    catalogue: $(this).val()
                },
                success: function (html) {
                    $(html).appendTo($('#' + event.data.control_to_replace).closest('tbody'));
                    $("." + nearest_tr_class).remove();
                }
            });
        }
    };

})(jQuery);
