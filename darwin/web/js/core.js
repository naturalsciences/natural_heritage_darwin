function getIdInClasses(el)
{
    var classes = $(el).attr("class").split(" ");
    for ( var i = 0; i < classes.length; i++ )
    {
        exp = new RegExp(".*id_([-]?[0-9]+)",'gi');
        var result = exp.exec(classes[i]) ;
        if ( result )
        {
            return result[1];
        }
    }
}

function check_screen_size()
{
  if($(window).width() < 1100) {
    $('head').append('<link rel="stylesheet" id="tiny" type="text/css" href="/css/tiny.css">') ;
  }
  else {
    $('#tiny').remove() ;
  }
}

function getElInClasses(element,prefix)
{
    var classes = $(element).attr("class").split(" ");
    for ( var i = 0; i < classes.length; i++ )
    {
        exp = new RegExp(prefix+"(.*)",'gi');
        var result = exp.exec(classes[i]) ;
        if ( result )
        {
            return result[1];
        }
    }
}

function removeAllQtip()
{
    var i = $.fn.qtip.interfaces.length; while(i--)
    {
            // Access current elements API
        var api = $.fn.qtip.interfaces[i];
            // Queue the animation so positions are updated correctly
        if(api && api.status.rendered && !api.status.hidden && !api.elements.target.is('.button'))
            api.destroy();
    };
}

function addBlackScreen()
{
    $(document).ready(function()
    {
        $('<div id=\"qtip-blanket\">')
            .css({
                top: $(document).scrollTop(), // Use document scrollTop so it's on-screen even if the window is scrolled
                left: 0,
                height: $(document).height() // Span the full document height...
            })
            .appendTo(document.body) // Append to the document body
            .hide(); // Hide it initially
    });
}

function hideForRefresh(el)
{
  $(el).css({position: 'relative'})
  $(el).append('<div id="loading_screen" />')
}

function showAfterRefresh(el)
{
  $(el).children('#loading_screen').remove();
}

function trim(myString)
{
  return jQuery.trim(myString);
}

function showValues()
{
  $(this).parent().find('ul').slideDown();
  $(this).parent().find('.hide_value').show();
  $(this).hide();
  return false;
}

function hideValues()
{
  $(this).parent().find('ul').slideUp();
  $(this).parent().find('.display_value').show();
  $(this).hide();
  return false;
}


function clearPropertyValue()
{
  parent_el = $(this).closest('tr');
  $(parent_el).find('input').val('');
  $(parent_el).hide();
}

$(document).ready(function()
{
  $(this).ajaxStart(function(){
    $('#load_indicator').fadeIn();
  });

  $(this).ajaxComplete(function(){
    $('#load_indicator').fadeOut();
  });
});

function returnText(object)
{
  var obj = object.jquery ? object[0] : object;
  if(typeof obj.selectionStart == 'number')
  {
    return obj.value.substring(obj.selectionStart, obj.selectionEnd);
  }
  else if(document.selection)
  {
    // Internet Explorer
    obj.focus();
    var range = document.selection.createRange();
    if(range.parentElement() != obj) return false;

    if(typeof range.text == 'string')
    {
      return range.text;
    }
  }
  else
    return false;
}

function clearSelection(el)
{
  t = el.val();
  el.val('');
  el.val(t);
}

function result_choose ()
{
    el = $(this).closest('tr');
    ref_element_id = getIdInClasses(el);
    ref_element_name = el.find('span.item_name').text();
    ref_level_name = el.find('span.level_name').text();
    if(typeof fct_update=="function")
    {
        fct_update(ref_element_id, ref_element_name, ref_level_name);
    }
    else
    {
        $('.result_choose').die('click');
        $('body').trigger('close_modal');
    }
}

function objectsAreSame(x, y) {
   var objectsAreSame = true;
   for(var propertyName in x) {
      if(x[propertyName] !== y[propertyName]) {
         objectsAreSame = false;
         break;
      }
   }
   return objectsAreSame;
}

