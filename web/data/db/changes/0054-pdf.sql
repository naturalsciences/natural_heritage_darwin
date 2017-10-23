begin;
set search_path=darwin2,public;

alter table multimedia add column extracted_info text;

/***
* Trigger function fct_cpy_fullToIndex
* Call the fulltoIndex function for different tables
*/
CREATE OR REPLACE FUNCTION fct_cpy_fullToIndex() RETURNS trigger
AS $$
BEGIN
        IF TG_TABLE_NAME = 'properties' THEN
                NEW.applies_to_indexed := COALESCE(fullToIndex(NEW.applies_to),'');
                NEW.method_indexed := COALESCE(fullToIndex(NEW.method),'');
        ELSIF TG_TABLE_NAME = 'chronostratigraphy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'collections' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'expeditions' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'bibliography' THEN
                NEW.title_indexed := fullToIndex(NEW.title);
        ELSIF TG_TABLE_NAME = 'identifications' THEN
                NEW.value_defined_indexed := COALESCE(fullToIndex(NEW.value_defined),'');
        ELSIF TG_TABLE_NAME = 'lithology' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'lithostratigraphy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'mineralogy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
                NEW.formule_indexed := fullToIndex(NEW.formule);
        ELSIF TG_TABLE_NAME = 'people' THEN
                NEW.formated_name_indexed := COALESCE(fullToIndex(NEW.formated_name),'');
                NEW.name_formated_indexed := fulltoindex(coalesce(NEW.given_name,'') || coalesce(NEW.family_name,''));
                NEW.formated_name_unique := COALESCE(toUniqueStr(NEW.formated_name),'');
        ELSIF TG_TABLE_NAME = 'codes' THEN
                IF NEW.code ~ '^[0-9]+$' THEN
                    NEW.code_num := NEW.code;
                ELSE
                    NEW.code_num := null;
                END IF;
                NEW.full_code_indexed := fullToIndex(COALESCE(NEW.code_prefix,'') || COALESCE(NEW.code::text,'') || COALESCE(NEW.code_suffix,'') );
        ELSIF TG_TABLE_NAME = 'tag_groups' THEN
                NEW.group_name_indexed := fullToIndex(NEW.group_name);
                NEW.sub_group_name_indexed := fullToIndex(NEW.sub_group_name);
        ELSIF TG_TABLE_NAME = 'taxonomy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'classification_keywords' THEN
                NEW.keyword_indexed := fullToIndex(NEW.keyword);
        ELSIF TG_TABLE_NAME = 'users' THEN
                NEW.formated_name_indexed := COALESCE(fullToIndex(NEW.formated_name),'');
                NEW.formated_name_unique := COALESCE(toUniqueStr(NEW.formated_name),'');
        ELSIF TG_TABLE_NAME = 'vernacular_names' THEN
                NEW.community_indexed := fullToIndex(NEW.community);
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'igs' THEN
                NEW.ig_num_indexed := fullToIndex(NEW.ig_num);
        ELSIF TG_TABLE_NAME = 'collecting_methods' THEN
                NEW.method_indexed := fullToIndex(NEW.method);
        ELSIF TG_TABLE_NAME = 'collecting_tools' THEN
                NEW.tool_indexed := fullToIndex(NEW.tool);
        ELSIF TG_TABLE_NAME = 'loans' THEN
                NEW.search_indexed := fullToIndex(COALESCE(NEW.name,'') || COALESCE(NEW.description,''));
        ELSIF TG_TABLE_NAME = 'multimedia' THEN
                NEW.search_indexed := fullToIndex ( COALESCE(NEW.title,'') ||  COALESCE(NEW.description,'') || COALESCE(NEW.extracted_info,'') ) ;
        ELSIF TG_TABLE_NAME = 'comments' THEN
                NEW.comment_indexed := fullToIndex(NEW.comment);
        ELSIF TG_TABLE_NAME = 'ext_links' THEN
                NEW.comment_indexed := fullToIndex(NEW.comment);
        ELSIF TG_TABLE_NAME = 'specimens' THEN
                NEW.object_name_indexed := fullToIndex(COALESCE(NEW.object_name,'') );
        END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;


commit;
