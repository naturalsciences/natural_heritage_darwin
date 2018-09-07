<?php if($rights->count() > 0) : ?>
<table class="summary">
  <thead>
    <tr>
      <th><?php echo __('Collection') ; ?></th>
      <th><?php echo __('Right') ; ?></th>
    </tr>
  </thead>
  <tbody>  
  <?php foreach($rights as $right) : ?>
    <tr>
      <td><?php echo $right->Collections->getName() ; ?></td>
      <td>
          <?php if(isset($widgets[$right->getCollectionRef()])):?>
          <ul>          
            <?php echo image_tag('blue_expand.png', array('alt' => '+', 'class'=> 'tree_cmd_td collapsed', 'id' => 'collection_'.$right->getCollectionRef()."_collapsed")); ?>
            <?php echo image_tag('blue_expand_up.png', array('alt' => '-', 'class'=> 'tree_cmd_td expanded', 'id' => 'collection_'.$right->getCollectionRef()."_expanded")); ?>
            <?php echo $summary->getRaw($right->getDbUserType()) ;?>            
            <fieldset id="collection_<?php echo $right->getcollectionRef() ; ?>_tree" class="search_form tree">              
              <?php include_partial('result_widget', array('widget' => $widgets[$right->getCollectionRef()])) ; ?>
            </fieldset>
            <script type="text/javascript">       
                $('#collection_<?php echo $right->getcollectionRef() ; ?>_collapsed').click(function() 
                {
                  $(this).hide();
                  $(this).siblings('.expanded').show();                
                  $('#collection_<?php echo $right->getcollectionRef() ; ?>_tree').slideDown();
                });
                $('#collection_<?php echo $right->getcollectionRef() ; ?>_expanded').click(function() 
                {
                  $(this).hide();
                  $(this).siblings('.collapsed').show();                
                  $('#collection_<?php echo $right->getcollectionRef() ; ?>_tree').slideUp();
                });                
            </script>            
          </ul>
          <?php else : ?>
            <?php echo $summary->getRaw($right->getDbUserType()) ;?>
          <?php endif;?>            
      </td>
    </tr>
  <?php endforeach ; ?>
  </tbody>
</table>
<?php else : ?>
  <?php echo __("You can only view public collections"); ?>
<?php endif ; ?>
