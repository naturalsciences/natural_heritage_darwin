set search_path=darwin2,public;

BEGIN;

DROP TYPE IF EXISTS stats_collections CASCADE;
create type stats_collections as (collection varchar, new_items bigint, updated_items bigint, new_types bigint, updated_types bigint, new_species bigint);
DROP TYPE IF EXISTS encoders_stats_collections CASCADE;
create type encoders_stats_collections as (encoder TEXT, collection_path TEXT, new_items bigint, updated_items bigint, new_types bigint, updated_types bigint, new_species bigint);

create or replace function stats_collections_encoding_optimistics (collections.id%TYPE, timestamp, timestamp) returns setof stats_collections language sql immutable as $$
WITH users_statistics AS
(
  WITH users_stats AS
  (
      SELECT DISTINCT
        collection_ref      AS "Collection ID",
        (
          SELECT
        '/'
        ||
        array_to_string(
            array_agg(
                sc.name),
            '/')
        ||
        '/'
        ||
        collection_name
          FROM
            collections AS sc
            INNER JOIN
            (SELECT
               unnest(
                   string_to_array(
                       trim(
                           collection_path,
                           '/'),
                       '/')) :: BIGINT AS id) AS scc
              ON
                sc.id = scc.id
        )                   AS "Collection Path",
        main_ut.action      AS "Action",
        CASE WHEN
          main_s.type
          =
          'specimen'
          THEN 'non type'
        ELSE 'type' END     AS "Type",
        count(*)
        OVER (
          PARTITION BY
            collection_ref,
            action
        )                   AS "Action Count",
        count(*)
        OVER (
          PARTITION BY
            collection_ref,
            action,
            CASE WHEN
              main_s.type = 'specimen'
              THEN 'non type'
            ELSE 'type' END
        )                   AS "Type Count"
      FROM users_tracking AS main_ut
        INNER JOIN specimens AS main_s
          ON main_ut.record_id = main_s.id
             AND main_ut.modification_date_time BETWEEN $2 :: TIMESTAMP AND $3 :: TIMESTAMP
             AND main_ut.referenced_relation = 'specimens'
      WHERE
        CASE
        WHEN 0 != $1
          THEN
            collection_ref IN (SELECT id
                               FROM collections
                               WHERE id = $1 OR path LIKE '%/' || $1 || '/%')
        ELSE
          TRUE
        END
        AND main_ut.action != 'delete'
      ORDER BY "Collection Path", "Action", "Type"
  )
  SELECT
    users_stats."Collection Path",
    users_stats."Action",
    users_stats."Type",
    users_stats."Action Count",
    users_stats."Type Count",
    new_species."New species"
  FROM users_stats
    LEFT JOIN
    (
      SELECT
        s.collection_ref,
        count(DISTINCT tax.id) AS "New species"
      FROM
        (users_tracking AS ut INNER JOIN taxonomy AS tax
            ON ut.referenced_relation = 'taxonomy'
               AND ut.action = 'insert'
               AND ut.record_id = tax.id
               AND tax.level_ref > 47
               AND ut.modification_date_time BETWEEN $2 :: TIMESTAMP AND $3 :: TIMESTAMP
          ) INNER JOIN
        (specimens AS s INNER JOIN users_tracking AS ust
            ON ust.referenced_relation = 'specimens'
               AND ust.action = 'insert'
               AND ust.record_id = s.id
               AND ust.modification_date_time BETWEEN $2 :: TIMESTAMP AND $3 :: TIMESTAMP
          ) ON s.taxon_ref = tax.id
      GROUP BY s.collection_ref
    ) AS new_species
      ON users_stats."Collection ID" = new_species.collection_ref
)
SELECT DISTINCT
  us."Collection Path",
  coalesce (
      (
        SELECT DISTINCT "Action Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'insert'
      ),
      0
  ) as "Insertion count",
  coalesce(
      (
        SELECT DISTINCT "Action Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'update'
      ),
      0
  ) as "Update count",
  coalesce(
      (
        SELECT DISTINCT "Type Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'insert'
              AND sus."Type" = 'type'
      ),
      0
  ) as "Inserted Type count",
  coalesce(
      (
        SELECT DISTINCT "Type Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'update'
              AND sus."Type" = 'type'
      ),
      0
  ) as "Updated Type count",
  coalesce(us."New species", 0) as "New species"
FROM users_statistics as us
ORDER BY us."Collection Path"
$$;

