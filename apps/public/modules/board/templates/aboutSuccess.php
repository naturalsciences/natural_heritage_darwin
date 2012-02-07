<?php slot('title', __('About Us'));  ?>

<div class="page">
  <h1><?php echo __('About us');?></h1>
  <div class="about_content">
    <p>
      <?php echo __('Darwin is an <strong>Open Source</strong> tool for collection management.<br />
    It has been build by the IT departement of the Royal Belgian Institute of Natural Sciences.<br />');?>
    </p>
    <br />
    <ul>
      <li>
        <a href="<?php echo __('http://www.naturalsciences.be');?>">
          <?php echo image_tag('public/rbins_logo.png');?>
          <span><?php echo __('Royal Belgian Institute of Natural Sciences');?></span>
        </a>
      </li>
    </ul>
    <br />
  </div>
  <div class="about_content">
    <h2><?php echo __('Our Partners :');?></h2>
    <ul>
      <li>
        <a href="<?php echo __('http://www.belspo.be');?>">
          <?php echo image_tag('public/belspo_logo.jpg');?>
          <span><?php echo __('The Belgian Federal Science Policy Office');?><span>
        </a>
      </li>
    </ul>
  </div>
</div>


