<?php slot('widget_title',__('DaRWIN 2 Statistics'));  ?>
<?php if(!empty($stats)) : ?>
<?php foreach($stats as $id=>$stat) : ?>
  <?php if($id !="date_gen_stat") : ?>
    <?php if($sf_user->isAtleast($stat['level'])) : ?>
      <table class="full_size" id="line_<?php echo $id ; ?>">
        <thead>
        <tr>
          <th>
            <?php if($stat['expandable']) : ?>    
              <?php echo image_tag('blue_expand.png', array('alt' => '+', 'class'=> 'tree_cmd_td collapsed')); ?>
              <?php echo image_tag('blue_expand_up.png', array('alt' => '-', 'class'=> 'tree_cmd_td expanded')); ?>      
            <?php else: ?>
              <?php echo image_tag('grey_expand.png', array('alt' => '+', 'class'=> 'tree_cmd_td')); ?>            
            <?php endif ; ?>
            <?php echo __($stat['title']) ; ?>
            <?php if($stat['description']!= '') : ?>         
              <?php echo help_ico($stat['description'],$sf_user);?>
            <?php endif ; ?>
          </th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <td>
            <div class="expanded_stats <?php echo($stat['expandable']?'tree':'') ; ?>">
            <?php if(count($stat['fields']) == 1) : ?>
              <?php include_partial('board/stats_in_list', array('result' => $stat['result'], 'field' => $stat['fields'][0],'expandable' => $stat['expandable'])); ?>  
            <?php else : ?>
              <?php include_partial('board/stats_in_table', array('result' => $stat['result'], 'fields' => $stat['fields'],'expandable' => $stat['expandable'])); ?>    
            <?php endif ; ?>
            <?php if($stat['expandable']) : ?>
            <script type="text/javascript">
            $(document).ready(function () {
              $('#line_<?php echo $id ; ?> img.collapsed').click(function() 
              {
                $(this).hide();
                $(this).siblings('.expanded').show();
                $(this).closest('table').find('tbody div.tree').slideDown();
              });  
              $('#line_<?php echo $id ; ?> img.expanded').click(function() 
              {
                $(this).hide();
                $(this).siblings('.collapsed').show();
                $(this).closest('table').find('tbody div.tree').slideUp();
              });
            });
            </script>
            <?php endif ; ?> 
            </div>   
          </td>
        </tr> 
        </tbody>
      </table>    
      <hr />
    <?php endif ; ?>
  <?php else : ?>
    <div class="stat_date"><?php echo __("Statistics generated at %stat_date%",array("%stat_date%" => $stat)) ; ?></div>
  <?php endif ; ?>
<?php endforeach ; ?>
<?php else : ?>
<?php echo __("The statistics are currently not available") ; ?>
<?php endif ; ?>


