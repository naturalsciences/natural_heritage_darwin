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
  if($(window).width() < 1100)
    $('head').append('<link rel="stylesheet" id="tiny" type="text/css" href="/css/tiny.css">') ;
  else
    $('#tiny').remove() ;
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
};

function hideValues()
{
  $(this).parent().find('ul').slideUp();
  $(this).parent().find('.display_value').show();
  $(this).hide();
  return false;
};


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
        $('.result_choose').die('click');
        $('body').trigger('close_modal');
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





/**********************************
 * Specimen Search related functions
 * 
 ************************************/

/***
 * Update the visible columns list in specimens_search
 */
function update_list(li)
{
  if ( li.hasClass('check')) {
    li.removeClass('check') ;
    li.addClass('uncheck') ; 
  }
  else {
    li.removeClass('uncheck') ;
    li.addClass('check') ;
  }
}

function getColVisible()
{
  column_str = '';
  if($('.column_menu ul > li.check').length)
  {
    $('.column_menu ul > li.check').each(function (index)
    {
      if(column_str != '') column_str += '|';
      column_str += $(this).attr('id').substr(3);
    });
  }
  return column_str;
}

/***
 * Hide or show table column when a column is checked as visible
 */
function hide_or_show(li)
{
  field = li.attr('id') ;
  column = field.substr(3) ;
  
  if(li.hasClass('uncheck')) {
    $('table.spec_results thead tr th.col_'+column).hide();
    $('table.spec_results tbody tr td.col_'+column).hide();
    //this line below is neccessary to avoid table border to be cut
  }
  else {
    $('table.spec_results thead tr th.col_'+column).show();
    $('table.spec_results tbody tr td.col_'+column).show();
    //this line below is neccessary to avoid table border to be cut    
  }
}

