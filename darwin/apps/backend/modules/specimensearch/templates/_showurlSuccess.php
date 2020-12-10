<input  name="toggle_query_link" id="toggle_query_link" type="button" value="<?php print(__("Show URL")); ?>" ></button>
      <div style="display:none" name="show_query_link" id="show_query_link"
      <?php 
        
    if ($method === 'POST') {
     $tmp_array=$postMapper;
    }
    elseif ($method === 'GET') {
     $tmp_array=$getMapper;
    }
    $tmp_array=array_filter_recursive($tmp_array);
    $abs_url=url_for($s_url,true)."?".http_build_query($tmp_array);
    print("Absolute URL <br/><a target='_blank' href='$abs_url'>".urldecode($abs_url)."</a>");?>
    </div>
<script type="text/javascript">

$(document).ready(function () {
     //ftheeten 2018 04 17
  $("#toggle_query_link").click(
    function()
    {
        if(!$('#show_query_link').is(':visible'))
        {
            $("#show_query_link").show();
        }
        else
        {
            $("#show_query_link").hide();
        }
    }
  );
});
</script>