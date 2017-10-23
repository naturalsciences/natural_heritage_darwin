<div class="catalogue_table_view">
  <ul>
    <?php foreach($Codes as $code):?>
    <li class="main_codes_display">
      <?php echo $code->getCodePrefix().
                 $code->getCodePrefixSeparator().
                 $code->getCode().
                 $code->getCodeSuffixSeparator().
                 $code->getCodeSuffix()
      ;?>
    </li>
    <?php endforeach ; ?>
  </ul>
</div>