create or replace function stats_collections_encoding_optimistics (collections.id%TYPE, text, text) returns setof stats_collections language sql immutable as $$
select * from stats_collections_encoding_optimistics($1, $2::timestamp, $3::timestamp);
$$;

GRANT EXECUTE ON FUNCTION stats_collections_encoding_optimistics (collections.id%TYPE, timestamp, timestamp) to d2viewer;
GRANT EXECUTE ON FUNCTION stats_collections_encoding_optimistics (collections.id%TYPE, text, text) to d2viewer;

create or replace function stats_collections_encoding (collections.id%TYPE, timestamp, timestamp) returns setof stats_collections language sql immutable as $$
WITH users_statistics AS
(
  WITH users_stats AS
  (
      SELECT DISTINCT
        collection_ref AS "Collection ID",
        (
          SELECT
        '/'
        ||
        array_to_string(
            array_agg(
                sc.name),
            '/')
        ||
        '/'
        ||
        collection_name
          FROM
            collections AS sc
            INNER JOIN
            (SELECT
               unnest(
                   string_to_array(
                       trim(
                           collection_path,
                           '/'),
                       '/')) :: BIGINT AS id) AS scc
              ON
                sc.id = scc.id
        ) AS "Collection Path",
        main_ut.action AS "Action",
        CASE WHEN
          main_s.type
          =
          'specimen'
          THEN 'non type'
        ELSE 'type' END AS "Type",
        main_s.id
      FROM users_tracking AS main_ut
        INNER JOIN specimens AS main_s
          ON main_ut.record_id = main_s.id
             AND main_ut.modification_date_time BETWEEN $2 :: TIMESTAMP AND $3 :: TIMESTAMP
             AND main_ut.referenced_relation = 'specimens'
      WHERE
        CASE
        WHEN 0 != $1
          THEN
            collection_ref IN (SELECT id
                               FROM collections
                               WHERE id = $1 OR path LIKE '%/' || $1 || '/%')
        ELSE
          TRUE
        END
        AND main_ut.action != 'delete'
      ORDER BY "Collection Path", "Action", "Type"
  )
  SELECT DISTINCT
    users_stats."Collection Path",
    users_stats."Action",
    users_stats."Type",
    coalesce(count(*) over (partition by "Collection ID", "Action"),0) as "Action Count",
    coalesce(count(*) over (partition by "Collection ID", "Action", "Type"),0) as "Type Count",
    coalesce(new_species."New species",0) as "New species"
  FROM users_stats
    LEFT JOIN
    (
      SELECT
        s.collection_ref,
        count(DISTINCT tax.id) AS "New species"
      FROM
        (users_tracking AS ut INNER JOIN taxonomy AS tax
            ON ut.referenced_relation = 'taxonomy'
               AND ut.action = 'insert'
               AND ut.record_id = tax.id
               AND tax.level_ref > 47
               AND ut.modification_date_time BETWEEN $2 :: TIMESTAMP AND $3 :: TIMESTAMP
          ) INNER JOIN
        (specimens AS s INNER JOIN users_tracking AS ust
            ON ust.referenced_relation = 'specimens'
               AND ust.action = 'insert'
               AND ust.record_id = s.id
               AND ust.modification_date_time BETWEEN $2 :: TIMESTAMP AND $3 :: TIMESTAMP
          ) ON s.taxon_ref = tax.id
      GROUP BY s.collection_ref
    ) AS new_species
      ON users_stats."Collection ID" = new_species.collection_ref
)
SELECT DISTINCT
  us."Collection Path",
  coalesce (
      (
        SELECT DISTINCT "Action Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'insert'
      ),
      0
  ) as "Insertion count",
  coalesce(
      (
        SELECT DISTINCT "Action Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'update'
      ),
      0
  ) as "Update count",
  coalesce(
      (
        SELECT DISTINCT "Type Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'insert'
              AND sus."Type" = 'type'
      ),
      0
  ) as "Inserted Type count",
  coalesce(
      (
        SELECT DISTINCT "Type Count"
        FROM users_statistics as sus
        WHERE sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'update'
              AND sus."Type" = 'type'
      ),
      0
  ) as "Updated Type count",
  coalesce(us."New species", 0) as "New species"
FROM users_statistics as us
ORDER BY us."Collection Path"
$$;

create or replace function stats_collections_encoding(collections.id%TYPE, text, text) returns setof stats_collections language sql immutable as $$
select * from stats_collections_encoding($1, $2::timestamp, $3::timestamp);
$$;

