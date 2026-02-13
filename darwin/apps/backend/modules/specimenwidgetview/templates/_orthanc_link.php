<tbody class="t_orthanc_links" row_id="<?php print($i_link);?>"></tbody>
<script language="javascript">
	$(document).ready(
		function()
		{
				console.log("load_orthanc");
				get_orthanc_links("<?php print($base_url);?>","<?php print($uuid);?>","<?php print($i_link);?>");
		}
	);
</script>