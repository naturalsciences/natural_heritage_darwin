<?php slot('title',__('Correction of import row'));?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="page">

  <?php echo form_tag('staging/update?id='.$form->getObject()->getId(), array('class'=>'edition','method'=>'post'));?>
  <?php if($form->hasGlobalErrors()):?>
    <ul class="spec_error_list">
      <?php foreach ($form->getErrorSchema()->getErrors() as $name => $error): ?>
        <li class="error_fld_<?php echo $name;?>"><?php echo __($error) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif;?>
  <?php if(!$fields) : ?>
    <?php echo __('No errors on this record') ; ?>
    <p class="form_buttons right_aligned error">
      <a href="<?php echo url_for('staging/index?import='.$form->getObject()->getImportRef()) ?>" id="spec_cancel"><?php echo __('Back');?></a>
    </p>
  <?php else : ?>
    <?php foreach($fields as $key => $array) : ?>
      <?php if($key == 'duplicate') : ?>
      <fieldset>
        <ul class="error_list">
          <li><?php echo __($array['display_error'],array('%here%' => link_to('here', $form->getObject()->getLevel().'/view?id='.$array['duplicate_record'],'target=blanck'))) ?></li>
      <?php else : ?>
      <fieldset><legend><?php echo __('Field to be corrected')." : ".$key ;?></legend>
        <ul class="error_list">
            <li><?php echo __($array['display_error'],array('%field%' => $key)) ; ?></li>
      <?php endif ; ?>
        </ul>
        <?php if(in_array($array['fields'],array('people','identifiers','operator','relation_institution_ref'))) : ?>
        <table class="encoding collections_rights" id="<?php echo $array['fields'] ; ?>_table">
          <thead>
            <tr>
              <th><label><?php echo __($array['fields']) ; ?></label></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($form['Wrong'.ucfirst($array['fields'])] as $form_value) : ?>
           <?php $retainedKey = 0;?>
           <?php include_partial('member_row', array(
              'form' => $form_value,
              'ref_id' => $form->getObject()->getId(),
              'row_num'=>$retainedKey,
              'id_field'=>$array['embedded_field'])
           );
           $retainedKey ++;?>
          <?php endforeach ; ?>
          </tbody>
        </table>
        <?php else : ?>
          <?php echo $form[$array['fields']]->renderError() ; ?>
          <?php echo $form[$array['fields']]->render() ; ?>


          <?php if ( in_array($key, array('taxon','litho','lithology','mineral','chrono')) ) : ?>
            <?php $catalogues = 'catalogues_'.$key;
            $catalogues = $$catalogues;
          ?>
            <?php if (count($catalogues) > 0) : ?><ul class="taxon_parent"><?php echo __("import_taxon_parent_info") ; ?><?php endif; ?>
            <?php foreach($catalogues as $level => $catalogue) : ?>
              <?php $lvl_name = $key.'_level_name';
                      $lvl_name = $$lvl_name;
                      if($key == 'taxon') $link_url = 'taxonomy';
                      if($key == 'mineral') $link_url = 'mineralogy';
                      if($key == 'litho') $link_url = 'lithostratigraphy';
                      if($key == 'lithology') $link_url = 'lithology';
                      if($key == 'chrono') $link_url = 'chronostratigraphy';
              ?>
              <li class="rid_<?php echo $catalogue['class'] != ''?$catalogue['class']:'' ; ?>" data-catalogue="<?php echo $link_url ; ?>">                
                <div class="<?php echo $catalogue['class'] != ''? 'line_ok':'line_not_ok'; ?>"></div>
                <?php if ($catalogue['class'] != '') echo image_tag('info.png',"title=info class=info") ;?>
                <?php if($catalogue['level_sys_name'] == $lvl_name) echo '<strong>';?>
                <?php if($catalogue['class'] == ''):?>
                  <a target="_blank" href="<?php echo url_for($link_url.'/new').'?'.$link_url.'[name]='.urlencode($catalogue['name']).'&'.$link_url.'[level_ref]='.$catalogue['level_ref'] ; ?>">
                    <?php echo $catalogue['name']." (".$level.")";?>
                  </a>
                <?php else:?>
                  <?php echo link_to($catalogue['name']." (".$level.")", $link_url.'/view?id='.$catalogue['class']) ; ?>
                <?php endif;?>                
                <?php if($catalogue['level_sys_name'] == $lvl_name) echo '</strong>';?>
                <div class="tree"></div>
              </li>
            <?php endforeach ; ?>
            </ul>
          <?php endif ; ?>
        <?php endif ; ?>
      </fieldset>
    <?php endforeach ; ?>
    <?php echo $form->renderHiddenFields() ; ?>
    <div class="warn_message">
      <?php echo __('<strong>Warning!</strong><br />If you don\'t correct default values before saving, the associated error will remain.');?>
    </div>
    <p class="form_buttons right_aligned error">
      <a href="<?php echo url_for('staging/index?import='.$form->getObject()->getImportRef()) ?>" id="spec_cancel"><?php echo __('Back');?></a>
      <input type="submit" value="<?php echo __('Update');?>" id="submit"/>
    </p>
  <?php endif ; ?>
</div>

<script>
$(document).ready(function () {
  $(".ref_clear").click( function()
  {
    $(this).parent().find('input').val(0);
  });

  $('.taxon_parent li .info').click(function()
  {    
    item_row=$(this).closest('li');
    catalogue='<?php echo url_for('catalogue/tree');?>'+'/table/'+item_row.attr('data-catalogue') ;
    if(item_row.find('.tree').is(":hidden"))
    {
      $.get(catalogue+'/id/'+getIdInClasses(item_row),function (html){
        item_row.find('.tree').html(html).slideDown();
      });
    }
    $('.tree').slideUp();
  });
});
</script>
