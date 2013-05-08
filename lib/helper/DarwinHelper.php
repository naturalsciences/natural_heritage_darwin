<?php

function word2color($w){
  if (strlen($w)==0) return substr('00000' . dechex(mt_rand(0, 0xffffff)), -6);
  while (strlen($w)<6) $w.=$w;
  $minbrightness=1;  // range from 0 to 15, if this is 0 then for ex. black is allowed
  $max_brightness=14; // range from 0 to 15, if this is 15 then for ex. white is allowed
  $plus_red=0;    // set one of these to set the probability of one of these colors higher
  $plus_green=0;
  $plus_blue=0;
  $r='';
  for ($i=1; $i<6; $i++) {
      #$r.= '">';// this is a depug mode, to see the color written
      $plus=0;
      if ($plus_red<>0 and $i==0) $plus=$plus_red;
      if ($plus_green<>0 and $i==2) $plus=$plus_green;
      if ($plus_blue<>0 and $i==4) $plus=$plus_blue;

      $offset = round(strlen($w)/6*$i);
      $c= substr ($w, $offset, 1);
      $dec=ord($c)%($max_brightness+$plus-$minbrightness) +$minbrightness+$plus;
      if ($dec>$max_brightness-$minbrightness) $dec=$max_brightness-$minbrightness;
      $r.= strtoupper( dechex($dec) );
  }
  return $r;
}

function help_ico($message, $sf_user)
{
  if(! $sf_user->getHelpIcon()) return '';
  return '<div class="help_ico"><span>'.$message.'</span></div>';
}
?>
