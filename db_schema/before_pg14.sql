CREATE AGGREGATE array_concat_agg(anyarray) (
   SFUNC = array_cat,
   STYPE = anyarray
);