<script type="text/javascript">
$(document).ready(function () {
    //ftheeten 2018 10 04
    var original_tree = $('.collection_tree_div').html();
    
     $(".do_reinit_collection").click(
        function()        
        {

             $('.collection_tree_div').html(original_tree);
             $('.treelist li:not(li:has(ul)) img.tree_cmd').hide();
             
            
        }
    );
    
    var filter_collection_logic=function()
		{
            //$('.container').html(original_tree);
			var searched_value=$(".filter_collection").first().val();			

            $(".treelist").each(function(iTree, tree)
                {
                    spans=$(tree).find("span");
                            spans.each(function(i, elem )
                            {
                                
                                var coll_name=$(elem).text();
                               
                                if (coll_name.toLowerCase().indexOf(searched_value.toLowerCase())!=-1) 
                                {		
                             
                                    $(elem).parents("li").show();
                                    $(elem).parents("li").css("visibility", "visible"); 
                                    $(elem).parents("li").parents("ul").show();
                                    $(elem).parents("li").addClass("collection_expanded");
                                    $(elem).parents("li").parents("ul").addClass("collection_expanded");
                                    
                                    
                                    
                                }
                                else
                                {
                                 
                                   
                                    if(! $(elem).parent("div").hasClass("collection_expanded"))
                                    {
                                       
                                        $(elem).parent("div").parent("li").css("display", "none");
                                    }
                                }
                            });
                });
			
		}
    
    //ftheeten 2018 10 03
	$(".do_filter_collection").click(
        function()
        {
                    
            filter_collection_logic();
        }
	);
    
        onElementInserted('body', '.collapsed', function(element)
        {
           $('.collapsed').click(function()
            {
                $(this).hide();
                $(this).siblings('.expanded').show();
                $(this).parent().siblings('ul').show();
            });
            
        });
        
   onElementInserted('body', '.expanded', function(element)
        {
          $('.expanded').click(function()
            {
                $(this).hide();
                $(this).siblings('.collapsed').show();
                $(this).parent().siblings('ul').hide();
            });
            
        });
    //end collections search engine

    $('.treelist li:not(li:has(ul)) img.tree_cmd').hide();
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
       $('table.widget_sub_table').find(':checked').removeAttr('checked').change();
    });

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

    $('#check_editable').click(function(){
      $('.treelist input:checked').removeAttr('checked').change();
      $('li[data-enc] > div > label > input:checkbox').attr('checked','checked').change();
    });
});
</script>
<br/>
	Search collection : <input type='text' id='filter_collection' name='filter_collection' class='filter_collection'></input>
	<input type='button' id='do_filter_collection' name='do_filter_collection' class='do_filter_collection' value='Filter'></input> 
    <input type='button' id='do_reinit_collection' name='do_reinit_collection' class='do_reinit_collection' value='Reinit'></input>
<br/>
<br/>
<table class="widget_sub_table">
  <tr>
    <td>
      <div class="treelist collection_tree_div">
		    <?php echo $form['collection_ref'] ; ?>
      </div>
      <div class="check_right">
      <?php if($sf_user->isAtLeast(Users::ENCODER)):?>
         <input type="button" class="result_choose" value="<?php echo __('check only editable');?>" id="check_editable">
      <?php endif;?>
        <input type="button" class="result_choose" value="<?php echo __('clear');?>" id="clear_collections">
      </div>

	  </td>
	</tr>
</table>
