#!/bin/bash
BACKDIR="$(pwd)"


d=`date +%Y-%d-%m`
OUTPUT_FILE=$BACKDIR/darwin_schema_$d.sql
echo $OUTPUT_FILE
pg_dump --schema-only --format=p --host=salmoneus --file=$OUTPUT_FILE darwin2
echo "done"