with stats as
(select * from stats_collections_encoding (4, '2014-01-01'::timestamp, '2014-12-31'::timestamp)
union
select * from stats_collections_encoding (231, '2014-01-01'::timestamp, '2014-12-31'::timestamp)
union
select * from stats_collections_encoding (2, '2014-01-01'::timestamp, '2014-12-31'::timestamp)
union
select * from stats_collections_encoding (3, '2014-01-01'::timestamp, '2014-12-31'::timestamp)
union
select * from stats_collections_encoding (1, '2014-01-01'::timestamp, '2014-12-31'::timestamp)
)
select * from (
select * from stats 
union
select 'ZTotal', sum(new_items), sum(updated_items), sum(new_types), sum(updated_types), sum(new_species) from stats
) as global_stats
order by "collection";
