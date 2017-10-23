<div class="page well_registered">
  <div class="white_content">
    <?php if(isset($params['name']) && isset($params['physical']) && isset($params['title'])):?>
      <?php if($params['physical'] && !empty($params['name'])):?>
        <p>
          <?php if(empty($params['title'])):?>
            <?php echo __('Dear %title%,', array('%title%'=>$params['title']));?>
          <?php else:?>
            <?php echo __('Dear %title% %name%,', array('%title%'=>$params['title'], '%name%' => $params['name']));?>
          <?php endif;?>
        </p>
      <?php endif;?>
    <?php endif;?>
    </br>
    <p><?php echo __('You should have received a link to renew your password on the e-mail address provided.');?></p>
    <p><?php echo __('Please check your mail box.');?></p>
    <p id="team_signature"><?php echo __('DaRWIN 2 team');?></p>
  </div>
</div>
