<?php slot('title', __('Reports'));  ?>

<div class="page">

<h1><?php echo __('Reports');?></h1>
  <div class="already_asked_form" id="unit_original_name">
  </div>
  <table class="search">
    <tr>
      <th><?php echo __('Choose a report');?> : </th>
      <th><select id="report_list">
        <option value=""></option>
        <?php foreach($report_list as $key=>$report) : ?>
          <option value="<?php echo $key ; ?>"><?php echo $report['name_'.$sf_user->getCulture()] ; ?></option>
        <?php endforeach ; ?>
      </selec>
      </th>
    </tr>
  </table>
  <div class="report_form">
  </div>  
</div>
<script type="text/javascript">
  function refresh_reports()
  {
    $.ajax({
        url: '<?php echo url_for("report/getAskedReport") ; ?>',
        success: function(html) {
        $(".already_asked_form").html(html);
        }
    });    
  }
  $(document).ready(function() {
    refresh_reports() ;
    $('#report_list').change(function(event) {
      event.preventDefault() ;
      if($(this).val() != '')
      {
        $.ajax({
          type: "POST",
          url: '<?php echo url_for("report/getReport") ; ?>',
          data: 'name='+$(this).val(),
          success: function(html) {
          $(".report_form").html(html);
          }
        });
      $(".report_form").html('<img src="/images/loader.gif" />');
      }
      $(".report_form").html('');  
    });
  }) ;
</script>