<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<!--[if lte IE 8]>
    <link rel="stylesheet" href="/leaflet/leaflet.ie.css" />
<![endif]-->

<script type="text/javascript">
$(document).ready(function () 
{
  $('body').catalogue({});
});
</script>

<?php echo form_tag('gtu/'.($form->getObject()->isNew() ? 'create' : 'update?id='.$form->getObject()->getId()), array('class'=>'edition'));?>

<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th class="top_aligned"><?php echo $form['code']->renderLabel() ?></th>
        <td>
          <?php echo $form['code']->renderError() ?>
          <?php echo $form['code'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['gtu_from_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['gtu_from_date']->renderError() ?>
          <?php echo $form['gtu_from_date'] ?>
        </td>
      </tr>
      <tr>
        <th class="top_aligned"><?php echo $form['gtu_to_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['gtu_to_date']->renderError() ?>
          <?php echo $form['gtu_to_date'] ?>
        </td>
      </tr>
    </tbody>
</table>

<?php
$tag_grouped = array();
$avail_groups = TagGroups::getGroups(); 
foreach($form['TagGroups'] as $group)
{
  $type = $group['group_name']->getValue();
  if(!isset($tag_grouped[$type]))
    $tag_grouped[$type] = array();
  $tag_grouped[$type][] = $group;
}
foreach($form['newVal'] as $group)
{
  $type = $group['group_name']->getValue();
  if(!isset($tag_grouped[$type]))
    $tag_grouped[$type] = array();
  $tag_grouped[$type][] = $group;
}
?>
<div id="gtu_group_screen">
<div class="tag_parts_screen" alt="<?php echo url_for('gtu/addGroup'. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>">
<?php foreach($tag_grouped as  $group_key => $sub_forms):?>
  <fieldset alt="<?php echo $group_key;?>">
    <legend><?php echo __($avail_groups[$group_key]);?></legend>
    <ul>
      <?php foreach($sub_forms as $form_value):?>
	<?php include_partial('taggroups', array('form' => $form_value));?>
      <?php endforeach;?>
    </ul>
    <a class="sub_group"><?php echo __('Add Sub Group');?></a>
  </fieldset>
<?php endforeach;?>
</div>


  <div class="gtu_groups_add">
    <select id="groups_select">
      <option value=""></option>
      <?php foreach(TagGroups::getGroups() as $k => $v):?>
        <option value="<?php echo $k;?>"><?php echo $v;?></option>
      <?php endforeach;?>
    </select>
    <a href="<?php echo url_for('gtu/addGroup'. ($form->getObject()->isNew() ? '': '?id='.$form->getObject()->getId()) );?>" id="add_group"><?php echo __('Add Group');?></a>
  </div>

</div>
    
  <fieldset id="location">
    <legend><?php echo __('Localisation');?></legend>
    <div id="reverse_tags" style="display: none;"><ul></ul><br class="clear" /></div>
    <table>
      <tr>
        <th><?php echo $form['latitude']->renderLabel() ;?><?php echo $form['latitude']->renderError() ?></th>
        <th><?php echo $form['longitude']->renderLabel(); ?><?php echo $form['longitude']->renderError() ?></th>
        <th><?php echo $form['lat_long_accuracy']->renderLabel() ;?><?php echo $form['lat_long_accuracy']->renderError() ?></th>
        <th></th>
      </tr>
      <tr>
        <td><?php echo $form['latitude'];?></td>
        <td><?php echo $form['longitude'];?></td>
        <td><?php echo $form['lat_long_accuracy'];?></td>
        <td><strong><?php echo __('m');?></strong> <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
      </tr>

      <tr>
        <th></th>
        <th><?php echo $form['elevation']->renderLabel(); ?><?php echo $form['elevation']->renderError() ?></th>
        <th><?php echo $form['elevation_accuracy']->renderLabel() ;?><?php echo $form['elevation_accuracy']->renderError() ?></th>
        <th></th>
      </tr>
      <tr>
        <td></td>
        <td><?php echo $form['elevation'];?></td>
        <td><?php echo $form['elevation_accuracy'];?></td>
        <td><strong><?php echo __('m');?></strong> <?php echo image_tag('remove.png', 'alt=Delete class=clear_prop'); ?></td>
      </tr>
      <tr>
        <td colspan="3"><div style="width:100%; height:400px;" id="map"></div></td>
        <td>

<script type="text/javascript">
$(document).ready(function () {

  initEditMap("map");

  <?php if($form->getObject()->getLongitude() != ''):?>
    map.setView([<?php echo $form->getObject()->getLatitude();?>,<?php echo $form->getObject()->getLongitude();?>], 12);
  <?php else:?>
    map.setView([0,0], 2);
  <?php endif;?>
});
</script>
</td>
      </tr>
    </table>

  </fieldset>

  <table>
    <tfoot>
      <tr>
        <td>
          <?php echo $form->renderHiddenFields(true) ?>

          <?php if (!$form->getObject()->isNew()): ?>
            <?php echo link_to(__('New Gtu'), 'gtu/new') ?>
            &nbsp;<?php echo link_to(__('Duplicate Gtu'), 'gtu/new?duplicate_id='.$form->getObject()->getId()) ?>
          <?php endif?>

          &nbsp;<a href="<?php echo url_for('gtu/index') ?>"><?php echo __('Cancel');?></a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to(__('Delete'), 'gtu/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input id="submit" type="submit" value="<?php echo __('Save');?>" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>


<script  type="text/javascript">
$(document).ready(function () {
    $('.tag_parts_screen .clear_prop').live('click', function()
    {
      parent_el = $(this).closest('li');
      $(parent_el).find('input').val('');
      $(parent_el).hide();

      sub_groups  = parent_el.parent();
      if(sub_groups.find("li:visible").length == 0)
      {
	      sub_groups.closest('fieldset').hide();
      	disableUsedGroups();
      }
    });

   

    disableUsedGroups();
    $('.purposed_tags li').live('click', function()
    {
      input_el = $(this).parent().closest('li').find('input[id$="_tag_value"]');
      if(input_el.val().match("\;\s*$"))
        input_el.val( input_el.val() + $(this).text() );
      else
        input_el.val( input_el.val() + " ; " +$(this).text() );
      input_el.trigger('click');
    });

    $('input[id$="_tag_value"]').live('keydown click',purposeTags);

   function purposeTags(event)
   {
      if (event.type == 'keydown')
      {
        var code = (event.keyCode ? event.keyCode : event.which);
        if (code != 59 /* ;*/ && code != $.ui.keyCode.SPACE ) return;
      }
      parent_el = $(this).closest('li');
      group_name = parent_el.find('input[name$="\[group_name\]"]').val();
      sub_group_name = parent_el.find('[name$="\[sub_group_name\]"]').val();
      if(sub_group_name == '' || $(this).val() == '') return;
      $('.purposed_tags').hide();
      $.ajax({
        type: "GET",
        url: "<?php echo url_for('gtu/purposeTag');?>" + '/group_name/' + group_name + '/sub_group_name/' + sub_group_name + '/value/'+ $(this).val(),
        success: function(html)
        {
          parent_el.find('.purposed_tags').html(html);
          parent_el.find('.purposed_tags').show();
        }
      });
    }

    $('#add_group').click(function(event)
    {
      event.preventDefault();
      selected_group = $('#groups_select option:selected').val();
      addGroup(selected_group);
    });

    $('a.sub_group').live('click',function(event)
    {
      event.preventDefault();
      addSubGroup( $(this).closest('fieldset').attr('alt'));
    });

});

function addSubGroup(selected_group, default_type, value)
{
    hideForRefresh('#gtu_group_screen');
    fieldset = $('fieldset[alt="'+selected_group+'"]');
    if( fieldset.length ==0 )
    {
      addGroup(selected_group, default_type, value);
    }
    list =  fieldset.find('>ul');
    $.ajax({
      type: "GET",
      url: $('.tag_parts_screen').attr('alt')+'/group/'+ selected_group + '/num/' + (0+$('.tag_parts_screen ul li').length),
      success: function(html)
      {
        html = $(html);
        html.find('.complete_widget select').val(default_type);

        if(value != undefined && value !='')
        {
          html.find('.tag_encod input').val(value);
        }
        list.append(html);

        showAfterRefresh('#gtu_group_screen');
      }
    });
}

function addTagToGroup(group, sub_group, tag)
{
  if($('fieldset[alt="'+group+'"] .complete_widget input, fieldset[alt="'+group+'"] .complete_widget option:selected').filter(function()
    { return $(this).is(':visible') && $(this).val() == sub_group; }).length == 0)
  {
    addSubGroup(group, sub_group, tag);
  }
  else
  {
    el = $('fieldset[alt="'+group+'"] .complete_widget input, fieldset[alt="'+group+'"] .complete_widget option:selected').filter('[value="'+sub_group+'"]');
    el_input = el.closest('li').find('.tag_encod input');
    el_input.val( el_input.val()  +' ; ' + tag);
  }
}

function disableUsedGroups()
{
  $('#groups_select option').removeAttr('disabled');
  $('.tag_parts_screen fieldset:visible').each(function()
  {
    var cur_group = $(this).attr('alt');
    $("#groups_select option[value='"+cur_group+"']").attr('disabled','disabled');
    if($("#groups_select option[value='"+cur_group+"']:selected"))
      $('#groups_select').val("");
  });
}

function addGroup(g_val, sub_group, value)
{
  if(g_val != '')
  {
    hideForRefresh('#gtu_group_screen');
    g_name = $('[value="'+g_val+'"]').text();
    $.ajax({
      type: "GET",
      url: $('.tag_parts_screen').attr('alt')+'/group/'+ g_val + '/num/' + (0+$('.tag_parts_screen ul li').length),
      success: function(html)
      {
        html = $(html);
        if( $('fieldset[alt="'+g_val+'"]').length == 0)
        {
          fld = '<fieldset alt="'+ g_val +'"><legend>' + g_name + '</legend><ul></ul><a class="sub_group"><?php echo __('Add Sub Group');?></a></fieldset>';
          $('.tag_parts_screen').append(fld);    
        }
        html.find('select').val(sub_group);
        fld_set = $('fieldset[alt="'+g_val+'"]');

        if(value != undefined && value !='')
        {
           html.find(' .tag_encod input').val(value);
        }

        fld_set.find('> ul').append(html);
        fld_set.show();

        disableUsedGroups();
        showAfterRefresh('#gtu_group_screen');
      }
    });
  }
}
</script>
