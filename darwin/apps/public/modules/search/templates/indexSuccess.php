<?php slot('title', __('Search Specimens/Rocks/Minerals'));  ?>  
<div class="page">
<h1><?php echo __("Specimen search criteria");?></h1>
<?php echo form_tag('search/search', array('class'=>'publicsearch_form'));?>
  <h2 class="title"><?php echo __("Taxonomy") ?></h2>
  <?php echo $form->renderGlobalErrors(); ?>
  <div class="borded">
    <?php echo $form->renderHiddenFields(); ?>
    <table id="classifications">
      <thead>
        <tr>
          <th><?php echo __("Scientific Name") ?></th>
          <th><?php echo __("Common Name") ?></th>
          <th><?php echo __("Level") ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['taxon_name'];?></td>
          <td><?php echo $form['taxon_common_name'];?></td>
          <td><?php echo $form['taxon_level_ref'];?></td>
        </tr>
      </tbody>
    </table>
    <br />
  </div>
  <table id="coll_and_countries">
    <tbody>
      <tr>
        <td>
          <div class="small_space_right">
            <h2 class="title"><?php echo __("Collections") ?></h2>
            <div class="borded framed">
            <table class="double_table collections">
              <tbody>
			    <tr>
				<th>
				<?php echo __("Institution Identifier") ?>
				</th>
				</tr>
				<tr>
				<td>
				<?php echo $form['institution_protocol'] ; ?>&nbsp;<?php echo $form['institution_identifier'] ; ?>
				</td>
				</tr>
                <tr>
                  <td>
                    <div class="treelist">
		                  <?php echo $form['collection_ref'] ; ?>        
                    </div>
                    <div class="check_right">
                      <input type="button" class="result" value="<?php echo __('Clear') ; ?>" id="clear_collections">
                    </div>
	                </td>
	              </tr>
	            </tbody>
            </table>
          </div>
        </td>
        
        <td>
          <h2 class="title"><?php echo __("Countries") ?></h2>
          <div class="borded framed">
          <table id="gtu_search" class="double_table tag">
            <thead>
              <tr><th colspan="2"><?php echo __('Tags') ; ?></th></tr>
            </thead>
            <tbody>              
              <tr class="tag_line">
                <td>
                  <?php echo $form['tags'];?>
                  <div class="tag_info"><span class="tag_info"><?php echo __('Please use ";" as tag separator.');?></span></div>
                  <div class="purposed_tags" id="purposed_tags">
                  </div>
                </td>
                <td>
                  <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop id=clear_tag'); ?>
                </td>
              </tr>
              <script  type="text/javascript">
                $('textarea.tag_line').bind('keydown click',purposeTags);
                $('#clear_tag').click(function(){
                    $('textarea.tag_line').val('');
                });
                function purposeTags(event)
                {
                  if (event.type == 'keydown')
                  {
                    var code = (event.keyCode ? event.keyCode : event.which);
                    if (code != 59 /* ;*/ && code != $.ui.keyCode.SPACE ) return;
                  }        
                  parent_el = $(this).closest('tr');

                  if($(this).val() == '') return;
                  $(this).find('#purposed_tags').html('<img src="/images/loader.gif" />');
                  $.ajax({
                    type: "GET",
                    url: "<?php echo url_for('search/purposeTag');?>" + '/value/'+ $(this).val(),
                    success: function(html)
                    {
                      parent_el.find('#purposed_tags').html(html);
                      parent_el.find('#purposed_tags').show();
                    }
                  });
                }

                $('#purposed_tags li').live('click',function()
                { 
                  input_el = $('textarea.tag_line');
                  if(input_el.val().match("\;\s*$"))
                    input_el.val( input_el.val() + $(this).text() );
                  else
                    input_el.val( input_el.val() + " ; " +$(this).text() );
                  input_el.trigger('click');
                });    
              </script>
            </tbody>
          </table>
          </div>
        </td>  
      </tr>
    </tbody>
	</div>
  </table>
  
  <table>
    <tbody>
      <tr>
        <td>
          <div class="space_right">
            <h2 class="title"><?php echo __("Types") ?></h2>
            <div class="borded framed" class='triple_table'>
              <?php echo $form['type'] ; ?>
            </div>
          </div>
        </td>

        <td>
          <div class="space_right">
          <h2 class="title"><?php echo __("Sexes") ?></h2>
            <div class="borded framed" class='triple_table'>
              <?php echo $form['sex'] ; ?>
            </div>
          </div>
        </td>

        <td>
          <h2 class="title"><?php echo __("Stages") ?></h2>
          <div class="borded framed" class='triple_table'>
            <?php echo $form['stage'] ; ?>
          </div>
        </td>          
      </tr>
    </tbody>
  </table>
  <h2 class="title"><?php echo __("Specimen criteria") ?></h2>
 <div class="borded">
    <?php echo $form->renderHiddenFields(); ?>
    <table id="classifications">
      <thead>
        <tr>
          <th><?php echo __("Codes") ?></th>        
          <th><?php echo __("I.G. unit") ?></th>          
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $form['codes'];?></td>
          <td><?php echo $form['ig_num'];?>
          <td><div class="tag_info"><span class="tag_info"><?php echo __('Please use ";" as tag separator.');?></td></span></div>
          </td>          
        </tr>
      </tbody>
	  
	   <thead>
	  <tr>
	  <th>
		<?php echo __("People identifier and role") ?>
	  </th>
	  </tr>
	  </thead>
	  <tbody>
	  <tr>
	  <td>
		<?php echo $form['people_protocol'] ; ?>&nbsp;<?php echo $form['people_identifier'] ; ?>
		&nbsp;
		<?php echo $form['people_identifier_role'] ; ?>
		</td>
	  </tr>
	  </tbody>
    </table>
    <br />
  </div> 
  <h2 class="title"><?php echo __("Other") ?></h2>  
  <div class="borded">
    <table id="other">
      <thead>
		<th>
		<?php echo __("Type of link") ?>
		</th>
		<th>
		<?php echo __("Link URL") ?>
		</th>
	  </thead>
	  <tbody>
	  <tr>
          <td><?php echo $form['link_type'];?></td>
		  <td><?php echo $form['link_url'];?></td>
	  </tr>
	  </tbody>
	  </table>
	  <table>
	  <br/>
	  <thead>
		<th>
		<?php echo __("Recorded query") ?>
		</th>
		<th>
		<?php echo __("Report type") ?>
		</th>
	  </thead>
	  <tbody>
	  <tr>
          <td><?php echo $form['public_query'];?></td>
		  <td>
            <select class="url_report">
            <option value=<?php echo(url_for("savesearch/downloadSpec")."/query_id/")?>>
            Tab-delimited (specimens)
            </option> 
            <option value=<?php echo(url_for("savesearch/downloadSpecLabels")."/query_id/")?>>
            Tab-delimited (specimens - labels )
            </option> 
			<option value=<?php echo(url_for("savesearch/downloadTaxonomy")."/type_file/taxonomy_count/query_id/")?>>
            Tab-delimited (taxonomy : statistics)
            </option>
			<option value=<?php echo(url_for("savesearch/downloadVirtualCollections")."/query_id/")?>>
            Tab-delimited (Virtual Collections)
            </option>   			
            </select>
         <input type="button" name="download_q" id="download_q" value="<?php echo __("Download") ?>"/></td>
	  </tr>
	  </tbody>
  </table>	
  </div>
  <div style="text-align:right">
    <?php echo link_to(__('Clear'),'@search');?>
    <input type="submit" name="submit" id="submit" value="<?php echo __('Search'); ?>" class="search_submit">
  </div>