GRANT EXECUTE ON FUNCTION stats_collections_encoding (collections.id%TYPE, text, text) to d2viewer;

CREATE OR REPLACE function stats_encoders_encoding_optimistics (top_collection collections.id%TYPE, users_array TEXT, from_date TIMESTAMP, to_date TIMESTAMP)
  RETURNS setof encoders_stats_collections
language SQL as
$$
WITH users_statistics AS
(
  WITH users_stats AS
  (
      SELECT DISTINCT
        users.id            AS "User ID",
        users.formated_name AS "User",
        collection_ref      AS "Collection ID",
        (
          SELECT
        '/'
        ||
        array_to_string(
            array_agg(
                sc.name),
            '/')
        ||
        '/'
        ||
        collection_name
          FROM
            collections AS sc
            INNER JOIN
            (SELECT
               unnest(
                   string_to_array(
                       trim(
                           collection_path,
                           '/'),
                       '/')) :: BIGINT AS id) AS scc
              ON
                sc.id = scc.id
        )                   AS "Collection Path",
        main_ut.action      AS "Action",
        CASE WHEN
          main_s.type
          =
          'specimen'
          THEN 'non type'
        ELSE 'type' END     AS "Type",
        count(*)
        OVER (
          PARTITION BY
            users.id,
            collection_ref,
            action
        )                   AS "Action Count",
        count(*)
        OVER (
          PARTITION BY
            users.id,
            collection_ref,
            action,
            CASE WHEN
              main_s.type = 'specimen'
              THEN 'non type'
            ELSE 'type' END
        )                   AS "Type Count"
      FROM users
        INNER JOIN users_tracking AS main_ut
          ON users.id = main_ut.user_ref
             AND main_ut.modification_date_time BETWEEN $3 :: TIMESTAMP AND $4 :: TIMESTAMP
             AND main_ut.referenced_relation = 'specimens'
        INNER JOIN specimens AS main_s
          ON main_ut.record_id = main_s.id
      WHERE CASE
            WHEN '0' = ( select unnest(string_to_array(trim($2,'[]'), ', ')) limit 1 )
              THEN
                TRUE
            ELSE
              users.id::text IN ( select unnest(string_to_array(trim($2,'[]'), ', ')) )
            END
            AND
            CASE
            WHEN 0 != $1
              THEN
                collection_ref IN (SELECT id
                                   FROM collections
                                   WHERE id = $1 OR path LIKE '%/' || $1 || '/%')
            ELSE
              TRUE
            END
            AND main_ut.action != 'delete'
      ORDER BY "User", "Collection Path", "Action", "Type"
  )
  SELECT
    users_stats."User",
    users_stats."Collection Path",
    users_stats."Action",
    users_stats."Type",
    users_stats."Action Count",
    users_stats."Type Count",
    new_species."New species encoded by the encoder used in this collection"
  FROM users_stats
    LEFT JOIN
    (
      SELECT
        s.collection_ref,
        ut.user_ref            AS "User ID",
        count(DISTINCT tax.id) AS "New species encoded by the encoder used in this collection"
      FROM
        (users_tracking AS ut INNER JOIN taxonomy AS tax
            ON ut.referenced_relation = 'taxonomy'
               AND ut.action = 'insert'
               AND ut.record_id = tax.id
               AND tax.level_ref > 47
               AND ut.modification_date_time BETWEEN $3 :: TIMESTAMP AND $4 :: TIMESTAMP
          ) INNER JOIN
        (specimens AS s INNER JOIN users_tracking AS ust
            ON ust.referenced_relation = 'specimens'
               AND ust.action = 'insert'
               AND ust.record_id = s.id
               AND ust.modification_date_time BETWEEN $3 :: TIMESTAMP AND $4 :: TIMESTAMP
          ) ON s.taxon_ref = tax.id
      GROUP BY s.collection_ref, ut.user_ref
    ) AS new_species
      ON users_stats."Collection ID" = new_species.collection_ref
         AND users_stats."User ID" = new_species."User ID"
)
SELECT DISTINCT
  us."User", us."Collection Path",
  coalesce (
      (
        SELECT DISTINCT "Action Count"
        FROM users_statistics as sus
        WHERE sus."User" = us."User"
              AND sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'insert'
      ),0
  ) as "Insertion count",
  coalesce(
      (
        SELECT DISTINCT "Action Count"
        FROM users_statistics as sus
        WHERE sus."User" = us."User"
              AND sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'update'
      ),0
  ) as "Update count",
  coalesce (
      (
        SELECT DISTINCT "Type Count"
        FROM users_statistics as sus
        WHERE sus."User" = us."User"
              AND sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'insert'
              AND sus."Type" = 'type'
      ),0
  ) as "Inserted Type count",
  coalesce(
      (
        SELECT DISTINCT "Type Count"
        FROM users_statistics as sus
        WHERE sus."User" = us."User"
              AND sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'update'
              AND sus."Type" = 'type'
      ),0
  ) as "Update Type count",
  coalesce(us."New species encoded by the encoder used in this collection",0) as "New species encoded by the encoder used in this collection"
