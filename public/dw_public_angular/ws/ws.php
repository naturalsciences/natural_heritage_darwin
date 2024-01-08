<?php

require_once("lib.php");


function main_op()
{
	
	try
	{
		if(array_key_exists("collection_id", $_REQUEST))
		{
			if($_REQUEST["collection_id"]=="-1")
			{
				
				unset($_REQUEST['collection_id']);
				
			}
		}
		if(array_key_exists("operation", $_REQUEST))
		{
			if($_REQUEST["operation"]=="get_collections")
			{
				json_darwin_get_collections();
			}
			
			elseif($_REQUEST["operation"]=="get_sub_collections")
			{
				json_darwin_get_sub_collections($_REQUEST["col"]);
			}
			
			elseif($_REQUEST["operation"]=="get_codes")
			{
				if(array_key_exists("q", $_REQUEST)&&array_key_exists("col", $_REQUEST))
				{
					if(array_key_exists("taxon", $_REQUEST))
					{
						json_darwin_get_code_by_taxon($_REQUEST["q"], $_REQUEST["col"], $_REQUEST['taxon']);
					}
					else
					{
						json_darwin_get_code($_REQUEST["q"], $_REQUEST["col"]);
					}
				}
				else
				{
					json_darwin_get_code($_REQUEST["q"]);
				}
			}
			elseif($_REQUEST["operation"]=="get_ig_num")
			{
				if(array_key_exists("q", $_REQUEST)&&array_key_exists("col", $_REQUEST))
				{
					json_darwin_get_ig_num($_REQUEST["q"], $_REQUEST["col"]);
				}
				else
				{
					json_darwin_get_ig_num($_REQUEST["q"]);
				}
			
			}
			elseif($_REQUEST["operation"]=="get_countries")
			{
			
				if(array_key_exists("q", $_REQUEST)&&array_key_exists("col", $_REQUEST)&&array_key_exists("taxon", $_REQUEST))
				{	
					json_darwin_get_countries_by_specimen($_REQUEST["q"], $_REQUEST["col"], $_REQUEST['taxon']);
				}
			}
			elseif($_REQUEST["operation"]=="get_localities")
			{

				if(array_key_exists("q", $_REQUEST)&&array_key_exists("col", $_REQUEST)&&array_key_exists("taxon", $_REQUEST)&&array_key_exists("col", $_REQUEST)&&array_key_exists("country", $_REQUEST))
				{	
					json_darwin_get_localities_by_country($_REQUEST["q"], $_REQUEST["col"], $_REQUEST['taxon'], $_REQUEST['country']);
				}
			}
			elseif($_REQUEST["operation"]=="get_types")
			{
				if(array_key_exists("col", $_REQUEST))
				{
					json_darwin_get_types_by_collection($_REQUEST['col']);
				}
				else
				{
					json_darwin_get_types();
				}
			}
			
			
			elseif($_REQUEST["operation"]=="get_collectors"
				&& array_key_exists("col", $_REQUEST)&& array_key_exists("q", $_REQUEST)&&
				array_key_exists("taxon", $_REQUEST)&& array_key_exists("country", $_REQUEST) &&
				 array_key_exists("locality", $_REQUEST)
				)
			{
				json_darwin_get_collectors_collection_taxa_country_locality($_REQUEST['q'],$_REQUEST['col'],$_REQUEST['taxon'],$_REQUEST['country'],$_REQUEST['locality']);
			}
			elseif($_REQUEST["operation"]=="get_collectors"
				&& array_key_exists("col", $_REQUEST)&& array_key_exists("q", $_REQUEST)&&
				array_key_exists("taxon", $_REQUEST)
				)
			{
				json_darwin_get_collectors_collection_taxa_country_locality($_REQUEST['q'],$_REQUEST['col'],$_REQUEST['taxon'],"-1","-1");
			}
			elseif($_REQUEST["operation"]=="get_collectors"
				&& array_key_exists("col", $_REQUEST)&& array_key_exists("q", $_REQUEST))
			{
				json_darwin_get_collectors_collection($_REQUEST['q'],$_REQUEST['col']);
			}
			elseif($_REQUEST["operation"]=="search_specimen")
			{
				 json_darwin_search_specimens();
			}
			elseif($_REQUEST["operation"]=="search_specimen_debug")
			{
				 json_darwin_search_specimens(true);
			}
			elseif($_REQUEST["operation"]=="count_georef_specimen")
			{
				 json_darwin_count_geo_ref();
			}
			elseif($_REQUEST["operation"]=="get_specimen"&& array_key_exists("uuid", $_REQUEST))
			{
				json_darwin_get_specimen($_REQUEST["uuid"]);
			}
			elseif($_REQUEST["operation"]=="get_specimen"&& array_key_exists("id", $_REQUEST))
			{
				json_darwin_get_specimen_id($_REQUEST["id"]);
			}
			elseif($_REQUEST["operation"]=="get_collection_id"&& array_key_exists("code", $_REQUEST))
			{
				json_darwin_get_collection_id($_REQUEST["code"]);
			}
			elseif($_REQUEST["operation"]=="get_taxon"&& array_key_exists("q", $_REQUEST)&& array_key_exists("rank_id", $_REQUEST))
			{
				$include_lower=false;
				
				if(array_key_exists("include_lower", $_REQUEST))
				{
					if(strtolower($_REQUEST["include_lower"])=="true")
					{
						$include_lower=true;
					}
					
				}
				if(array_key_exists("collection_id", $_REQUEST))
				{
					if(array_key_exists("parent_id", $_REQUEST))
					{
						
						json_darwin_get_taxon_by_collection_and_parent($_REQUEST["q"],$_REQUEST["rank_id"],$_REQUEST["collection_id"], $_REQUEST["parent_id"], $include_lower);
					}
					else
					{
						json_darwin_get_taxon_by_collection($_REQUEST["q"],$_REQUEST["rank_id"],$_REQUEST["collection_id"], $include_lower);
					}
				}
				else
				{
					if(array_key_exists("parent_id", $_REQUEST))
					{
						json_darwin_get_taxon_by_parent_generic($_REQUEST["q"],$_REQUEST["rank_id"],$_REQUEST["parent_id"],$include_lower );
					}
					else
					{
						json_darwin_get_taxon_generic($_REQUEST["q"],$_REQUEST["rank_id"],$include_lower );
					}
				}
				
			}
			else
			{
				print("unrecognized query");
			}
			
		}
		
	} 
	catch (PDOException $e) 
	{
		echo 'ERROR: ' . $e->getMessage();
	}
	
}

main_op();

?>