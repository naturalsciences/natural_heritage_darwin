<?php slot('title',__("Darwin Quality Assurance"));?>
<div class="page">
  <h1><?php echo __('Darwin Code Fixer');?></h1>
    <p class="info">
     Still <?php echo $fix_count;?> codes to correct in your collections.
    </p>
    <br />
    <table class="qa_table">
    <thead>
      <tr>
        <th><?php echo __('Specimen');?></th>
        <th><?php echo __('Specimens Code');?></th>
        <th></th>
        <th></th>
      </tr>
    </thead>
    <tbody >
    <?php foreach ($fixes as $s_id => $spec):?>
      <?php foreach($spec['codes'] as $scode):?>
      <tr>
        <td>S_ID # <?php echo link_to($s_id,'specimen/edit?id='.$s_id) ;?></td>
        <td>
          (<?php echo $scode['s_code_category'];?>)
           <?php echo $scode['s_code_prefix'].$scode['s_code_prefix_separators'].
           $scode['s_code'].$scode['s_code_suffix_separator'].$scode['s_code_suffix']; ?>
        </td>
        <td>
         <form>
            <table data-codeid="<?php echo $scode['s_id'];?>">
              <thead>
                <tr>
                  <th><?php echo __('Part');?></th>
                  <th><?php echo __('Part Code');?></th>
                  <th><?php echo __('Move to :');?></th>
                </tr>
              </thead>
              <tbody>
              <?php foreach($spec['parts'] as $p_id => $part):?>
                <tr>
                  <td>P_ID # <?php echo link_to($p_id,'parts/edit?id='.$p_id) ;?></td>
                  <td>
                    <?php if( count($part) != 0): ?>
                    <ul>
                    <?php foreach($part as $pcode):?>
                      <li>(<?php echo $pcode['p_code_category'];?>)
                        <?php echo $pcode['p_code_prefix'].$pcode['p_code_prefix_separators'].
                        $pcode['p_code'].$pcode['p_code_suffix_separator'].$pcode['p_code_suffix']; ?>
                      </li>
                    <?php endforeach;?>
                    </ul>
                    <?php else:?>-
                    <?php endif;?>
                  </td>
                  <td><input type="checkbox" class="add_to" name="add_to" value="<?php echo $p_id;?>" /></td>
                </tr>
              <?php endforeach;?>
              <tfoot>
              <tr>
                <td></td>
                <td></td>
                <td>
                  <strong><input type="checkbox" value="delete" /> <?php echo __('Delete');?></strong>
                </td>
              </tr>
              </tfoot>
              </tbody>
            </table>
          </form>
        </td>
        <td><input type="button" value="Move" class="move"></td>
      </tr>
      <?php endforeach;?>
    <?php endforeach;?>
  </table>
</div>

<script type="text/javascript">
$(document).ready(function () {
  $('.qa_table .add_to').change(function() {
    $(this).closest('table').find('tfoot input:checkbox').attr('checked', false);
  });
  
  $('.qa_table tfoot input:checkbox').change(function() {
    $(this).closest('table').find('tbody input:checkbox').attr('checked', false);
  });
  
  $('.qa_table .move').click(function() {
    parent = $(this).closest('tr').find('table');
    console.log(parent);
    
    if(parent.find('input:checked').length == 0)
      alert('Choose at least 1 thing to do');

    var to= [];
    parent.find('.add_to:checked').each(function() {
      to.push( $(this).val() );
    });

    $.ajax({
      type: "POST",
      url: '<?php echo url_for('qa/move');?>',
      data: { 
        from: parent.attr('data-codeid'),
        to: to.join(',')
      },
      success: function(html) {
      }
    });

    parent.closest('tr').hide();
    if(parent.closest('tr').parent().find('tr:visible').length == 0) {
      location.reload();
    }
  });
});
</script>
