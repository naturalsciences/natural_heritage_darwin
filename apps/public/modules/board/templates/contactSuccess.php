<?php slot('title', __('About Us'));  ?>

<div class="page">
  <h1><?php echo __('Contact');?></h1>
  <div class="contact_content">
    <p>
      <?php echo __('If you have a problem using the tool, or if you want to help : write a mail at <a href="mailto:%mail%">%mail%</a>', array('%mail%'=>$contact['mail']));?>
    <p>
    <p>
      <?php echo __('If you have a question about a specimen, please contact the collection manager of the specimen. <br />
      you can find his or her e-mail address in the record.');?>
    </p>
    <p><?php echo __("Scientists wishing to visit one of our collections should contact the collection manager directly.  For more information on how to apply for a visit, please refer to the RBINS rules and regulations below . Currently, this pdf document is not yet available in English.") ; ?>
    <br />
    <?php echo link_to(__('Settlement of external visiting scientists'),$file1);?>
    </p>
    <p><?php echo __("Academic personnel wishing to loan collection objects should contact the collection manager directly.  For more information on how to apply for a loan, please refer to the RBINS rules and regulations below . Currently, this pdf document is not yet available in English.") ; ?> 
    <br />
    <?php echo link_to(__('Settlement of loans for study'),$file2);?>  
  </div>
</div>
