
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
		console.log("debug");
		console.log("<?php echo sfConfig::get('dw_root_url_darwin_backend'); ?>");
		console.log(s.src);
		s.onload = callback;
		document.body.appendChild(s);
	}
	
	var build_orthanc_query=function(p_url, p_type, p_level, p_query, p_success)
	{
		var data={}
		data["Level"]=p_level;
		data["Expand"]=true;
		data["Limit"]=100;
		data["Full"]=true;
		for (var k in p_query)
		{			
			data[k]=p_query[k];			
		}
		console.log(data);
		/*data = Object.assign({}, data, p_query);
		*/
		
		
		 return $.ajax({
				url: p_url,
				type: p_type,
				data: JSON.stringify(data),
				dataType: "json",
				success: p_success,
				
			});

	}
	//https://naturalheritage.africamuseum.be/Orthanc/wsi/iiif/frames-pyramids/f218cb76-c077a7ce-aa6881c8-515dd159-04c71011/0/manifest.json
	
	var parse_instance=function(base_url, url_inst_tag, p_serie)
	{
		build_orthanc_query(
							url_inst_tag, 
							"GET",
							"Instance", 
							{},
							function( result ) {
								console.log(result);
								if("0008,0016" in result)
								{
									var sop_class_tag=result["0008,0016"];
									
									console.log(sop_class_tag);
									var sop_class=sop_class_tag["Value"];
									console.log(sop_class);
									if(sop_class=="1.2.840.10008.5.1.4.1.1.77.1.6"||sop_class=="1.2.840.10008.5.1.4.1.1.7")
									{
										console.log("try to get manifest");
										console.log("serie=");
										console.log(p_serie);
										var url_manifest=base_url+"wsi/iiif/series/"+p_serie+"/manifest.json";
										console.log(url_manifest);
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
								}
							});
	}
	
	var find_root_iiif=function(base_url, p_sop, p_serie)
	{
		var query={"Query":{"SOPInstanceUID":p_sop}};
		
		var url_orthanc_find=base_url+"/tools/find";
		build_orthanc_query(
							url_orthanc_find, 
							"POST",
							"Instance", 
							query,
							function( result ) {
								console.log(result);
								for(var i=0; i<result.length;i++)
								{
									var inst_id=result[i]["ID"];
									console.log(inst_id);
									var url_inst_tag=base_url+"/instances/"+inst_id+"/tags";
									console.log(url_inst_tag);
									parse_instance(base_url, url_inst_tag, p_serie);
								}
							}
							);
	}
	var parse_orthanc_instance=function(url_instance, base_url, p_serie)
	{
			build_orthanc_query(
					url_instance, 
							"GET",
							"", 
							{},
							function( result ) {
								console.log(result);
								/*if("0008,0016" in result)
								{
									var sop_class_tag=result["0008,0016"];
									console.log(url_instance);
									console.log(sop_class_tag);
									var sop_class=sop_class_tag["Value"];
									console.log(sop_class);
								}*/
								if("MainDicomTags" in result)
								{
									var main_sop_inst=result["MainDicomTags"]["SOPInstanceUID"];
									
									var inst_id=result["MainDicomTags"]["InstanceNumber"];
									if(inst_id==1)
									{
										console.log(main_sop_inst);
										find_root_iiif(base_url, main_sop_inst, p_serie);
									}
								}
							});
	}
	var  get_orthanc_2d_series=function(url_series_id, base_url, p_serie)
	{
		
		build_orthanc_query(
			url_series_id, 
							"GET",
							"", 
							{},
							function( result ) {
								console.log(result);
								if("Instances" in result)
								{
									for(var i=0;i<result["Instances"].length;i++)
									{
										var inst_id=result["Instances"][i];
										//console.log("inst_id");
										//console.log(inst_id);
										var url_inst=base_url+"/instances/"+inst_id;
										console.log(url_inst);
										parse_orthanc_instance(url_inst,base_url, p_serie);
										
									}
								}
							}
						);
	}
	
	var get_orthanc_2d_studies=function(url_series_find, base_url)
	{
		console.log(url_series_find)
		build_orthanc_query(
							url_series_find, 
							"GET",
							"Study", 
							{},
							function( result ) {
								console.log("serie");
								console.log(result);
								
								for(var i=0; i<result.length; i++)
								{
									if("ID" in result[i])
									{
										var serie_id=result[i]["ID"];
										console.log(serie_id);
										var url_series_id=base_url+"/series/"+serie_id;
										console.log(url_series_id);
										get_orthanc_2d_series(url_series_id, base_url, serie_id);
										
									}
								}
								
								
								
							});
	}
	
	var get_orthanc_patient_links_2d=function(base_url, p_uuid)
					{
						console.log(p_uuid);	
						var url_orthanc_find=base_url+"/tools/find";
						var aux={};
						aux["Query"]={}
						var metadata_query={};
						metadata_query["DarwinUUID"]=p_uuid;
						aux["MetadataQuery"]=metadata_query;
						build_orthanc_query(
							url_orthanc_find, 
							"POST",
							"Patient", 
							aux,
							function( result ) {
								 //console.log(result);
								  if(result!==null)
								  {
									  for(var i=0; i<result.length;i++)
									  {
										   var tmp=result[i];
										   console.log(tmp);
										   if("ID" in tmp)
										   {
											  console.log("patient_id");
											  console.log( tmp["ID"]);
											  if("Studies" in tmp)
											  {
												  for(var j=0; j<tmp["Studies"].length; j++)
												  {
													  var study_id=tmp["Studies"][j];
													  var url_study_to_series=base_url+"/studies/"+study_id+"/series";
													  get_orthanc_2d_studies(url_study_to_series, base_url);
													  console.log(study_id);
												  }
											  }
											  
											 
										   }
									  }
									  
									  
								  }
								}
							);
						
						
					}
	
	var process_orthanc_iiif=function(p_list)
	{					
			for(var i=0; i<p_list.length; i++)
			{
				var tmp=p_list[i];
				get_orthanc_patient_links_2d(tmp, uuid);
				
			}		
	}
	
	
	$(document).ready(
			function()
			{
				loadUV()
				process_orthanc_iiif(orthanc_2d_json);
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
