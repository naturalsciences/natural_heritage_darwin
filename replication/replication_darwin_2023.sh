#!/usr/bin/env bash



_nowT1=$(date +"%m_%d_%Y %T")
echo "Begin migration to public $_nowT1"

echo "Refreshing public views"
export PGPASSWORD='phvisodu$ft'
psql -h localhost -U darwin2 -a -d darwin2_public --command="SELECT darwin2.fct_rmca_refresh_materialized_view_and_consult_tables()"

_nowT2=$(date +"%m_%d_%Y %T")
echo "View source server refreshed $_nowT2"

umount /mnt/darwin_public_replication
sshfs -o allow_other,nonempty,uid=1001,gid=1001,password_stdin USER@IP:/home/darwin_replication/darwin_public_rep/ /mnt/darwin_public_replication/ <<< 'PWD'


echo "Sending_backup"

_nowfile=$(date +"%m_%d_%Y")
_file_backup="/mnt/darwin_public_replication/dw_rep_$_nowfile.sql"
_file_backup_target="/home/darwin_replication/darwin_public_rep/dw_rep_$_nowfile.sql"
_confirm_file="/mnt/darwin_public_replication/sync_folder/dw_rep_confirm.txt"



umount /mnt/darwin_public_replication
sshfs -o allow_other,nonempty,uid=1001,gid=1001,password_stdin USER@IP:/home/darwin_replication/darwin_public_rep/ /mnt/darwin_public_replication/ <<< 'PWD'

export PGPASSWORD='PWD'
pg_dump --user=postgres -h localhost  --data-only --format=p --column-inserts  --schema=darwin2  darwin2_public > $_file_backup


echo "$_file_backup_target" > "$_confirm_file"

_nowT3=$(date +"%m_%d_%Y %T")
echo "Backup_made $_nowT3"


umount /mnt/darwin_public_replication
