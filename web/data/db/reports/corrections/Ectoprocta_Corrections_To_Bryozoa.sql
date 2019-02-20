BEGIN;

-- "Spigaleos elegans de Blauwe & Gordon, 2014"
update specimens set taxon_ref = 210222 where taxon_ref = 210373;
update specimens_relationships set taxon_ref = 210222 where taxon_ref = 210373;
update staging set taxon_ref = 210222 where taxon_ref = 210373;
update staging_relationship set taxon_ref = 210222 where taxon_ref = 210373;
update taxonomy set parent_ref = 210222 where parent_ref = 210373;
update catalogue_people set record_id = 210222 
where id in 
(
  select id from catalogue_people as cp
  where referenced_relation = 'taxonomy' 
    and record_id = 210373
    and not exists (
                     select 1 from catalogue_people as cpp
                     where cpp.referenced_relation = cp.referenced_relation
                       and cpp.record_id = 210222
                       and cpp.people_type = cp.people_type
                       and cpp.people_sub_type = cp.people_sub_type
                       and cpp.people_ref = cp.people_ref
                   )
);
update catalogue_relationships set record_id_1 = 210222 
where id in 
(
  select id from catalogue_relationships as cp
  where referenced_relation = 'taxonomy' 
    and record_id_1 = 210373
    and not exists (
                     select 1 from catalogue_relationships as cpp
                     where cpp.referenced_relation = cp.referenced_relation
                       and cpp.record_id_1 = 210222
                       and cpp.relationship_type = cp.relationship_type
                   )
);
update catalogue_relationships set record_id_2 = 210222 
where id in 
(
  select id from catalogue_relationships as cp
  where referenced_relation = 'taxonomy' 
    and record_id_2 = 210373
    and not exists (
                     select 1 from catalogue_relationships as cpp
                     where cpp.referenced_relation = cp.referenced_relation
                       and cpp.record_id_2 = 210222
                       and cpp.relationship_type = cp.relationship_type
                   )
);
update classification_keywords set record_id = 210222 
where id in 
(
  select id from classification_keywords as cp
  where referenced_relation = 'taxonomy' 
    and record_id = 210373
    and not exists (
                     select 1 from classification_keywords as cpp
                     where cpp.referenced_relation = cp.referenced_relation
                       and cpp.record_id = 210222
                       and cpp.keyword_type = cp.keyword_type
                       and cpp.keyword_indexed = cp.keyword_indexed
                   )
);
update comments set record_id = 210222 
where id in 
(
  select id from comments as cp
  where referenced_relation = 'taxonomy' 
    and record_id = 210373
    and not exists (
                     select 1 from comments as cpp
                     where cpp.referenced_relation = cp.referenced_relation
                       and cpp.record_id = 210222
                       and cpp.notion_concerned = cp.notion_concerned
                       and cpp.comment_indexed = cp.comment_indexed
                   )
);
update properties set record_id = 210222 
where id in 
(
  select id from properties as cp
  where referenced_relation = 'taxonomy' 
    and record_id = 210373
    and not exists (
                     select 1 from properties as cpp
                     where cpp.referenced_relation = cp.referenced_relation
                       and cpp.record_id = 210222
                       and cpp.property_type = cp.property_type
                       and cpp.lower_value = cp.lower_value
                       and cpp.upper_value = cp.upper_value
                   )
);
update vernacular_names set record_id = 210222 
where id in 
(
  select id from vernacular_names as cp
  where referenced_relation = 'taxonomy' 
    and record_id = 210373
    and not exists (
                     select 1 from vernacular_names as cpp
                     where cpp.referenced_relation = cp.referenced_relation
                       and cpp.record_id = 210222
                       and cpp.community_indexed = cp.community_indexed
                       and cpp.name_indexed = cp.name_indexed
                   )
);
delete from taxonomy where id = 210373;
-- "Melicerita depressa de Blauwe & Gordon, 2014"
update specimens set taxon_ref = 210218 where taxon_ref = 210369;
update specimens_relationships set taxon_ref = 210218 where taxon_ref = 210369;
update staging set taxon_ref = 210218 where taxon_ref = 210369;
update staging_relationship set taxon_ref = 210218 where taxon_ref = 210369;
update taxonomy set parent_ref = 210218 where parent_ref = 210369;
update catalogue_people set record_id = 210218
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210369
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210218
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210218
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 210369
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210218
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210218
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 210369
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210218
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210218
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210369
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210218
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210218
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210369
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210218
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210218
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210369
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210218
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210218
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210369
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210218
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 210369;
-- "Crisia Lamouroux, 1812"
update specimens set taxon_ref = 210301 where taxon_ref = 30669;
update specimens_relationships set taxon_ref = 210301 where taxon_ref = 30669;
update staging set taxon_ref = 210301 where taxon_ref = 30669;
update staging_relationship set taxon_ref = 210301 where taxon_ref = 30669;
update taxonomy set parent_ref = 210301 where parent_ref = 30669;
update catalogue_people set record_id = 210301
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30669
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210301
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210301
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 30669
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210301
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210301
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 30669
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210301
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210301
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30669
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210301
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210301
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30669
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210301
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210301
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30669
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210301
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210301
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30669
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210301
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 30669;
-- "Hornera Lamouroux, 1821"
update specimens set taxon_ref = 210311 where taxon_ref = 30674;
update specimens_relationships set taxon_ref = 210311 where taxon_ref = 30674;
update staging set taxon_ref = 210311 where taxon_ref = 30674;
update staging_relationship set taxon_ref = 210311 where taxon_ref = 30674;
update taxonomy set parent_ref = 210311 where parent_ref = 30674;
update catalogue_people set record_id = 210311
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30674
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210311
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210311
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 30674
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210311
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210311
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 30674
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210311
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210311
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30674
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210311
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210311
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30674
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210311
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210311
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30674
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210311
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210311
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30674
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210311
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 30674;
-- "Smittina Norman, 1903"
update specimens set taxon_ref = 202545 where taxon_ref IN (30583, 210330);
update specimens_relationships set taxon_ref = 202545 where taxon_ref IN (30583, 210330);
update staging set taxon_ref = 202545 where taxon_ref IN (30583, 210330);
update staging_relationship set taxon_ref = 202545 where taxon_ref IN (30583, 210330);
update taxonomy set parent_ref = 202545 where parent_ref IN (30583, 210330);
update catalogue_people set record_id = 202545
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (30583, 210330)
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202545
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 202545
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 IN (30583, 210330)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 202545
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 202545
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 IN (30583, 210330)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 202545
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 202545
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (30583, 210330)
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202545
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 202545
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (30583, 210330)
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202545
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 202545
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (30583, 210330)
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202545
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 202545
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (30583, 210330)
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202545
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id in (30583, 210330);
-- "Micropora Gray, 1848"
update specimens set taxon_ref = 210227 where taxon_ref IN (30476, 210319);
update specimens_relationships set taxon_ref = 210227 where taxon_ref IN (30476, 210319);
update staging set taxon_ref = 210227 where taxon_ref IN (30476, 210319);
update staging_relationship set taxon_ref = 210227 where taxon_ref IN (30476, 210319);
update taxonomy set parent_ref = 210227 where parent_ref IN (30476, 210319);
update catalogue_people set record_id = 210227
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (30476, 210319)
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210227
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210227
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 IN (30476, 210319)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210227
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210227
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 IN (30476, 210319)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210227
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210227
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (30476, 210319)
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210227
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210227
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (30476, 210319)
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210227
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210227
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (30476, 210319)
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210227
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210227
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (30476, 210319)
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210227
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id in (30476, 210319);
-- "Escharella Gray, 1848"
update specimens set taxon_ref = 30526 where taxon_ref = 210307;
update specimens_relationships set taxon_ref = 30526 where taxon_ref = 210307;
update staging set taxon_ref = 30526 where taxon_ref = 210307;
update staging_relationship set taxon_ref = 30526 where taxon_ref = 210307;
update taxonomy set parent_ref = 30526 where parent_ref = 210307;
update catalogue_people set record_id = 30526
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210307
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30526
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 30526
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 210307
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 30526
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 30526
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 210307
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 30526
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 30526
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210307
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30526
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 30526
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210307
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30526
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 30526
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210307
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30526
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 30526
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210307
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30526
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 210307;
-- "Spigaleos Hayward, 1992"
update specimens set taxon_ref = 210221 where taxon_ref = 210332;
update specimens_relationships set taxon_ref = 210221 where taxon_ref = 210332;
update staging set taxon_ref = 210221 where taxon_ref = 210332;
update staging_relationship set taxon_ref = 210221 where taxon_ref = 210332;
update taxonomy set parent_ref = 210221 where parent_ref = 210332;
update catalogue_people set record_id = 210221
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210332
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210221
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210221
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 210332
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210221
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210221
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 210332
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210221
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210221
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210332
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210221
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210221
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210332
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210221
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210221
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210332
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210221
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210221
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210332
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210221
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 210332;
-- "Melicerita Milne Edwards, 1836"
update specimens set taxon_ref = 210217 where taxon_ref = 210318;
update specimens_relationships set taxon_ref = 210217 where taxon_ref = 210318;
update staging set taxon_ref = 210217 where taxon_ref = 210318;
update staging_relationship set taxon_ref = 210217 where taxon_ref = 210318;
update taxonomy set parent_ref = 210217 where parent_ref = 210318;
update catalogue_people set record_id = 210217
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210318
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210217
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210217
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 210318
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210217
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210217
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 210318
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210217
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210217
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210318
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210217
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210217
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210318
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210217
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210217
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210318
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210217
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210217
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210318
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210217
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 210318;
-- "Cornucopina Levinsen"
update specimens set taxon_ref = 30411 where taxon_ref = 210300;
update specimens_relationships set taxon_ref = 30411 where taxon_ref = 210300;
update staging set taxon_ref = 30411 where taxon_ref = 210300;
update staging_relationship set taxon_ref = 30411 where taxon_ref = 210300;
update taxonomy set parent_ref = 30411 where parent_ref = 210300;
update catalogue_people set record_id = 30411
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210300
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30411
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 30411
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 210300
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 30411
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 30411
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 210300
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 30411
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 30411
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210300
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30411
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 30411
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210300
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30411
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 30411
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210300
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30411
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 30411
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210300
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30411
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 210300;
-- "Crisidia Milne Edwards, 1838"
update specimens set taxon_ref = 210302 where taxon_ref = 30670;
update specimens_relationships set taxon_ref = 210302 where taxon_ref = 30670;
update staging set taxon_ref = 210302 where taxon_ref = 30670;
update staging_relationship set taxon_ref = 210302 where taxon_ref = 30670;
update taxonomy set parent_ref = 210302 where parent_ref = 30670;
update catalogue_people set record_id = 210302
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30670
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210302
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210302
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 30670
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210302
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210302
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 30670
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210302
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210302
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30670
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210302
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210302
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30670
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210302
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210302
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30670
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210302
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210302
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30670
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210302
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 30670;
-- "Cellaria Ellis & Solander, 1786"
update specimens set taxon_ref = 210297 where taxon_ref = 30429;
update specimens_relationships set taxon_ref = 210297 where taxon_ref = 30429;
update staging set taxon_ref = 210297 where taxon_ref = 30429;
update staging_relationship set taxon_ref = 210297 where taxon_ref = 30429;
update taxonomy set parent_ref = 210297 where parent_ref = 30429;
update catalogue_people set record_id = 210297
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30429
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210297
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210297
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 30429
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210297
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210297
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 30429
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210297
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210297
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30429
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210297
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210297
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30429
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210297
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210297
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30429
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210297
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210297
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30429
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210297
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 30429;
-- "Osthimosia Jullien"
update specimens set taxon_ref = 30517 where taxon_ref = 210324;
update specimens_relationships set taxon_ref = 30517 where taxon_ref = 210324;
update staging set taxon_ref = 30517 where taxon_ref = 210324;
update staging_relationship set taxon_ref = 30517 where taxon_ref = 210324;
update taxonomy set parent_ref = 30517 where parent_ref = 210324;
update catalogue_people set record_id = 30517
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210324
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30517
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 30517
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 210324
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 30517
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 30517
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 210324
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 30517
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 30517
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210324
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30517
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 30517
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210324
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30517
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 30517
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210324
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30517
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 30517
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210324
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 30517
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 210324;
-- "Membranicellariidae Levinsen, 1909"
update specimens set taxon_ref = 210223 where taxon_ref = 5033;
update specimens_relationships set taxon_ref = 210223 where taxon_ref = 5033;
update staging set taxon_ref = 210223 where taxon_ref = 5033;
update staging_relationship set taxon_ref = 210223 where taxon_ref = 5033;
update taxonomy set parent_ref = 210223 where parent_ref = 5033;
update catalogue_people set record_id = 210223
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5033
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210223
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210223
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 5033
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210223
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210223
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 5033
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210223
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210223
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5033
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210223
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210223
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5033
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210223
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210223
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5033
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210223
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210223
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5033
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210223
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 5033;
-- "Microporidae Gray, 1848"
update specimens set taxon_ref = 210226 where taxon_ref IN (210280, 5035);
update specimens_relationships set taxon_ref = 210226 where taxon_ref IN (210280, 5035);
update staging set taxon_ref = 210226 where taxon_ref IN (210280, 5035);
update staging_relationship set taxon_ref = 210226 where taxon_ref IN (210280, 5035);
update taxonomy set parent_ref = 210226 where parent_ref IN (210280, 5035);
update catalogue_people set record_id = 210226
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210280, 5035)
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210226
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210226
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 IN (210280, 5035)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210226
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210226
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 IN (210280, 5035)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210226
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210226
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210280, 5035)
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210226
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210226
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210280, 5035)
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210226
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210226
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210280, 5035)
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210226
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210226
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210280, 5035)
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210226
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id in (210280, 5035);
-- "Cellariidae Fleming, 1828"
update specimens set taxon_ref = 210216 where taxon_ref IN (210267, 5019);
update specimens_relationships set taxon_ref = 210216 where taxon_ref IN (210267, 5019);
update staging set taxon_ref = 210216 where taxon_ref IN (210267, 5019);
update staging_relationship set taxon_ref = 210216 where taxon_ref IN (210267, 5019);
update taxonomy set parent_ref = 210216 where parent_ref IN (210267, 5019);
update catalogue_people set record_id = 210216
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210267, 5019)
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210216
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210216
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 IN (210267, 5019)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210216
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210216
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 IN (210267, 5019)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210216
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210216
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210267, 5019)
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210216
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210216
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210267, 5019)
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210216
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210216
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210267, 5019)
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210216
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210216
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210267, 5019)
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210216
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id IN (210267, 5019);
-- "Celleporidae Johnston, 1838"
update specimens set taxon_ref = 210220 where taxon_ref IN (210268, 5051);
update specimens_relationships set taxon_ref = 210220 where taxon_ref IN (210268, 5051);
update staging set taxon_ref = 210220 where taxon_ref IN (210268, 5051);
update staging_relationship set taxon_ref = 210220 where taxon_ref IN (210268, 5051);
update taxonomy set parent_ref = 210220 where parent_ref IN (210268, 5051);
update catalogue_people set record_id = 210220
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210268, 5051)
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210220
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210220
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 IN (210268, 5051)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210220
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210220
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 IN (210268, 5051)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210220
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210220
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210268, 5051)
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210220
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210220
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210268, 5051)
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210220
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210220
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210268, 5051)
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210220
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210220
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (210268, 5051)
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210220
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id IN (210268, 5051);
-- "Crisiidae"
update specimens set taxon_ref = 210271 where taxon_ref = 5116;
update specimens_relationships set taxon_ref = 210271 where taxon_ref = 5116;
update staging set taxon_ref = 210271 where taxon_ref = 5116;
update staging_relationship set taxon_ref = 210271 where taxon_ref = 5116;
update taxonomy set parent_ref = 210271 where parent_ref = 5116;
update catalogue_people set record_id = 210271
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5116
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210271
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210271
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 5116
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210271
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210271
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 5116
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210271
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210271
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5116
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210271
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210271
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5116
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210271
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210271
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5116
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210271
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210271
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5116
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210271
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 5116;
-- "Bugulidae Gray, 1848"
update specimens set taxon_ref = 5016 where taxon_ref = 210263;
update specimens_relationships set taxon_ref = 5016 where taxon_ref = 210263;
update staging set taxon_ref = 5016 where taxon_ref = 210263;
update staging_relationship set taxon_ref = 5016 where taxon_ref = 210263;
update taxonomy set parent_ref = 5016 where parent_ref = 210263;
update catalogue_people set record_id = 5016
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210263
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 5016
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 5016
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 210263
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 5016
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 5016
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 210263
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 5016
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 5016
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210263
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 5016
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 5016
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210263
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 5016
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 5016
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210263
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 5016
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 5016
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210263
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 5016
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 210263;
-- "Hastingsiidae"
update specimens set taxon_ref = 210274 where taxon_ref = 5131;
update specimens_relationships set taxon_ref = 210274 where taxon_ref = 5131;
update staging set taxon_ref = 210274 where taxon_ref = 5131;
update staging_relationship set taxon_ref = 210274 where taxon_ref = 5131;
update taxonomy set parent_ref = 210274 where parent_ref = 5131;
update catalogue_people set record_id = 210274
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5131
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210274
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 210274
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 5131
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 210274
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 210274
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 5131
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 210274
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 210274
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5131
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210274
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 210274
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5131
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210274
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 210274
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5131
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210274
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 210274
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 5131
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 210274
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 5131;
-- "Horneridae Smitt"
update specimens set taxon_ref = 5119 where taxon_ref = 210275;
update specimens_relationships set taxon_ref = 5119 where taxon_ref = 210275;
update staging set taxon_ref = 5119 where taxon_ref = 210275;
update staging_relationship set taxon_ref = 5119 where taxon_ref = 210275;
update taxonomy set parent_ref = 5119 where parent_ref = 210275;
update catalogue_people set record_id = 5119
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210275
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 5119
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 5119
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 210275
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 5119
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 5119
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 210275
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 5119
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 5119
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210275
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 5119
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 5119
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210275
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 5119
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 5119
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210275
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 5119
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 5119
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210275
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 5119
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 210275;
-- "Smittinidae Levinsen, 1909"
update specimens set taxon_ref = 202544 where taxon_ref IN (5087, 210283);
update specimens_relationships set taxon_ref = 202544 where taxon_ref IN (5087, 210283);
update staging set taxon_ref = 202544 where taxon_ref IN (5087, 210283);
update staging_relationship set taxon_ref = 202544 where taxon_ref IN (5087, 210283);
update taxonomy set parent_ref = 202544 where parent_ref IN (5087, 210283);
update catalogue_people set record_id = 202544
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (5087, 210283)
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202544
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 202544
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 IN (5087, 210283)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 202544
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 202544
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 IN (5087, 210283)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 202544
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 202544
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (5087, 210283)
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202544
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 202544
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (5087, 210283)
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202544
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 202544
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (5087, 210283)
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202544
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 202544
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (5087, 210283)
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202544
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id IN (5087, 210283);
-- "Romancheinidae Jullien, 1888"
update specimens set taxon_ref = 208578 where taxon_ref = 210282;
update specimens_relationships set taxon_ref = 208578 where taxon_ref = 210282;
update staging set taxon_ref = 208578 where taxon_ref = 210282;
update staging_relationship set taxon_ref = 208578 where taxon_ref = 210282;
update taxonomy set parent_ref = 208578 where parent_ref = 210282;
update catalogue_people set record_id = 208578
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210282
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 208578
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 208578
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 210282
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 208578
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 208578
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 210282
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 208578
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 208578
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210282
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 208578
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 208578
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210282
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 208578
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 208578
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210282
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 208578
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 208578
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 210282
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 208578
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 210282;
-- "Ctenostomatida Busk, 1852"
update specimens set taxon_ref = 208575 where taxon_ref IN (662, 210257);
update specimens_relationships set taxon_ref = 208575 where taxon_ref IN (662, 210257);
update staging set taxon_ref = 208575 where taxon_ref IN (662, 210257);
update staging_relationship set taxon_ref = 208575 where taxon_ref IN (662, 210257);
update taxonomy set parent_ref = 208575 where parent_ref IN (662, 210257);
update catalogue_people set record_id = 208575
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (662, 210257)
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 208575
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 208575
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 IN (662, 210257)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 208575
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 208575
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 IN (662, 210257)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 208575
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 208575
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (662, 210257)
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 208575
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 208575
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (662, 210257)
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 208575
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 208575
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (662, 210257)
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 208575
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 208575
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (662, 210257)
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 208575
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id IN (662, 210257);
-- "Cheilostomatida Busk, 1852"
update specimens set taxon_ref = 202537 where taxon_ref IN (661, 210256);
update specimens_relationships set taxon_ref = 202537 where taxon_ref IN (661, 210256);
update staging set taxon_ref = 202537 where taxon_ref IN (661, 210256);
update staging_relationship set taxon_ref = 202537 where taxon_ref IN (661, 210256);
update taxonomy set parent_ref = 202537 where parent_ref IN (661, 210256);
update catalogue_people set record_id = 202537
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (661, 210256)
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202537
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 202537
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 IN (661, 210256)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 202537
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 202537
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 IN (661, 210256)
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 202537
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 202537
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (661, 210256)
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202537
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 202537
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (661, 210256)
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202537
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 202537
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (661, 210256)
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202537
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 202537
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id IN (661, 210256)
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202537
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id IN (661, 210256);
-- "Gymnolaemata Allman, 1856"
update specimens set taxon_ref = 202536 where taxon_ref = 132;
update specimens_relationships set taxon_ref = 202536 where taxon_ref = 132;
update staging set taxon_ref = 202536 where taxon_ref = 132;
update staging_relationship set taxon_ref = 202536 where taxon_ref = 132;
update taxonomy set parent_ref = 202536 where parent_ref = 132;
update catalogue_people set record_id = 202536
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 132
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202536
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 202536
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 132
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 202536
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 202536
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 132
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 202536
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 202536
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 132
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202536
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 202536
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 132
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202536
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 202536
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 132
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202536
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 202536
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 132
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 202536
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 132;
-- "Bryozoa"
update specimens set taxon_ref = 109920 where taxon_ref = 30;
update specimens_relationships set taxon_ref = 109920 where taxon_ref = 30;
update staging set taxon_ref = 109920 where taxon_ref = 30;
update staging_relationship set taxon_ref = 109920 where taxon_ref = 30;
update taxonomy set parent_ref = 109920 where parent_ref = 30;
update catalogue_people set record_id = 109920
where id in
      (
        select id from catalogue_people as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30
              and not exists (
            select 1 from catalogue_people as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 109920
                  and cpp.people_type = cp.people_type
                  and cpp.people_sub_type = cp.people_sub_type
                  and cpp.people_ref = cp.people_ref
        )
      );
