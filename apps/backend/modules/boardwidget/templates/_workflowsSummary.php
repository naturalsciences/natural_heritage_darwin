<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<?php echo form_tag('informativeWorkflow/search', array('class'=>'search_form','id'=>'workflow_filter'));?>
<table class="show_table">
  <thead>
    <tr>
      <th class="right_aligned"><?php echo $form['status']->render() ; ?></th>
    </tr>
  </thead>
</table>
<div class="search_results">
  <div class="search_results_content">
  </div>
</div>
</form>
<script type="text/javascript">
$(document).ready(function () 
{
  $('#workflowsSummary').choose_form({});
  $('#workflow_filter').submit();
  $('#searchWorkflows_status').change(function() { $('#workflow_filter').submit() });
});
</script>