function attachHelpQtip(element)
{
  $(element).find(".help_ico").qtip({
    content: {
      text: function(api) {
        return $(this).find('span').html();
      }
    },
    style: {
      tip: "bottomLeft",
      classes: "ui-tooltip-dwgreen ui-tooltip-rounded"
    },
    position: {
      my: "bottom left",
      at: "top right"
    }
  });
}

function postToUrl(url, params, newWindow)
{
    var form = $('<form>');
    form.attr('action', url);
    form.attr('method', 'POST');
    if(newWindow){ form.attr('target', '_blank'); }

    var addParam = function(paramName, paramValue){
        var input = $('<input type="hidden">');
        input.attr({ 'id':     paramName,
                     'name':   paramName,
                     'value':  paramValue });
        form.append(input);
    };

    // Params is an Array.
    if(params instanceof Array){
        for(var i=0; i<params.length; i++){
            addParam(i, params[i]);
        }
    }

    // Params is an Associative array or Object.
    if(params instanceof Object){
        for(var key in params){
            addParam(key, params[key]);
        }
    }

    // Submit the form, then remove it from the page
    form.appendTo(document.body);
    form.submit();
    form.remove();
}

function getSearchColumnVisibilty() {
  column_arr = [];
  $('ul.column_menu .col_switcher :checked').each(function(){
    column_arr.push($(this).val());
  });
  column_str = column_arr.join('|');
  return column_str;
}

function decodeBase64(s) {
    var e={},i,b=0,c,x,l=0,a,r='',w=String.fromCharCode,L=s.length;
    var A="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    for(i=0;i<64;i++){e[A.charAt(i)]=i;}
    for(x=0;x<L;x++){
        c=e[s.charAt(x)];b=(b<<6)+c;l+=6;
        while(l>=8){((a=(b>>>(l-=8))&0xff)||(x<(L-2)))&&(r+=w(a));}
  }
  return r;
}

