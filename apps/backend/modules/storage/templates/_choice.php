  <ul>
    <?php foreach($results as $r):?>
      <li>
        <?php if ($link):?>
          <?php echo link_to( $r['item'] == ''? "<i>".__('Empty')."</i>": $r['item'] ,$r->getRawValue()['link']);?>
        <?php else:?>
          <?php echo $r['item'] == ''? "<i>".__('Empty')."</i>": $r['item'];?>
        <?php endif;?>
        ( <?php echo $r['ctn'];?> )

        <?php $search = base64_encode(json_encode($r->getRawValue()['search'])); ?>
        <?php echo link_to(image_tag('magnifier.gif'),'specimensearch/search', array('class'=>'link_to_search' , 'data-search'=> $search,'title'=> __('Search')));?>
      </li>
    <?php endforeach;?>
  </ul>
