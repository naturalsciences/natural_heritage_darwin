begin;
update classification_keywords as mck
set referenced_relation = 'taxonomy',
    record_id = (select taxon_ref
                 from staging
                 where id = mck.record_id
                )
where referenced_relation = 'staging'
  and keyword_type IN ('GenusOrMonomial', 'Subgenus', 'SpeciesEpithet', 'FirstEpiteth', 'SubspeciesEpithet',  'InfraspecificEpithet', 'AuthorTeamAndYear', 'AuthorTeam', 'AuthorTeamOriginalAndYear', 'AuthorTeamParenthesisAndYear')
  and not exists (select 1
                  from classification_keywords as sck
                  where sck.referenced_relation = 'taxonomy'
                    and sck.keyword_type = mck.keyword_type
                    and sck.record_id = (select taxon_ref
                                         from staging
                                         where id = mck.record_id
                                        )
                    and sck.keyword_indexed = mck.keyword_indexed
                 )
  and exists (select taxon_ref
              from staging
              where id = mck.record_id
                and taxon_ref is not null
             );
--select count(*) from classification_keywords where referenced_relation = 'staging';
commit;