</div>
</form>
<script type="text/javascript">
$(document).ready(function () {
    $('.treelist li:not(li:has(ul)) img.tree_cmd').hide();

    $('.col_check').not('label.custom-label input').customRadioCheck();
    $('.collapsed').click(function()
    {
        $(this).addClass('hidden');
        $(this).siblings('.expanded').removeClass('hidden');
        $(this).parent().siblings('ul').show();
    });

    $('.expanded').click(function()
    {
        $(this).addClass('hidden');
        $(this).siblings('.collapsed').removeClass('hidden');
        $(this).parent().siblings('ul').hide();
    });

    $('.chk input').change(function()
    {
      li = $(this).closest('li');
      if(! $(this).is(':checked'))
        li.find(':checkbox').not($(this)).removeAttr('checked').change();
      else
        li.find(':checkbox').not($(this)).attr('checked','checked').change();
    });

    $('#clear_collections').click(function()
    {
      $('table.collections').find(':checked').removeAttr('checked').change();
    });

  var num_fld = 1;
  $('.and_tag').click(function()
  {
    $.ajax({
      type: "GET",
      url: $(this).attr('href') + '/num/' + (num_fld++) ,
      success: function(html)
      {
        $('table#gtu_search > tbody .and_row').before(html);
      }
    });
    return false;
  });    
  $('#reset').click(function()
  {
    document.location.href = "<?php echo url_for('search/index') ; ?>" ;
  });
  
  $("#download_q").click(
	function()
	{
		if($(".public_query").val().length>0)
		{
			var tmp_url=$(".url_report").val()+$(".public_query").val();
			window.open(tmp_url, '_blank');
		}
	}
  );
  
  if( $('.public_query').has('option').length == 0 ) {
	$('.public_query').append($('<option>', {
    value: "",
    text: 'Empty...'
}));
  }
  
});  
</script>
