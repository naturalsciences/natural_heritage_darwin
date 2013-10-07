<script type="text/javascript">
$(document).ready(function () {
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



});
</script>
<div class="container">
  <?php foreach($institutions as $institution):?>
    <h2><?php echo $institution->getFormatedName();?></h2>
    <div class="treelist">
    <?php
      $w = new sfWidgetCollectionList(array('choices'=>array(), 'is_choose' => $is_choose));
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
    <div class='new_link'><a <?php echo !(isset($is_choose) && $is_choose)?'':'target="_blank"';?> href="<?php echo url_for('collection/new') ?>"><?php echo __('New');?></a></div>
  <?php endif ; ?>
</div>
