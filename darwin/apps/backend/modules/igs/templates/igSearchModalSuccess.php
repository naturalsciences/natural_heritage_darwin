<?php use_stylesheets_for_form($searchform) ?>
<?php use_javascripts_for_form($searchform) ?>
<div class="ui-tooltip-titlebar">
<div id="ui-tooltip-modal-title" class="ui-tooltip-title" aria-atomic="true" >Search IG</div>
<a class="ui-state-default ui-tooltip-close ui-tooltip-icon" title="Close tooltip" aria-label="Close tooltip" role="button">
<span class="ui-icon ui-icon-close">×</span>
</a>
</div>
<span>
<?php include_partial('searchForm', array('form'=>$searchform, "is_choose"=> true, "increment"=> true)); ?>
</span>
<script>
	$(document).ready(
		function()
		{
			$(".ui-tooltip-close").click(
				function()
				{

						$('body').trigger('close_modal');
				}
			);
			
			

		}
	);
</script>