update catalogue_relationships set record_id_1 = 109920
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_1 = 30
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_1 = 109920
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update catalogue_relationships set record_id_2 = 109920
where id in
      (
        select id from catalogue_relationships as cp
        where referenced_relation = 'taxonomy'
              and record_id_2 = 30
              and not exists (
            select 1 from catalogue_relationships as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id_2 = 109920
                  and cpp.relationship_type = cp.relationship_type
        )
      );
update classification_keywords set record_id = 109920
where id in
      (
        select id from classification_keywords as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30
              and not exists (
            select 1 from classification_keywords as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 109920
                  and cpp.keyword_type = cp.keyword_type
                  and cpp.keyword_indexed = cp.keyword_indexed
        )
      );
update comments set record_id = 109920
where id in
      (
        select id from comments as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30
              and not exists (
            select 1 from comments as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 109920
                  and cpp.notion_concerned = cp.notion_concerned
                  and cpp.comment_indexed = cp.comment_indexed
        )
      );
update properties set record_id = 109920
where id in
      (
        select id from properties as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30
              and not exists (
            select 1 from properties as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 109920
                  and cpp.property_type = cp.property_type
                  and cpp.lower_value = cp.lower_value
                  and cpp.upper_value = cp.upper_value
        )
      );
update vernacular_names set record_id = 109920
where id in
      (
        select id from vernacular_names as cp
        where referenced_relation = 'taxonomy'
              and record_id = 30
              and not exists (
            select 1 from vernacular_names as cpp
            where cpp.referenced_relation = cp.referenced_relation
                  and cpp.record_id = 109920
                  and cpp.community_indexed = cp.community_indexed
                  and cpp.name_indexed = cp.name_indexed
        )
      );
delete from taxonomy where id = 30;

commit;