FROM users_statistics as us
ORDER BY us."User", us."Collection Path"
$$;

CREATE OR REPLACE function stats_encoders_encoding_optimistics (top_collection collections.id%TYPE, users_array TEXT, from_date TEXT, to_date TEXT)
  RETURNS setof encoders_stats_collections
language SQL as
$$
SELECT * FROM stats_encoders_encoding_optimistics ($1, $2, $3::timestamp, $4::timestamp);
$$;

CREATE OR REPLACE function stats_encoders_encoding (top_collection collections.id%TYPE, users_array TEXT, from_date TIMESTAMP, to_date TIMESTAMP)
  RETURNS setof encoders_stats_collections
language SQL as
$$
WITH users_statistics AS
(
  WITH users_stats AS
  (
      SELECT DISTINCT
        users.id            AS "User ID",
        users.formated_name AS "User",
        collection_ref      AS "Collection ID",
        (
          SELECT
        '/'
        ||
        array_to_string(
            array_agg(
                sc.name),
            '/')
        ||
        '/'
        ||
        collection_name
          FROM
            collections AS sc
            INNER JOIN
            (SELECT
               unnest(
                   string_to_array(
                       trim(
                           collection_path,
                           '/'),
                       '/')) :: BIGINT AS id) AS scc
              ON
                sc.id = scc.id
        )                   AS "Collection Path",
        main_ut.action      AS "Action",
        CASE WHEN
          main_s.type
          =
          'specimen'
          THEN 'non type'
        ELSE 'type' END     AS "Type",
        main_s.id
      FROM users
        INNER JOIN users_tracking AS main_ut
          ON users.id = main_ut.user_ref
             AND main_ut.modification_date_time BETWEEN $3 :: TIMESTAMP AND $4 :: TIMESTAMP
             AND main_ut.referenced_relation = 'specimens'
        INNER JOIN specimens AS main_s
          ON main_ut.record_id = main_s.id
      WHERE CASE
            WHEN '0' = ( select unnest(string_to_array(trim($2,'[]'), ', ')) limit 1 )
              THEN
                TRUE
            ELSE
              users.id::text IN ( select unnest(string_to_array(trim($2,'[]'), ', ')) )
            END
            AND
            CASE
            WHEN 0 != $1
              THEN
                collection_ref IN (SELECT id
                                   FROM collections
                                   WHERE id = $1 OR path LIKE '%/' || $1 || '/%')
            ELSE
              TRUE
            END
            AND main_ut.action != 'delete'
      ORDER BY "User", "Collection Path", "Action", "Type"
  )
  SELECT DISTINCT
    users_stats."User",
    users_stats."Collection Path",
    users_stats."Action",
    users_stats."Type",
    coalesce(count(*) over (partition by users_stats."User ID", "Collection ID", "Action"),0) as "Action Count",
    coalesce(count(*) over (partition by users_stats."User ID", "Collection ID", "Action", "Type"),0) as "Type Count",
    coalesce(new_species."New species encoded by the encoder used in this collection",0) as "New species encoded by the encoder used in this collection"
  FROM users_stats
    LEFT JOIN
    (
      SELECT
        s.collection_ref,
        ut.user_ref            AS "User ID",
        count(DISTINCT tax.id) AS "New species encoded by the encoder used in this collection"
      FROM
        (users_tracking AS ut INNER JOIN taxonomy AS tax
            ON ut.referenced_relation = 'taxonomy'
               AND ut.action = 'insert'
               AND ut.record_id = tax.id
               AND tax.level_ref > 47
               AND ut.modification_date_time BETWEEN $3 :: TIMESTAMP AND $4 :: TIMESTAMP
          ) INNER JOIN
        (specimens AS s INNER JOIN users_tracking AS ust
            ON ust.referenced_relation = 'specimens'
               AND ust.action = 'insert'
               AND ust.record_id = s.id
               AND ust.modification_date_time BETWEEN $3 :: TIMESTAMP AND $4 :: TIMESTAMP
          ) ON s.taxon_ref = tax.id
      GROUP BY s.collection_ref, ut.user_ref
    ) AS new_species
      ON users_stats."Collection ID" = new_species.collection_ref
         AND users_stats."User ID" = new_species."User ID"
)
SELECT DISTINCT
  us."User", us."Collection Path",
  coalesce (
      (
        SELECT DISTINCT "Action Count"
        FROM users_statistics as sus
        WHERE sus."User" = us."User"
              AND sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'insert'
      ),0
  ) as "Insertion count",
  coalesce (
      (
        SELECT DISTINCT "Action Count"
        FROM users_statistics as sus
        WHERE sus."User" = us."User"
              AND sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'update'
      ),0
  ) as "Update count",
  coalesce (
      (
        SELECT DISTINCT "Type Count"
        FROM users_statistics as sus
        WHERE sus."User" = us."User"
              AND sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'insert'
              AND sus."Type" = 'type'
      ),0
  ) as "Inserted Type count",
  coalesce (
      (
        SELECT DISTINCT "Type Count"
        FROM users_statistics as sus
        WHERE sus."User" = us."User"
              AND sus."Collection Path" = us."Collection Path"
              AND sus."Action" = 'update'
              AND sus."Type" = 'type'
      ),0
  ) as "Update Type count",
  coalesce(us."New species encoded by the encoder used in this collection",0) as "New species encoded by the encoder used in this collection"
