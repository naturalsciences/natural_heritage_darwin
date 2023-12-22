#!/usr/bin/env bash



_nowT1=$(date +"%m_%d_%Y %T")
echo "Begin restore to public $_nowT1"

_confirm_file="/home/darwin_replication/darwin_public_rep/sync_folder/dw_rep_confirm.txt"
_last_file="/home/darwin_replication/darwin_public_rep/sync_folder/dw_rep_last.txt"
_err_file="/home/darwin_replication/darwin_public_rep/sync_folder/dw_rep_err.txt"
touch -a $_last_file

_current_file=$(head -n 1 $_confirm_file)
_last_file=$(head -n 1 $_last_file)

if [ $_current_file != $_last_file ]; then
	echo "Emptying tables"
	export PGPASSWORD='PWD'
	psql -h localhost -U darwin2 -a -d darwin2_public --command="SELECT darwin2.fct_rmca_flush_tables()"
	_nowT2=$(date +"%m_%d_%Y %T")
	echo "Done$_nowT2"
	psql -h localhost -U darwin2 -b -q -f $_current_file -o $_err_file -d darwin2_public  
	_nowT3=$(date +"%m_%d_%Y %T")
	echo "Done$_nowT3"
	psql -h localhost -U darwin2 -a -d darwin2_public --command="SELECT darwin2.fct_rmca_refresh_materialized_view_and_consult_tables_after_rep()"
	_nowT4=$(date +"%m_%d_%Y %T")
	echo "$_current_file" > "$_last_file"
	echo "Replication done$_nowT4"
	find /home/darwin_replication/darwin_public_rep -name "*.sql" -mtime "+$(( DAYSOLD - 2 ))" -type f -delete
        echo "flushing old files"
	
fi
