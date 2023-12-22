<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<script type="text/javascript">
$(document).ready(function () 
{
  $('body').catalogue({});
});
</script>

<?php echo form_tag('igs/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition'));?>

<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" ig_num="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo __('I.G. number:');?></th>
        <td>
          <?php echo $form['ig_num']->renderError() ?>
          <?php echo $form['ig_num'] ?>
        </td>
		<th><?php echo __('Increment from prefix :');?></th>
		<td>
			<input type="text" id="ig_prefix" name="ig_prefix"></input>
			
			<div class="add_code" style="margin-left: 10px;">
				<?php echo link_to(__('Get last I.G.'),'igs/igSearchModal', array("class"=>"link_catalogue_view", "id"=>"search_ig"));?>
			</div>
		</td>
      </tr>
      <tr>
        <th><?php echo __('I.G. type:');?></th>
        <td>
          <?php echo $form['ig_type']->renderError() ?>
          <?php echo $form['ig_type'] ?>
        </td>
      </tr>      
      <tr>
        <th><?php echo __('I.G. creation date:'); ?></th>
        <td>
          <?php echo $form['ig_date']->renderError() ?>
          <?php echo $form['ig_date'] ?>
        </td>
      </tr>
	  <tr>
        <th><?php echo __('Complete:'); ?></th>
        <td>
          <?php echo $form['complete']->renderError() ?>
          <?php echo $form['complete'] ?>
        </td>
      </tr>
	  <tr>
        <th><?php echo __('Nagoya status:'); ?></th>
        <td>
          <?php echo $form['nagoya_status']->renderError() ?>
          <?php echo $form['nagoya_status'] ?>
        </td>
      </tr>
	  
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>

          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New I.G.'), 'igs/new') ?>
            &nbsp;<?php echo link_to(__('Duplicate I.G.'), 'igs/new?duplicate_id='.$form->getObject()->getId()) ?>
          <?php endif?>

          &nbsp;<a href="<?php echo url_for('igs/index') ?>"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('Delete'), 'igs/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => __('Are you sure?'))) ?>
          <?php endif; ?>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>
<script>
var init_result=false;

var replaceLast = function (subject, what, replacement) {
        var pcs = subject.split(what);
        var lastPc = pcs.pop();
        return pcs.join(what) + replacement + lastPc;
    };
	
	
$(document).ready(
		function()
		{
			$("#search_ig").click(
				function()
				{
					//console.log("test");
					$(".ig_num_search").val($(".ig_num").val(""));
				}
			);
			
		onElementInserted('body', '.ig_num_search', 
			function(element)
			{
				$(".ig_num_search").val($("#ig_prefix").val()+".");
				$(".ig_num_exact").prop('checked', true);
				$(".main_search_ig").click();
				
				
				
			}
		);
			
		onElementInserted('body', '.ig_num_search', 
			function(element)
			{
				$(".ig_num_search").val($("#ig_prefix").val()+".");
				$(".ig_num_exact").prop('checked', true);
				init_result=true;
				$(".main_search_ig").click();				
			}
			);
			
		onElementInserted('body', '.sort_by_ig_num', 
			function(element)
			{
				if(init_result)
				{
					$(".sort_by_ig_num").click();	
					init_result=false;
				}
			}
			);
	
		
		onElementInserted('body', '.ig_increment', 
			function(element)
			{
				$(element).click(
				function()
					{
						//console.log("increment");
						var tmp=$(element).attr("ig_val");
						//console.log(tmp);
						var reg=/\d+/g;
						var groups=tmp.match(reg);
						console.log(groups);
						if(groups.length>0)
						{
							var last_num=parseInt(groups[groups.length-1]);
							//console.log(last_num);
							var len_last_num=groups[groups.length-1].length;
							last_num=last_num+1;
							
							var last_num_str=last_num.toString();
							if(last_num_str.length<len_last_num)
							{
								
								last_num_str=last_num_str.padStart(len_last_num,"0");
							
							}
							tmp=replaceLast(tmp, groups[groups.length-1], last_num_str);
							$(".ig_num").val(tmp);
							$(".ui-tooltip-close").click();
						}
					}
				);
			}
		);
			<?php if ($form->getObject()->isNew()): ?>
				var tmp_year=new Date().getFullYear();
				$("#ig_prefix").val(tmp_year);
			<?php endif; ?>
		});
</script>
