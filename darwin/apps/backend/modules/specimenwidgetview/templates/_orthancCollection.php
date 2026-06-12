
<?php if($has_2d_orthanc===true): ?>

<div id="orthanc_coll_container">
<div id="uv_collection_orthanc" class="uv" style="width:100%; height:600px;"></div>
List of IIIF images: <select id="sel_uv"></select>
</div>
<script language="javascript">
	console.log("ready");
	var orthanc_2d_json=<?php print(htmlspecialchars_decode($orthanc_2d_links_json)); ?>;
	console.log(orthanc_2d_json);
	var uuid="<?php print($uuid);?>"
	var go_iiif=false;
	var i_iiif_manifest=0;
	function loadUV(callback) 
	{
		const s = document.createElement("script");
		s.src = "<?php echo sfConfig::get('dw_root_url_darwin_backend'); ?>/universalviewer/4_2_1/UV.js";
		s.onload = callback;
		document.body.appendChild(s);
	}
	
	
	var load_manifest_in_viewer=function(url_manifest, p_uuid)
    {
		
		if(i_iiif_manifest==0)
		{
		 loadUV(function () {
							if (window.UV) {																																								
								UV.init("uv_collection_orthanc", 
								{
									manifest: url_manifest,
									embedded: true });
							} else {
								console.error("Universal Viewer not loaded");
								}
							});


		}
		i_iiif_manifest=i_iiif_manifest+1;
		
		 $('#sel_uv').append($('<option>', {
					value:url_manifest,
					text: url_manifest
				}));
		
	}	
	
	
	
	
	
	$(document).ready(
			function()
			{
				loadUV()
				process_orthanc_iiif(orthanc_2d_json, uuid, load_manifest_in_viewer);
				$("#orthanc_coll_container").show();
				
				$("#sel_uv").on('change', function() {
					  console.log( this.value );																					
						UV.init("uv_collection_orthanc", {																																																						
						manifest: this.value,
						embedded: true
						});																																					 
					});
				
			}
		);
	
</script>
<?php endif;?>
