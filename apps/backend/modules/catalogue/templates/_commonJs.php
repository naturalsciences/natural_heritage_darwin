<script type="text/javascript">
$(document).ready(function () {
  $('tr#parent_ref').catalogue_level({
      search_url: $('a#searchPUL').attr('href') + '/table/' + $('input[id$=\"_table\"]').val(),
      current_id: $('input[id$=\"_id\"]').val()
  });

  $('body').catalogue({});
});
</script>
