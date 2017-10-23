<div class="page well_registered">
  <div id="login_arrow">
    <div id="login_arrow_top_blue">
      <?php echo image_tag('collapse_big.png', array('alt'=>'Login above'));?>
    </div>
    <div id="login_arrow_top_white">
      <?php echo image_tag('collapse_white.png', array('alt'=>'Login above'));?>
    </div>
    <div id="login_arrow_middle_blue">
      <?php echo image_tag('collapse.png', array('alt'=>'Login above'));?>
    </div>
    <div id="login_arrow_middle_white">
      <?php echo image_tag('collapse_white.png', array('alt'=>'Login above'));?>
    </div>
  </div>
  <div class="white_content">
    <?php if(isset($params['name']) && isset($params['physical']) && isset($params['title'])):?>
      <?php if($params['physical'] && !empty($params['name'])):?>
        <p><?php echo __('Dear').' '.((empty($params['title']))?'':$params['title'].' ').$params['name'].',';?></p>
      <?php endif;?>
    <?php endif;?>
    <p><?php echo __('Thank you for having registered on DaRWIN 2.');?></p>
    <p><?php echo __('You should have received a reminder of your username and password on the e-mail address provided.');?></p>
    <p><?php echo __('You can now log in and enjoy our enhanced collection services.');?></p>
    <p id="team_signature"><?php echo __('DaRWIN 2 team');?></p>
  </div>
</div>