FROM users_statistics as us
ORDER BY us."User", us."Collection Path"
$$;

CREATE OR REPLACE function stats_encoders_encoding (top_collection collections.id%TYPE, users_array TEXT, from_date TEXT, to_date TEXT)
  RETURNS setof encoders_stats_collections
language SQL as
$$
SELECT * FROM stats_encoders_encoding ($1, $2, $3::timestamp, $4::timestamp);
$$;

GRANT EXECUTE ON FUNCTION stats_encoders_encoding (collections.id%TYPE, TEXT, TEXT, TEXT) to d2viewer;
GRANT EXECUTE ON FUNCTION stats_encoders_encoding (collections.id%TYPE, TEXT, TIMESTAMP, TIMESTAMP) to d2viewer;
GRANT EXECUTE ON FUNCTION stats_encoders_encoding_optimistics (collections.id%TYPE, TEXT, TEXT, TEXT) to d2viewer;
GRANT EXECUTE ON FUNCTION stats_encoders_encoding_optimistics (collections.id%TYPE, TEXT, TIMESTAMP, TIMESTAMP) to d2viewer;

COMMENT ON FUNCTION stats_collections_encoding (collections.id%TYPE, timestamp, timestamp) IS 'Gives, by collections, from a top collection given, the encoding stats';
COMMENT ON FUNCTION stats_collections_encoding (collections.id%TYPE, text, text) IS 'Gives, by collections, from a top collection given, the encoding stats';
COMMENT ON FUNCTION stats_collections_encoding_optimistics (collections.id%TYPE, timestamp, timestamp) IS 'Gives, by collections, from a top collection given, the encoding stats - this one counts all updates that occured even if several times for the same specimen';
COMMENT ON FUNCTION stats_collections_encoding_optimistics (collections.id%TYPE, text, text) IS 'Gives, by collections, from a top collection given, the encoding stats - this one counts all updates that occured even if several times for the same specimen';
COMMENT ON FUNCTION stats_encoders_encoding (collections.id%TYPE, TEXT, timestamp, timestamp) IS 'Gives, by collections (from a top collection given) and by users (from an array of users id passed), the encoding stats';
COMMENT ON FUNCTION stats_encoders_encoding (collections.id%TYPE, TEXT, text, text) IS 'Gives, by collections (from a top collection given) and by users (from an array of users id passed), the encoding stats';
COMMENT ON FUNCTION stats_encoders_encoding_optimistics (collections.id%TYPE, TEXT, timestamp, timestamp) IS 'Gives, by collections (from a top collection given) and by users (from an array of users id passed), the encoding stats - this one counts all updates that occured even if several times for the same specimen';
COMMENT ON FUNCTION stats_encoders_encoding_optimistics (collections.id%TYPE, TEXT, text, text) IS 'Gives, by collections (from a top collection given) and by users (from an array of users id passed), the encoding stats - this one counts all updates that occured even if several times for the same specimen';

COMMIT ;