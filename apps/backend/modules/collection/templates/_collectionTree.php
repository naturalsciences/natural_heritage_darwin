<script type="text/javascript">
$(document).ready(function () {
    var original_tree = $('.collection_tree_div').html();

    $('.treelist li:not(li:has(ul)) img.tree_cmd').hide();
    $('.collapsed').click(function()
    {
        $(this).hide();
        $(this).siblings('.expanded').show();
        $(this).parent().siblings('ul').show();
    });

    $('.expanded').click(function()
    {
        $(this).hide();
        $(this).siblings('.collapsed').show();
        $(this).parent().siblings('ul').hide();
    });

    $(".extd_info").each(function ()
    {
      $(this).qtip({
        show: { solo: true, event:'click' },
        hide: { event:false },
        style: 'ui-tooltip-light ui-tooltip-rounded ui-tooltip-dialogue',
        content: {
          text: '<img src="/images/loader.gif" alt="loading"> Loading ...',
          title: { button: true, text: ' ' },
          ajax: {
            url: '<?php echo url_for('collection/extdinfo');?>',
            type: 'GET',
            data: { id: $(this).attr('data-manid'), staffid: $(this).attr('data-staffid')}
          }
        }
      });
    });


    //ftheeten 2018 10 04 collection search engine
    $(".do_reinit_collection").click(
        function()        
        {

             $('.collection_tree_div').html(original_tree);
             $('.treelist li:not(li:has(ul)) img.tree_cmd').hide();
             
            
        }
    );
    
    var filter_collection_logic=function()
		{
            $('.collection_tree_div').html(original_tree);
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
     
    //end ftheeten 2018 10 04
    
    


});
</script>
<br/>
	Search collection : <input type='text' id='filter_collection' name='filter_collection' class='filter_collection'></input>
	<input type='button' id='do_filter_collection' name='do_filter_collection' class='do_filter_collection' value='Filter'></input> 
    <input type='button' id='do_reinit_collection' name='do_reinit_collection' class='do_reinit_collection' value='Reinit'></input>
<br/>
<br/>
<div class="container collection_tree_div">
  <?php foreach($institutions as $institution):?>
    <h2><?php echo $institution->getFormatedName();?></h2>
    
    <div class='treelist'>
    <?php
      $w = new sfWidgetCollectionList(array('choices'=>array(), 'is_choose' => $is_choose));
      //ftheeten 2017 03 31
      
      if($statistics===TRUE)
      {
        $w->attachStatistics();
       
      }
      $root = $tree = new Collections();
      foreach($institution->Collections as $item)
      {
        $it = sfOutputEscaper::unescape($item);
        $anc = $tree->getFirstCommonAncestor($it);
        $anc->addChild($it);
        $tree = $it;
      }
      echo $w->displayTree($root,'', array(), '', $sf_user);
  ?>

    </div>
  <?php endforeach;?>
  <?php if ($sf_user->isAtLeast(Users::MANAGER)): ?>
    <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href='<?php echo url_for('collection/new') ?>'><?php echo __('New');?></a></div>
  <?php endif ; ?>
</div>
