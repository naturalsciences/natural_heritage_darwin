var sfDoubleList =
{
  init: function(id, className)
  {
    form = sfDoubleList.get_current_form(id);
    callback = function() { sfDoubleList.submit(form, className) };

    if (form.addEventListener)
    {
      form.addEventListener("submit", callback, false);
    }
    else if (form.attachEvent)
    {
      var r = form.attachEvent("onsubmit", callback);
    }
  },

  move: function(srcId, destId)
  {
    var src = document.getElementById(srcId);
    var dest = document.getElementById(destId);
    for (var i = 0; i < src.options.length; i++)
    {
      if (src.options[i].selected)
      {
        dest.options[dest.length] = new Option(src.options[i].text, src.options[i].value);
        src.options[i] = null;
        --i;
      }
    }
  },

  submit: function(form, className)
  {
    var element;
    for (var i = 0; i < form.elements.length; i++)
    {
      element = form.elements[i];
      if (element.type == 'select-multiple')
      {
        if (element.className == className + '-selected')
        {
          for (var j = 0; j < element.options.length; j++)
          {
            element.options[j].selected = true;
          }
        }
      }
    }
  },

  get_current_form: function(el)
  {
    if ("form" != el.tagName.toLowerCase())
    {
      return sfDoubleList.get_current_form(el.parentNode);
    }

    return el;
  }
};


//Function in charge of sorting an array
// descending parameter available if wished to sort alphabetically inversed
function sort_array(list_array, descending)
{
  var list_options_text = new Array();
  var results = new Array();
  var i;
  var j;
  var selectedOption;
  // Browse the array and generate a new one with the text part
  for (i in list_array)
  {
    list_options_text[i] = list_array[i]["text"];
  }
  // Sort this new array
  list_options_text.sort();
  // If alphabetically inversion wished, reverse the array
  if(descending)
  {
    list_options_text.reverse();
  }
  // Browse text values array and inner it test correspondance in original array
  for (i in list_options_text)
  {
    for (j in list_array)
    {
      // Generate a new array as a copy of original one but resorted as wished
      if(list_options_text[i] == list_array[j]["text"])
      {
        results[i] = list_array[j];
      }
    }
  }
  return results;
}
// Function creating an array of structure:
// [["value","text","selected"],["value","text","selected"],...]
// from an html select
function array_from_options(list)
{
  var results = new Array();
  $(list).find('option').each(function(index)
  {
    var list_options = new Array();
    list_options["value"] = $(this).val();
    list_options["text"] = $(this).text();
    list_options["selected"] = $(this).attr("selected");
    results[index] = list_options;
  });
  return results;
}
// Filter an array with the text passed as second parameter
// The array must be of structure: [["value","text","selected"],["value","text","selected"],...]
// A new array resulting of the first one filtered is generated
function filter_array(list_array, filter_text)
{
  var results = new Array();
  var i;
  var j=0;
  exp='.*'+filter_text+'.*';
  Expression = new RegExp(exp,'i');
  for(i in list_array)
  {
    if(Expression.test(list_array[i]["text"]))
    {
      results[j]=list_array[i];
      j++;
    }
  }
  return results;
}
// Function modifying the selected value of an array passed as parameter if
// text value of this array correspond to text given as second parameter
// The selected value taken is the third parameter
function select_option_in_array(list_array, list_array_text, selectedFlag)
{
  var i;
  for (i in list_array)
  {
    if(list_array[i]["text"] == list_array_text)
    {
      list_array[i]["selected"] = selectedFlag;
    }
  }
  return list_array;
}
// Function returning an array of structure:
// ["value","text","selected"]
// form an html select option passed as parameter
function list_options_from(element)
{
  var list_options = new Array();
  list_options["value"] = $(element).val();
  list_options["text"] = $(element).text();
  list_options["selected"] = $(element).attr('selected');
  return list_options;
}
// Function pushing to an array an array of structure:
// ["value","text","selected"]
function add_to_array(list_array, list_options)
{
  list_array.push(list_options);
  return list_array;
}
// Function removing from array a corresponding entry given as "criteria"
// The comparison is value based if "value_based" is true and text based if "value_based" is false
function remove_from_array(list_array, criteria, value_based)
{
  var i;
  for (i in list_array)
  {
    if (value_based)
    {
      if(list_array[i]["value"]==criteria)
      {
        list_array.splice(i,1);
      }
    }
    else
    {
      if(list_array[i]["text"]==criteria)
      {
        list_array.splice(i,1);
      }
    }
  }
  return list_array;
}
// Function generating an html list of options from an array of structure:
// [["value","text","selected"],["value","text","selected"],...]
function html_options_from_array(list_array)
{
  var response = '';
  var i;
  for (i in list_array)
  {
    var selectedOption = (list_array[i]["selected"])?'selected="selected"':'';
    response = response+'<option value="'+list_array[i]["value"]+'" '+selectedOption+'>'+list_array[i]["text"]+'</option>';
  }
  return response;
}

// Function to set title of all options in a select = to their value
function set_options_title(listConcerned)
{
  $(listConcerned).find('option').each(function(index)
  {
    $(this).attr('title', $(this).text());
  });
}
