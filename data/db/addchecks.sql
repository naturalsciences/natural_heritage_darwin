
ALTER TABLE catalogue_relationships add constraint chk_not_related_to_self check (record_id_1 != record_id_2);
ALTER TABLE collections add constraint fct_chk_onceInPath_collections CHECK(fct_chk_onceInPath( COALESCE(path,'') || '/' || id));
ALTER TABLE taxonomy add constraint fct_chk_onceInPath_taxonomy CHECK(fct_chk_onceInPath( COALESCE(path,'') || '/' || id));
ALTER TABLE chronostratigraphy add constraint fct_chk_onceInPath_chronostratigraphy CHECK(fct_chk_onceInPath( COALESCE(path,'') || '/' || id));
ALTER TABLE lithostratigraphy add constraint fct_chk_onceInPath_lithostratigraphy CHECK(fct_chk_onceInPath( COALESCE(path,'') || '/' || id));
ALTER TABLE mineralogy add constraint fct_chk_onceInPath_mineralogy CHECK(fct_chk_onceInPath( COALESCE(path,'') || '/' || id));
ALTER TABLE lithology add constraint fct_chk_onceInPath_lithology CHECK(fct_chk_onceInPath( COALESCE(path,'') || '/' || id));
