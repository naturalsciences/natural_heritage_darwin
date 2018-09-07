<?php if($searchForm->isValid()):?>
<?php if(isset($items) && $items->count() != 0):?>
  <?php
    if($orderDir=='asc')
      $orderSign = '<span class="order_sign_down">&nbsp;&#9660;</span>';
    else
      $orderSign = '<span class="order_sign_up">&nbsp;&#9650;</span>';
  ?>
  <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
  <?php include_partial('global/pager_info', array('form' => $searchForm, 'pagerLayout' => $pagerLayout)); ?>
  <div class="results_container">
    <table class="results <?php if($is_choose) echo 'is_choose';?>">
      <thead>
		<tr>
        <th></th>
        <?php if(isset($items[0]['color'])): ?>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=color'.( ($orderBy=='color' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
            <?php echo __('Colour');?>
            <?php if($orderBy=='color') echo $orderSign ?>
          </th>
        <?php endif ; ?>
        <?php if(isset($items[0]['code'])): ?>
          <th>
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=code'.( ($orderBy=='code' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Code');?>
              <?php if($orderBy=='code') echo $orderSign ?>
            </a>
          </th>
        <?php endif;?>
        <th>
          <a class="sort" href="<?php echo url_for($s_url.'&orderby=name_indexed'.( ($orderBy=='name_indexed' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
            <?php echo __('Name');?>
            <?php if($orderBy=='name_indexed') echo $orderSign ?>
          </a>
        </th>
        <th>
          <a class="sort" href="<?php echo url_for($s_url.'&orderby=level_ref'.( ($orderBy=='level_ref' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
            <?php echo __('Level');?>
            <?php if($orderBy=='level_ref') echo $orderSign ?>
          </a>
        </th>
        <?php if(isset($items[0]['lower_bound']) && isset($items[0]['upper_bound'])): ?>
          <th class="datesNum">
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=lower_bound'.( ($orderBy=='lower_bound' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Lower bound (My)');?>
              <?php if($orderBy=='lower_bound') echo $orderSign ?>
            </a>
          </th>
          <th class="datesNum">
            <a class="sort" href="<?php echo url_for($s_url.'&orderby=upper_bound'.( ($orderBy=='upper_bound' && $orderDir=='asc') ? '&orderdir=desc' : '').'&page='.$currentPage);?>">
              <?php echo __('Upper bound (My)');?>
              <?php if($orderBy=='upper_bound') echo $orderSign ?>
            </a>
          </th>
        <?php endif;?>
        <th></th>
	   </tr>
      </thead>
      <tbody>
        <?php foreach($items as $item):?>
          <tr class="rid_<?php echo $item->getId();?>">
            <?php
              $addedFormat = '';
              switch ($item->getStatus())
              {
                case 'invalid':
                  $addedFormat = ' ('.__('Invalid').')';
                  break;
                case 'deprecated':
                  $addedFormat = ' ('.__('Deprecated').')';
                  break;
              }
            ?>
            <td><?php echo image_tag('info.png',"title=info class=info");?></td>
            <?php if(isset($item['color'])): ?>
              <td><span class='round_color' style="<?php if($item->getColor()!= ""):?>background-color:<?php echo $item->getColor();?><?php endif;?>">&nbsp;</span></td>
            <?php endif ; ?>
            <?php if(isset($item['code'])): ?>
              <td>
                <span><?php echo $item->getCode();?></span>
              </td>
            <?php endif;?>
            <td>
              <span class="item_name"><?php echo $item->getNameWithFormat(ESC_RAW);?><span class="invalid"><?php echo $addedFormat;?></span></span>
              <div class="tree">
              </div>
            </td>
            <td>
              <span class="level_name"><?php echo $item->getLevel();?></span>
            </td>
            <?php if(isset($item['lower_bound']) && isset($item['upper_bound'])): ?>
              <td class="datesNum">
                <span><?php echo $item->getLowerBound();?></span>
              </td>
              <td class="datesNum">
                <span><?php echo $item->getUpperBound();?></span>
              </td>
            <?php endif;?>
            <td class="<?php echo (! $is_choose)?'edit':'choose';?>">
              <a href="#" class="search_related"><?php echo image_tag('link.png', array("title" => __("Search Related")));?></a>
                <?php echo link_to(image_tag('blue_eyel.png', array("title" => __("View"))),$searchForm->getValue('table').'/view?id='.$item->getId());?>
              <?php if(! $is_choose):?>
                <?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
                  <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))),$searchForm->getValue('table').'/edit?id='.$item->getId());?>
                  <?php echo link_to(image_tag('duplicate.png', array("title" => __("Duplicate"))),$searchForm->getValue('table').'/new?duplicate_id='.$item->getId());?>
                <?php endif ; ?>
              <?php else:?>
                <?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
                  <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))),$searchForm->getValue('table').'/edit?id='.$item->getId(),array('target'=>"_blank"));?>
                  <?php echo link_to(image_tag('duplicate.png', array("title" => __("Duplicate"))),$searchForm->getValue('table').'/new?duplicate_id='.$item->getId(),array('target'=>"_blank"));?>
                <?php endif ; ?>
                <div class="result_choose"><?php echo __('Choose');?></div>
              <?php endif;?>
            </td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>
  <?php include_partial('global/pager', array('pagerLayout' => $pagerLayout)); ?>
    <script type="text/javascript">
    $(document).ready(function () {
      $('a.search_related').click(function(event)
      {
        $(this).closest('form')[0].reset();
        event.preventDefault();
        row = $(this).closest('tr');
        iname = row.find('.item_name');
        $('.search_item_name').html(iname.html());
        rid = getIdInClasses(row);
        $('#searchCatalogue_item_ref').val(rid);
        $('.search_item_name').closest('tr').show();
      });

      $('.search_results_content tbody tr .info').click(function()
      {
        item_row=$(this).closest('tr');
        if(item_row.find('.tree').is(":hidden"))
        {
          $.get('<?php echo url_for('catalogue/tree?table='.$searchForm->getValue('table'));?>/id/'+getIdInClasses(item_row),function (html){
            item_row.find('.tree').html(html).slideDown();
          });
        }
        item_row.find('.tree').slideUp();
      });

    });
    </script>
<?php else:?>
  <?php echo __('No Matching Items');?>
<?php endif;?>
<?php else:?>
  <div class="error">
    <?php echo $searchForm['name']->renderError() ?>
    <?php echo $searchForm['level_ref']->renderError() ?>
    <?php if(isset($searchForm['code'])):?>
      <?php echo $searchForm['code']->renderError() ?>
    <?php endif;?>
    <?php if (isset($searchForm['upper_bound'])):?>
      <?php if($searchForm['lower_bound']->hasError() || $searchForm['upper_bound']->hasError()):?>
        <?php echo $searchForm['lower_bound']->renderError() ?>
        <?php echo $searchForm['upper_bound']->renderError() ?>
      <?php elseif($searchForm->hasGlobalErrors()):?>
        <?php echo $searchForm->renderGlobalErrors() ?>
      <?php endif;?>
    <?php endif;?>
  </div>
<?php endif;?>
