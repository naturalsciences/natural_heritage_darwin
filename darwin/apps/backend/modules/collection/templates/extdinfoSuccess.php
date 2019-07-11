<?php use_helper('Text');?>

<div class="col_extd_info">
<h2><?php echo __('Conservator');?> :</h2>
<p>
<dl>
  <dt><?php echo __('Name')?><dt>
  <dd><?php echo $manager->getFormatedName();?></dd>
</dl>
</p>

<?php if(count($coms) != 0):?>
<h2><?php echo __('Contacts');?> :</h2>
<p>
  <dl>
    <?php foreach($coms as $com):?>
    <dt><?php echo $com->getCommType()?><dt>
    <dd><?php echo auto_link_text($com->getEntry());?></dd>
    <?php endforeach;?>
  </dl>
</p>
<?php endif;?>

<?php if(isset($staff) && $staff):?>
  <h2><?php echo __('Staff Member');?> :</h2>
  <p>
  <dl>
    <dt><?php echo __('Name')?><dt>
    <dd><?php echo $staff->getFormatedName();?></dd>
  </dl>
  </p>
<?php endif;?>
</div>
