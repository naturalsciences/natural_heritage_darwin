#!/bin/bash

pg_version="9.1"
dbname="darwin2"
dbport="5432"
hostname="127.0.0.1"
schema="darwin2"
testschema="unittest"
testuser="unittest"
unifiedpasswd=""
darwin_version=`ls changes/*.sql | sort -nr | head -n1 | sed 's/-.*//' | xargs  basename`


function title() {
  echo -e "\n \033[1m${@}\033[0m :"
}
function command_name() {
  echo -e "\n \033[1m\t${@}\033[0m"
}
function command_desc() {
  echo -e "\t   ${@}"
}
function option_desc() {
  echo -e "\n\t${@}"
}
function error_msg() {
  echo -e "\033[1;31m${@}\033[0;0m"
}
function warn_msg() {
  echo -e "\033[1;33m${@}\033[0;0m"
}
function info() {
  echo -e "${@}"
}

usage(){
  title "$(basename $0) allow you to install DARWIN Database"
  option_desc "Usage : install_db [options] [action]"

  title "Available [actions]"
  command_name "help"
  command_desc "Display this help message"

  command_name "install-all"
  command_desc "install all the database.(must be run in privileged account) Execute targets  create-db, create-user, create-schema, install-lib, install-db"

  command_name "install-db"
  command_desc "install the darwin db into the \$db_user schema : create types, tables, functions and indexes"

  command_name "install-test"
  command_desc "install the darwin db into the 'unittest' schema"

  command_name "test"
  command_desc "Unit test the database installation in a schema 'unittest'"

  command_name "create-schema"
  command_desc "Create the schema for the install of the db "

  command_name "create-db"
  command_desc "create an new postgresql db and the tablespace associated for darwin"

  command_name "install-lib"
  command_desc "install library used by darwin"

  command_name "create-user"
  command_desc "create a default user to access only the darwin schema and db"

  command_name "upgrade"
  command_desc "tries to update the current db to the new version"

  command_name "uninstall-db"
  command_desc "remove the tables,function... from darwin. WARNING! This action can remove ALL your data"

  command_name "drop-db"
  command_desc "remove the darwin database. WARNING! This action can remove ALL your data"

  command_name "test-adm"
  command_desc "Test your administrative connection. Open a command to your db"

  title "Available [option] :"

  option_desc "-h hostname (Default: $hostname)"
  command_desc "host for the connection to the database"

  option_desc "-d dbname (Default: $dbname)"
  command_desc "database name to be created or to be used (depends of the target)"

  option_desc "-p port (Default: $dbport)"
  command_desc "Port for the connection to the database"

  option_desc "-s schema (Default: $schema)"
  command_desc "schema used in the database"

  option_desc "-t schema (Default: $testschema)"
  command_desc "schema used in the database for unit testing"

  option_desc "-u user (Default: $testsuser)"
  command_desc "user used in the database for unit testing"

  option_desc "-V db version (Default: $pg_version)"
  command_desc "Version of the postgresql database"

  option_desc "-O unified_password"
  command_desc "Used if you want to set the password of darwin2, cebmpad and d2viewer to the same value (unified_password)"

  option_desc #emtpyline
  exit 1
}

[[ $# -eq 0 ]] && usage
while getopts ":O:h:p:d:V:s:t:u:" opt ; do
  case $opt in
    h)
      if [[ $OPTARG = -* ]]; then
        warn_msg "invalid argument for option -h, -h ignored"
        ((OPTIND--))
        continue
      fi
      hostname=$OPTARG
    ;;
    p)
      if [[ $OPTARG = -* ]]; then
        warn_msg "invalid argument for option -p, -p ignored"
        ((OPTIND--))
        continue
      fi
      dbport=$OPTARG
    ;;
    O)
      if [[ $OPTARG = -* ]]; then
        warn_msg "invalid argument for option -O, -O ignored"
        ((OPTIND--))
        continue
      fi
      unifiedpasswd="PASSWORD '$OPTARG'"
    ;;
    s)
      if [[ $OPTARG = -* ]]; then
        warn_msg "invalid argument for option -s, -s ignored"
        ((OPTIND--))
        continue
      fi
      schema=$OPTARG
    ;;
    t)
      if [[ $OPTARG = -* ]]; then
        warn_msg "invalid argument for option -t, -t ignored"
        ((OPTIND--))
        continue
      fi
      testschema=$OPTARG
    ;;
    u)
      if [[ $OPTARG = -* ]]; then
        warn_msg "invalid argument for option -u, -u ignored"
        ((OPTIND--))
        continue
      fi
      testuser=$OPTARG
    ;;
    d)
      if [[ $OPTARG = -* ]]; then
        warn_msg "invalid argument for option -d, -d ignored"
        ((OPTIND--))
        continue
      fi
      dbname=$OPTARG
    ;;
    V)
      if [[ $OPTARG = -* ]]; then
        warn_msg "invalid argument for option -V, -V ignored"
        ((OPTIND--))
        continue
      fi
      pg_version=$OPTARG
    ;;
    \?)
      error_msg "Invalid option -$OPTARG"
    ;;
  esac
