<?php slot('title',__('Correction of import row'));?>
<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<div class="page">

  <?php echo form_tag('staging/update?id='.$form->getObject()->getId(), array('class'=>'edition','method'=>'post'));?>  
  <h1><?php echo __('Level')." : ".$form->getObject()->getLevel() ; ?></h1>
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
        <?php if(in_array($array['fields'],array('collectors','donators','identifiers'))) : ?>
        <table class="encoding collections_rights" id="<?php echo $array['fields'] ; ?>_table">
          <thead>
            <tr>
              <th><label><?php echo __($array['fields']) ; ?></label></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($form['Wrong'.ucfirst($array['fields'])] as $form_value) : ?>
           <?php $retainedKey = 0;?>          
           <?php include_partial('member_row', array('form' => $form_value, 'ref_id' => $form->getObject()->getId(), 'row_num'=>$retainedKey, 
                                                     'id_field'=>$array['embedded_field'])); 
           $retainedKey ++;?>
          <?php endforeach ; ?>
          </tbody>
        </table>
        <?php else : ?>
          <?php echo $form[$array['fields']]->renderError() ; ?>
          <?php echo $form[$array['fields']]->render() ; ?>
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
