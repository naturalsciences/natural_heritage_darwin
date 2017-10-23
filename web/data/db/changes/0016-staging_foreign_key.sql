ALTER TABLE staging 
    add constraint fk_staging_chronostratigraphy foreign key (chrono_ref) references chronostratigraphy(id) on delete set NULL,
    add constraint fk_staging_lithostratigraphy foreign key (litho_ref) references lithostratigraphy(id) on delete set NULL,
    add constraint fk_staging_lithology foreign key (lithology_ref) references lithology(id) on delete set NULL,
    add constraint fk_staging_mineralogy foreign key (mineral_ref) references mineralogy(id) on delete set NULL ;
