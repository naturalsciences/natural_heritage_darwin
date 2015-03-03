<?php if(count($reports)) : ?>
  <table class="results">
    <thead>
      <tr>
        <th><?php echo __("Report you asked") ; ?></th>
        <th></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($reports as $report) : ?>
      <tr>
        <td><?php echo Reports::getReportName($report->getName(),$sf_user->getCulture()) ; ?></td>       
        <td>
          <?php if($report->getUri()) : ?>
             <a class="bt_close" href="<?php echo url_for( 'report/downloadFile?id='.$report->getId());?>"><?php echo __("Download") ?></a>
          <?php else : ?>
            <?php echo __("report not available yet") ; ?>
          <?php endif; ?>
        </td>
        <td><?php echo link_to(image_tag('remove.png', array("title" => __("Delete"))), 'report/delete?id='.$report->getId(),'class=remove_report') ?></td>
      </tr>
    <?php endforeach ; ?>
  </tbody>
  </table>
<?php endif ; ?>

<script language="javascript">
$(document).ready(function () {
  $('.remove_report').click(function(event)
  {
    event.preventDefault();
    if(confirm('<?php echo addslashes(__('Are you sure ?'));?>'))
    {
      $.ajax({
        url: $(this).attr('href'),
        success: function(html)
        {
          if(html == "ok" )
          {
            refresh_reports() ;
          }
        }
      });
   }
  });
});
</script>