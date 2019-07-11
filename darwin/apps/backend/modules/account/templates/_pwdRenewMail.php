<?php if(isset($userParams) && isset($userParams['user_id']) && isset($userParams['hash'])):?>
<?php $invitation = __('Dear')." ";
    if($userParams['physical'])
    {
      if (empty($userParams['title']))
      {
        $invitation .= $userParams['name'];
      }
      else
      {
        $invitation .= $userParams['title']." ".$userParams['name'];
      }
    }
    else
    {
      $invitation .= __('member of')." ".$userParams['name'];
    }
    $invitation .= ",\r\r";?>
<?php echo $invitation;?>
<?php echo __('You wished to renew your password to DaRWIN2 application.')."\r";?>
<?php echo __('By clicking this link, you will be redirected on a page where you will be asked for password renewal:')."\r\r";?>
<?php echo "http://".$sf_request->getHost().url_for('account/renewPwd?'.http_build_query(array('id'=>$userParams['user_id'], 'hash'=>$userParams['hash'])))."\r\r";?>
<?php echo __('DaRWIN 2 team');?>
<?php endif;?>
