<?php if(count($items) !=0 ):?>
  <?php use_helper('Text');?>
  <table>
    <?php foreach($items as $item):?>
      <?php include_partial('item_specimen',array('item'=>$item) );?>
    <?php endforeach;?>
  </table>
  <script  type="text/javascript">
   $(document).ready(function () {
      $('.row_delete').click(function(event)
      {
        event.preventDefault();
        $(this).closest('tr').remove();
        checkItem();
      });

      $('img.extd_info').each(function(){
        tip_content = $(this).next().html();
        $(this).qtip(
        {
          content: tip_content,
          style: {
            tip: true, // Give it a speech bubble tip with automatic corner detection
            name: 'cream'
         }
        });
      });
   });
  </script>
<?php else:?>
  <p class="warn_message"><?php echo __('No Items here. Please pin some items or another source to be able to do a mass action');?></p>
<?php endif;?>
