<?php slot('title', __('Search sampling location'));  ?>
<div class="page">
<h1><?php echo __('Sampling location search');?></h1>

<?php if($sf_params->get('with_js') == '1' || $sf_params->get('with_js') === true):?>

<input type="hidden" id="http_referer" name="http_referer" value="<?php print($_SERVER["HTTP_REFERER"]);?>">
<script language="javascript">
$(document).ready(function () {
    $('.result_choose').live('click',chooseGtu);
   
});



function chooseGtu(event)
{
  el = $(this).closest('tr');
 
  ref_element_id = getIdInClasses(el);
  //ftheeten 2018 12 13
  cell=el.find('td.item_name');
  //2019 02 28
  var referer = $("#http_referer").val();
  if(referer.indexOf("/staging/edit/")!=-1)
  {   
    ref_element_name = el.find(".gtu_code").html();   
  }
  else
  {
    ref_element_name = cell.html();
  }
  //ftheeten 2018 12 13
  if($(event.target).attr("name")=="date_choose")
  {   
    //see _refGtu template
    //var selectedindex = $("#select_gtu_date").text();
    //console.log(selectedindex);
    var date_tags = el.find('td.temporal_information_value');
    
   
    ref_element_id = getGtuId(date_tags);
    var gtu_id=ref_element_id.split("_")[0];

    var idSelectDate="#select_gtu_date_id_"+ref_element_id;

    var tags_class=".temporalinformation_date_id_"+$(idSelectDate).val();

    var date_tags=el.find(tags_class);
    var gtu_address='.class_gtu_id_'+gtu_id;
    var gtu_tags=$(gtu_address);
    
    gtu_tags.html(gtu_tags.html()+date_tags.html());
    ref_element_name=gtu_tags.html();
  }
  else if($(event.target).attr("name")=="gtu_choose")
  {
    var gtu_id=ref_element_id.split("_")[0];
    var idSelectDate="#select_gtu_date_id_"+ref_element_id;

    var tags_class=".temporalinformation_date_id_"+$(idSelectDate).val();

    var date_tags=el.find(tags_class);
    var gtu_address='.class_gtu_id_'+gtu_id;
    var gtu_tags=$(gtu_address);
    
    gtu_tags.html(gtu_tags.html());
    ref_element_name=gtu_tags.html();
  }

  $('.result_choose').die('click');
  $('body').trigger('close_modal');
}


function chooseGtuInMap(id)
{
  ref_element_id = id;
  ref_element_name = $('.map_result_id_'+id+' .item_name').html();
  $('body').trigger('close_modal');
}

function getGtuId(el)
{
    var classes = $(el).attr("class").split(" ");
    for ( var i = 0; i < classes.length; i++ )
    {
        exp = new RegExp("temporalinformation_gtu_id_([-]?[0-9]+)",'gi');
        var result = exp.exec(classes[i]) ;
        if ( result )
        {
            return result[1];
        }
    }
}



</script>
<?php endif;?>
  <?php include_partial('searchForm', array('form' => $form, 'is_choose' => true)) ?>

</div>