<?php slot('title', __('Discover DaRWIN'));  ?>

<div class="page tour">

   <div class="tour_head">
    <h1><?php echo __("Discover the DaRWIN Interface");?></h1>
    <p><?php echo __("DaRWIN is not just a website for displaying our collections, it's an entire product of collection management.
    <br />You can register free of charge and discover this nice tool.<br />
    Let's take a look of some of our amazing features :");?>
    <p>
  </div>

  <div class="reg_bottom">
    <a href="<?php echo url_for('register/index');?>" class="fan_register_but"><?php echo __('Register Now!');?></a>
  </div>  

  <div class="tour_text tour_left">
    <h2><?php echo __("Friendly Working Space:");?></h2>
    <?php echo image_tag('public/saved.png',array('class'=>'tour_img'));?>
    <p><?php echo __("Everything in the DaRWIN interface is build to make <strong>YOUR</strong> job easier.<br />
     You work on a set of specimens? You want to save a search you often use? you want to know who has changed the info?<br />
     <strong>DaRWIN can do that!</strong>");?></p>
  </div>
<div class="clear"></div>

  <div class="tour_text tour_right">
    <h2><?php echo __("Enhanced Search :");?></h2>
    <?php echo image_tag('public/map_search.png',array('class'=>'tour_img'));?>
    <p><?php echo __("You find the public search a little bit too restrictive? <br />
      Let's try our DaRWIN search tool. You will be able to search for tons of information.<br />
      From Map to complex taxonomy and synonymies searches.");?></p>
  </div>

  <div class="reg_bottom">
    <a href="<?php echo url_for('register/index');?>" class="fan_register_but"><?php echo __('Register Now!');?></a>
  </div>

  <div class="tour_text tour_left">
    <h2><?php echo __("Customizable Interface :");?></h2>
    <?php echo image_tag('public/customize.png',array('class'=>'tour_img'));?>
    <p><?php echo __("In the Darwin tool you have the ability to customize freely your interface.<br />
      You are only interested in taxonomy? No problem, no other infos will clutter your tool.<br />
      Hide or minimize the information you don't need to focus on.");?></p>
  </div>
  <div class="clear last_item"></div>
  

</div>
