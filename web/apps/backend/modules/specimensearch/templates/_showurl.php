<input  name="toggle_query_link<?php print($id);?>" id="toggle_query_link<?php print($id);?>" class="show_query_link" type="button" value="<?php print(__("Show URL")); ?>" ></button>
      <div style="display:none" name="show_query_link<?php print($id);?>" id="show_query_link<?php print($id);?>">
      <?php 
        //ftheeten 2018 04 17
        
       
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $tmp_array=$_POST;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
     $tmp_array=$_GET;
    }
    $tmp_array=array_filter_recursive($tmp_array);
    $abs_url=url_for("specimensearch/search/is_choose/",true)."/".$currentPage."?".http_build_query($tmp_array);
    print("Absolute URL <br/><a target='_blank' href='$abs_url'>".urldecode($abs_url)."</a>");?>
    </div>
<script type="text/javascript">

$(document).ready(function () {
     //ftheeten 2018 04 17
  $("#toggle_query_link<?php print($id);?>").click(
    function()
    {
        if(!$('#show_query_link<?php print($id);?>').is(':visible'))
        {
            $("#show_query_link<?php print($id);?>").css("display", "block");
        }
        else
        {
            $("#show_query_link<?php print($id);?>").css("display", "none");
        }
    }
  );
});
</script>