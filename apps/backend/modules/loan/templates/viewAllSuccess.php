<?php slot('title', __('Search Loans'));  ?>
<div id="myLoansViewAllTable" class="page">
  <h1><?php echo __('All the loans you\'re involved in.');?></h1>
  <?php if( count($loans) ) : ?> 
  <?php use_helper('Text');?>  
  <table class="show_table">
    <thead>
      <tr>
        <th class="left_aligned"><?php echo __('Name'); ?></th>
        <th class="left_aligned"><?php echo __('Status'); ?></th>
        <th class="left_aligned"><?php echo __('From'); ?></th>
        <th class="left_aligned"><?php echo __('To'); ?></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($loans as $loan) : ?>
      <tr>
        <td><?php echo $loan['name'];?></td>
        <td><?php echo $status[$loan->getId()]; ?></td>
        <td><?php $fromDate = new DateTime($loan['from_date']);
            echo $fromDate->format('d/m/Y');
            ?></td>
        <td><?php $toDate = new DateTime($loan['to_date']);
            echo $toDate->format('d/m/Y');
            ?></td>
        <td><?php echo link_to(image_tag('blue_eyel.png'),url_for(array('module'=> 'loan', 'action' => 'index', 'id' => $loan->getId())));?></td>
        <?php if( in_array($loan->getId(),sfOutputEscaper::unescape($rights)) ) :?>
        <td><?php echo link_to(image_tag('edit.png'),url_for(array('module'=> 'loan', 'action' => 'edit', 'id' => $loan->getId())));?></td>
        <td><?php echo link_to(image_tag('remove.png'),
                  url_for(array('module'=> 'loan', 'action' => 'delete', 'id' => $loan->getId())),
                  array('method' => 'delete', 'confirm' => __('Are you sure?'))
                  );
            ?>
        </td>
        <?php else :?>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <?php endif; ?>
    </tr>
      <?php endforeach ; ?>
    
    </tbody>
    <tfoot>
      <tr>
        <td colspan="7">&nbsp;</td>
      </tr>
      <tr>
        <td></td>
        <td colspan='3' style='text-align: center;'><?php echo link_to(__('Back to board'),url_for(array('module'=> 'board', 'action' => 'index')));?></td>
        <td colspan='3'><?php if ($sf_user->isAtLeast(Users::ENCODER))
                              {
                                echo image_tag('add_blue.png');
                                                  echo "&nbsp;". link_to(__('Add'),url_for(array('module'=> 'loan', 'action' => 'new')));
                              }
                              else
                              {
                                echo "&nbsp;";
                              }
                        ?></td>
      </tr>
    </tfoot>
  </table>
  <?php else :?>
    <?php echo __('Nothing here'); ?>
  <?php endif;?>
</div>