//http://www.1stwebmagazine.com/jquery-checkbox-and-radio-button-styling
;(function(){
$.fn.customRadioCheck = function() {

  return this.each(function() {

    var $this = $(this);
    var $span = $('<span/>');

    $span.addClass('custom-'+ ($this.is(':checkbox') ? 'check' : 'radio'));
    $this.is(':checked') && $span.addClass('checked'); // init
    $span.insertAfter($this);

    $this.parent('label').addClass('custom-label')
      .attr('onclick', ''); // Fix clicking label in iOS
    // hide by shifting left
    $this.css({ position: 'absolute', left: '-9999px' });

    // Events
    $this.on({
      update: function() {
        if ($this.is(':radio')) {
          $this.parent().siblings('label')
            .find('.custom-radio').removeClass('checked');
        }
        $span.toggleClass('checked', $this.is(':checked'));
      },
      change: function() {
        $this.trigger('update');
      },
      focus: function() { $span.addClass('focus'); },
      blur: function() { $span.removeClass('focus'); }
    });
  });
};
}());


    //ftheeten 2018 09 18
    function onElementInserted(containerSelector, elementSelector, callback) {

            var onMutationsObserved = function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        var elements = $(mutation.addedNodes).find(elementSelector);
                        for (var i = 0, len = elements.length; i < len; i++) {
                            callback(elements[i]);
                        }
                    }
                });
            };

            var target = $(containerSelector)[0];
            var config = { childList: true, subtree: true };
            var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
            var observer = new MutationObserver(onMutationsObserved);    
            observer.observe(target, config);

        }
        
        
    //ftheeten 2018 11 26
    var select2SetOption=function(selector, valueToCopy, textToCopy)
    {
          var newOption = new Option( textToCopy, valueToCopy, true, true);
          $(selector).append(newOption).trigger('change');
          $(selector).trigger({
                                    type: 'select2:select',
                                    params: {
                                    data: {id:valueToCopy, text:textToCopy}
                                    }
                                    });
          var data = $(selector).select2('data');
                               
         $(selector).select2("data", data, true);
    }
    
    //ftheeten 2019 02 04
    var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
		return false;
	};
		
	$(function() {
		$('img[class^="clear_"]').click(
            function(){
                if(location.pathname.indexOf('/specimen/')!=-1)
                {
                    var root=$(this);
                    var ctrl =$(this).siblings('input[name*="[id]"]');
                    var val_id=$(ctrl).val();					
                    var name_ctrl=$(ctrl).attr("name");
                    if(name_ctrl.indexOf("[")!=-1)	{
                        var array_val= name_ctrl.split(/[\[\]]/);
                        var obj_name=array_val[0];
                        var table_name=array_val[1];
                        var num= array_val[3];
                        if(array_val.length>1){	
                            //attention path is not the same as on Tervuren RBINS server (Darwin is linked to the root of the URL in RBINS)
                            var array_url = location.pathname.split("/") ;
                            if(array_url.length>=4)
                            {
                                var url_tmp=array_url.slice(0, array_url.length-4).join("/");                         
                                var url=location.protocol + '//' + location.host + "/"+  url_tmp + "/specimen/deleteLinkedObject";
                                $.getJSON( url, {id: val_id, table: table_name}).done(function( data ) {           
                                        obj = data;                                    
                                            if(obj.deleted=="yes")
                                            {
                                                    if($.isNumeric(num))
                                                    {
                                                        var radical=obj_name+"["+table_name+"]["+num+"]";
                                                        var ctrls =$('[name*="'+radical+'"]');
                                                        ctrls.each(
                                                            function(i, tmp_input)
                                                            {
                                                                $(tmp_input).remove();
                                                            }
                                                        );
                                                        //ensure that all the idnex of HTML cotnrol names are consecutive after removal of a control (otherwise error by Symfony validation)
                                                        var array_no_num=Array();
                                                        var findMax=obj_name+"["+table_name+"]";
                                                        var ctrls2 =$('[name*="'+findMax+'"]');
                                                        if(ctrls2.length)
                                                        {
                                                            var arr = ctrls2.map(function(_, o) { return { t: $(o).attr("name")}; }).get();
                                                            arr.sort(function(o1, o2) { return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0; });
                                                            var last = arr[arr.length-1];
                                                            var regex= /\[\d+\]/
                                                            var array_regex= regex.exec(last.t);
                                                            var vax_idx =-1;
                                                            if(array_regex.length>0)
                                                            {

                                                                max_idx=array_regex[0].replace(/\[|\]/g,'');
                                                                for(var i=0; i<=max_idx; i++)
                                                                {
                                                                    var name_to_test=obj_name+"["+table_name+"]["+i.toString()+"]";
                                                                    var ctrls3 =$('[name*="'+name_to_test+'"]');
                                                                    if(!ctrls3.length)
                                                                    {
                                                                       array_no_num.push(i);
                                                                    }                                                              
                                                                }
                                                                for(var i=0; i<array_no_num.length; i++)
                                                                {
                                                                    var idx_tmp=array_no_num[i];
                                                                    for(j=idx_tmp+1; j<= max_idx; j++)
                                                                    {
                                                                        var pattern_to_replace=obj_name+"["+table_name+"]["+j.toString()+"]";
                                                                        var newJ=j-1;
                                                                        var new_pattern=obj_name+"["+table_name+"]["+ newJ.toString()+"]";
                                                                        var ctrls4 =$('[name*="'+pattern_to_replace+'"]');
                                                                        if(ctrls4.length)
                                                                        {                                                                    
                                                                           ctrls4.each(
                                                                            function(i, tmp_input)
                                                                            {
                                                                                var old_name=$(tmp_input).attr("name");
                                                                                var new_name= old_name.replace(pattern_to_replace, new_pattern);
                                                                                $(tmp_input).attr("name", new_name);
                                                                                
                                                                            }
                                                                        );
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            
                                                         }
                                                    }
                                            }
                                        }
                                    );
                                }
                                                    
                            }
                    }
                }
			}
		);
	});