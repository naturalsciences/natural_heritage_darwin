SET SESSION session_replication_role = replica;  

SET search_path = darwin2, public;
\i ../createfunctions.sql

UPDATE catalogue_properties SET 
                property_tool_indexed = COALESCE(fullToIndex(property_tool),''),
                property_sub_type_indexed = COALESCE(fullToIndex(property_sub_type),''),
                property_method_indexed = COALESCE(fullToIndex(property_method),''),
                property_qualifier_indexed = COALESCE(fullToIndex(property_qualifier),''); 

UPDATE chronostratigraphy SET
                name_order_by = fullToIndex(name);

UPDATE collections SET 
                name_indexed = fullToIndex(name);
            
UPDATE expeditions SET 
                name_indexed = fullToIndex(name);

UPDATE habitats SET 
                code_indexed = fullToIndex(code);

UPDATE identifications SET 
                value_defined_indexed = COALESCE(fullToIndex(value_defined),'');

UPDATE lithology SET 
                name_order_by = fullToIndex(name);
UPDATE lithostratigraphy SET 
                name_order_by = fullToIndex(name);

UPDATE mineralogy SET 
                name_order_by = fullToIndex(name),
                formule_indexed = fullToIndex(formule);

UPDATE multimedia SET 
                title_indexed = fullToIndex(title);

UPDATE multimedia_keywords SET 
                keyword_indexed = fullToIndex(keyword);

UPDATE people SET 
                formated_name_indexed = COALESCE(fullToIndex(formated_name),''),
                name_formated_indexed = fulltoindex(coalesce(given_name,'') || coalesce(family_name,'')),
                formated_name_unique = COALESCE(toUniqueStr(formated_name),'');

UPDATE codes SET 
                full_code_order_by = fullToIndex(COALESCE(code_prefix,'') || COALESCE(code::text,'') || COALESCE(code_suffix,'') );

UPDATE tag_groups SET 
                group_name_indexed = fullToIndex(group_name),
                sub_group_name_indexed = fullToIndex(sub_group_name);

UPDATE taxonomy SET 
                name_order_by = fullToIndex(name);

UPDATE classification_keywords SET 
                keyword_indexed = fullToIndex(keyword);

UPDATE users SET 
                formated_name_indexed = COALESCE(fullToIndex(formated_name),''),
                formated_name_unique = COALESCE(toUniqueStr(formated_name),'');

UPDATE class_vernacular_names SET 
                community_indexed = fullToIndex(community);

UPDATE vernacular_names SET 
                name_indexed = fullToIndex(name);

UPDATE igs SET 
                ig_num_indexed = fullToIndex(ig_num);

UPDATE collecting_methods SET 
                method_indexed = fullToIndex(method);

UPDATE collecting_tools SET 
                tool_indexed = fullToIndex(tool);

UPDATE gtu g1
    SET tag_values_indexed = (SELECT array_agg(tags_list)
                              FROM (SELECT lineToTagRows(tag_agg) AS tags_list
                                    FROM (SELECT tag_value AS tag_agg
                                          FROM tag_groups t
                                          WHERE gtu_ref = g1.id
                                         ) as tag_list_selection
                                   ) as tags_rows
                             );

UPDATE darwin_flat f SET
     expedition_name_indexed = COALESCE(fullToIndex(expedition_name),''),
     taxon_name_order_by = COALESCE(fullToIndex(taxon_name),''),
     chrono_name_order_by = COALESCE(fullToIndex(chrono_name),''),
     litho_name_order_by = COALESCE(fullToIndex(litho_name),''),
     lithology_name_order_by = COALESCE(fullToIndex(lithology_name),''),
     mineral_name_order_by = COALESCE(fullToIndex(mineral_name),''),
     host_taxon_name_order_by = COALESCE(fullToIndex(host_taxon_name),''),
     ig_num_indexed = COALESCE(fullToIndex(ig_num),''),

     gtu_tag_values_indexed = (select tag_values_indexed from gtu where gtu.id = f.gtu_ref),
     gtu_country_tag_indexed = ( select lineToTagArray(taggr.tag_value) from tag_groups taggr WHERE taggr.gtu_ref=f.gtu_ref
        AND taggr.group_name_indexed = 'administrativearea' AND taggr.sub_group_name_indexed = 'country')
;