done
shift $((OPTIND-1))
if [[ $# -gt 1 ]] ; then
  error_msg "Just one action is allowed"
  usage
  exit 1
fi

function install_db() {
  $psql -f createtables.sql
  echo -e '- Tables created'
  $psql -f initiate_data.sql
  echo -e '- Datas inserted'
  $psql -f createfunctions.sql
  echo -e '- Functions created'
  $psql -f createtriggers.sql
  echo -e '- Trigger created'
  $psql -f addchecks.sql
  $psql -f createindexes.sql
  echo -e '- Indexes created'
  $psql -f createindexes_darwinflat.sql
  $admpsql -f grant_d2_to_read_user.sql
  psql_exit_status=$?
  if [ $psql_exit_status != 0 ]; then
    warn_msg "Grant was not applied"
  else
    echo -e '- Grant done'
  fi
  $psql -c "INSERT into $schema.db_version VALUES($darwin_version::integer,now())"
  echo -e "- Db version set to $darwin_version"
}

function install_lib() {
  if [ "$pg_version"="9.1" ] ; then
    $admpsql  -c "create extension pgcrypto; create extension pg_trgm; create extension hstore;"
  fi
}

function add_user() {
  username=$1
  if [ "$unifiedpasswd" = "" ]; then
    read -s -p "password for $username :" password
    pwd_str="PASSWORD '$password'"
  else
    pwd_str=$unifiedpasswd
  fi
  echo -e "\n"
  $admpsql -c "CREATE ROLE $username $pwd_str NOSUPERUSER NOCREATEDB NOCREATEROLE INHERIT LOGIN;"
}

function install_role() {
  add_user "darwin2"
  add_user "cebmpad"
  add_user "d2viewer"
}

psql="/usr/bin/psql -q -h $hostname -U darwin2 -d $dbname -p $dbport"
tpsql="/usr/bin/psql -q -h $hostname -U $testuser -d $dbname -p $dbport"
basepsql="sudo -u postgres psql -p $dbport -v dbname=$dbname -v schema=$schema"
admpsql="$basepsql -q -d $dbname"
pg_prove="/usr/bin/pg_prove -h $hostname -U unittest -d $dbname -p $dbport"
case "$@" in
  "test-adm")
    echo "Trying to Connect to DB"
    $admpsql
  ;;
  "install-all")
    $basepsql -c "create database $dbname ENCODING 'UNICODE';"
    install_role
    $admpsql -c "create schema $schema authorization darwin2;"
    $admpsql  -c "ALTER USER darwin2 SET search_path TO $schema, public;"
    install_lib
    install_db
  ;;
  "install-db")
    install_db
  ;;
  "install-test")
    add_user "$testuser"
    $admpsql -c "create schema $testschema authorization $testuser;"
    $admpsql  -c "ALTER USER $testuser SET search_path TO $testschema, public;"
    $admpsql  -c "CREATE EXTENSION pgtap;"
  ;;
  "test-psql")
    for sqlfiles in $(ls tests/*.sql)
    do
      $tpsql -f $sqlfiles
    done
  ;;
  "test")
    $pg_prove $(ls tests/*.sql)
  ;;
  "create-schema")
    $admpsql -c "create schema $schema authorization darwin2;"
    $admpsql  -c "ALTER USER darwin2 SET search_path TO $schema, public;"
  ;;
  "create-db")
    $basepsql -c "create database $dbname ENCODING 'UNICODE';"
  ;;
  "install-lib")
    install_lib
  ;;
  "create-user")
    install_role
  ;;
  "upgrade")
    test=0
    dw_version=`$psql -c "select id from $schema.db_version order by update_at DESC LIMIT 1;" -t -A`

    if [ "$dw_version" = "" ]
    then
      error_msg "Problem fetching current version"
      exit 1;
    fi

    dw_version=$(( $dw_version + 1))
    if [ "$(echo $dw_version | grep "^[[:digit:]]*$")" ]
    then
      upd_file=$(ls changes/*.sql | sort -n | grep $dw_version)
      if [ "$upd_file" = '' ]
      then
        echo -e "\n\t- Everything is up to date -"
        exit 0;
      else
        while [ $upd_file ]
        do
          $admpsql --set ON_ERROR_STOP=on -f $upd_file
          psql_exit_status=$?
          if [ $psql_exit_status != 0 ]; then
            error_msg "Problem occurs durring upgrade. Last command was"
            echo $admpsql --set ON_ERROR_STOP=on -f $upd_file
            exit $psql_exit_status
          fi

          $admpsql -c "update $schema.db_version set id=$dw_version , update_at=now();"
          echo -e "- $upd_file processed, Darwin database version is now \033[1;32m$dw_version\033[0;0m"
          dw_version=$(( $dw_version + 1))
          upd_file=$(ls changes/*.sql | sort -n | grep $dw_version)
        done
      fi
    else
      echo "Db version not set"
      exit;
    fi
  ;;
  "uninstall-db")
    $psql -f droptriggers.sql
    $psql -f dropfunctions.sql
    $psql -f dropindexes.sql
    $psql -f droptables.sql
  ;;
  "drop-db")
    $basepsql -f dropdb.sql
  ;;
  "help")
    usage
  ;;
  *)
    error_msg "Unknow action $@"
    exit 1
  ;;
esac

echo -e "\nHave a nice day !\n"
