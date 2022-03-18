CREATE AGGREGATE array_concat_agg(anycompatiblearray) (
   SFUNC = array_cat,
   STYPE = anycompatiblearray
);