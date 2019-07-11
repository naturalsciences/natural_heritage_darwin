<?php if(count($reports)) : ?>
  <table class="results">
    <thead>
      <tr>
        <th><?php echo __("Report you asked") ; ?></th>
        <th><?php echo __('Comment') ; ?></th>
        <?php if($sf_user->isA(USERS::ADMIN)) : ?><th><?php echo __('User') ; ?></th><?php endif ; ?>
        <th></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($reports as $report) : ?>
      <tr>
        <td><?php echo Reports::getReportName($report->getName(),$sf_user->getCulture()) ; ?>
        <?php echo image_tag('info.png', 'class=more_trk');?>
        <?php $widget = Reports::getRequiredFieldForReport($report->getName()) ; ?>
          <ul class="field_change">            
            <?php foreach($report->getDiffAsArray() as $field => $value):?>
              <li><strong><?php echo __($widget[$field]) ; ?></strong> <?php echo $value;?></li>
          <?php endforeach;?>       
          </ul>
        </td>
        <td>
          <?php echo($report->getComment()) ; ?>
        </td>
        <?php if($sf_user->isA(USERS::ADMIN)) : ?><td><?php echo ($report['formatedname']) ; ?></td><?php endif ; ?>
        <td>
          <?php if($report->getUri()) : ?>
              <?php if($report->getUri() == 'too_big')  : ?>
                <?php echo __("report too big") ; ?>
                <?php echo image_tag('info.png', 'class=more_trk');?>
                <ul class="field_change">
                  <li><?php echo __('report too big info');?></li>
                </ul>
              <?php else : ?>
                <a class="bt_close" href="<?php echo url_for( 'report/downloadFile?id='.$report->getId());?>"><?php echo __("Download") ?></a>
              <?php endif ; ?>
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
  $('img.more_trk').each(function()
  {
    $(this).qtip(
    {
     content: $(this).next().html(),
     delay: 100,
     show: { solo: true}
    });
  });
});
</script>
