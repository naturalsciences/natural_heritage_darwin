<?php if(isset($userParams)):?>
<?php
    if($userParams['physical'])
    {
      if (empty($userParams['title']))
      {
        echo __('Dear %username%,',array('%username%' => $userParams['name']));
      }
      else
      {
        echo __('Dear %usertitle% %username%,',array('%usertitle%' => $userParams['title'],'%username%'=> $userParams['name']));
      }
    }
    else
    {
      echo __('Dear member of %username%,',array('%username%' => $userParams['name']));
    }
    echo "\r\r";?>
<?php echo __('Thank you for having registered on DaRWIN 2.')."\r";?>
<?php echo __('You can now log in and enjoy our enhanced collection services.')."\r";?>
<?php if(!empty($userParams['username']))
      {
        echo __('As a reminder, here is your username: %username%',array('%username%' => $userParams['username']))."\r\r";
      }?>
<?php echo __('To log you in, you can visit us on %darwinurl%',array('%darwinurl%' => $sf_request->getHost()))."\r\r";?>
<?php echo __('DaRWIN 2 team');?>
<?php endif;?>
