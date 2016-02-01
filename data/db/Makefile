TESTS = $(wildcard tests/*.sql)
HOST = 127.0.0.1
PORT = 5432
DB_USER = darwin2
DB_WEB_USER = cebmpad
DB_READ_USER = d2viewer
DB_TEST_USER = unittest
DB_TEST_SCHEMA = unittest
DB_NAME = darwin2
TEST_CMD = pg_prove
PSQL = /usr/bin/psql
PARAM_PSQL = $(PSQL) -q -h $(HOST) -U $(DB_USER) -d $(DB_NAME) -p $(PORT)
TEST_PSQL = $(PSQL) -q -h $(HOST) -U $(DB_TEST_USER) -d $(DB_NAME) -p $(PORT)
PRIV_PSQL = $(PSQL) -q -v dbname=$(DB_NAME) -p $(PORT)
PRIV_PSQL_DB = $(PRIV_PSQL) -d $(DB_NAME)
PG_VER = 9.1

PG_PROVE = /usr/bin/pg_prove
PASSWD = ''

help: help-target help-param
help-target:
	@echo ""
	@echo "Darwin 2 Install Script :"
	@echo "========================="
	@echo ""
	@echo "Possible targets :"
	@echo "------------------"
	@echo ""
	@echo "help"
	@echo "   Display this help message"
	@echo ""
	@echo "install-all"
	@echo "   install all the database.(must be run in privileged account) Execute targets  create-db, create-user, create-schema, install-lib, install-db"
	@echo ""
	@echo "install-db"
	@echo "   install the darwin db into the $db_user schema : create types, tables, functions and indexes"
	@echo ""
	@echo "install-test"
	@echo "   install structure for unit testing"
	@echo ""
	@echo "test"
	@echo "   Unit test the database installation in a schema 'unittest'"
	@echo ""
	@echo "create-schema"
	@echo "   Create the schema for the install of the db "
	@echo ""
	@echo "create-test-schema"
	@echo "   Create the schema for the unit testing "
	@echo ""
	@echo "create-db"
	@echo "   create an new postgresql db and the tablespace associated for darwin"
	@echo ""
	@echo "install-lib"
	@echo "   install library used by darwin"
	@echo ""
	@echo "create-user"
	@echo "   create a default user to access only the darwin schema and db"
	@echo ""
	@echo "create-test-user"
	@echo "   create a default user for unit testing (user unittest)"
	@echo ""
	@echo "upgrade"
	@echo "   tries to update the current db to the new version"
	@echo ""
	@echo "uninstall-db"
	@echo "   remove the tables,function... from darwin. WARNING! This action can remove ALL your data"
	@echo ""
	@echo "drop-db"
	@echo "   remove the darwin database. WARNING! This action can remove ALL your data"
	@echo ""
	@echo ""
	@echo "Targets 'install-all', 'create-db', 'create-schema' and 'create-user' must be run into a privileged account (often 'postgres')"
	@echo "other targets can be run into the account created by 'create-user' "
help-param:
	@echo ""
	@echo "Env Parameters :"
	@echo "---------------"
	@echo ""
	@echo "HOST (Default: 127.0.0.1)"
	@echo "   host for the connection to the database"
	@echo ""
	@echo "DB_USER (Default: darwin2)"
	@echo "   username for the connection to the database"
	@echo ""
	@echo "DB_READ_USER (Default: d2viewer)"
	@echo "   username for a read only connection to the database"
	@echo ""
	@echo "DB_TEST_USER (Default: unittest)"
	@echo "   username for the connection to the unitest testing schema"
	@echo ""
	@echo "DB_TEST_SCHEMA (Default: unittest)"
	@echo "   testing schema name for the unitest user"
	@echo ""
	@echo "DB_NAME (Default: darwin2)"
	@echo "   database name to be created or to be used (depends of the target)"
	@echo ""
	@echo "PORT (Default: 5432)"
	@echo "   Port for the connection to the database"
	@echo ""
	@echo "PG_VER (Default: 9.1)"
	@echo "   Version of the postgresql database"
	@echo ""
	@echo "Other advanced parameters are available in the makefile"
	@echo ""

test:
ifeq ($(TEST_CMD),pg_prove)
	$(PG_PROVE) -h $(HOST) -d $(DB_NAME) -U $(DB_TEST_USER) $(TESTS)
else ifeq ($(TEST_CMD),psql)
	for file in $(TESTS) ; \
	do \
		$(PSQL) -h $(HOST) -d $(DB_NAME) -U $(DB_TEST_USER) -f $$file ; \
	done
endif

install-db:
	$(PARAM_PSQL) -f createtables.sql
	$(PARAM_PSQL) -f initiate_data.sql
	$(PARAM_PSQL) -f createfunctions.sql
	$(PARAM_PSQL) -f createtriggers.sql
	$(PARAM_PSQL) -f addchecks.sql
	$(PARAM_PSQL) -f createindexes.sql
	$(PARAM_PSQL) -f createindexes_darwinflat.sql
	$(PRIV_PSQL_DB) -f grant_d2_to_read_user.sql

install-all: create-db create-user create-schema install-lib install-db

install-test: create-test-user create-test-schema

uninstall-db:
	$(PARAM_PSQL) -f droptriggers.sql
	$(PARAM_PSQL) -f dropfunctions.sql
	$(PARAM_PSQL) -f dropindexes.sql
	$(PARAM_PSQL) -f droptables.sql

create-schema:
	$(PRIV_PSQL_DB) -c "create schema $(DB_NAME) authorization $(DB_USER);"
	$(PRIV_PSQL_DB) -c "ALTER USER $(DB_USER) SET search_path TO $(DB_NAME), public;"

create-test-schema:
	$(PRIV_PSQL_DB) -c "create schema $(DB_TEST_SCHEMA) authorization $(DB_TEST_USER);"
	$(PRIV_PSQL_DB) -c "ALTER USER $(DB_TEST_USER) SET search_path TO $(DB_TEST_SCHEMA), public;"
	$(PRIV_PSQL_DB) -c "create extension pgtap;"

grant-d2-to-web:
	$(PRIV_PSQL_DB) -f grant_d2_to_web_user.sql

grant-d2-to-read-user:
	$(PRIV_PSQL_DB) -f grant_d2_to_read_user.sql

create-db:
	$(PRIV_PSQL) -c "create database $(DB_NAME) ENCODING 'UNICODE';"

install-lib:
ifeq ('$(PG_VER)','9.1')
	$(PRIV_PSQL_DB) -c "create extension pgcrypto; create extension pg_trgm; create extension hstore;"
	$(PRIV_PSQL_DB) -f /usr/share/postgresql/$(PG_VER)/contrib/postgis-1.5/postgis.sql;
	$(PRIV_PSQL_DB) -f /usr/share/postgresql/$(PG_VER)/contrib/postgis-1.5/spatial_ref_sys.sql;
endif
	$(PRIV_PSQL_DB) -f postgisgrant.sql -v dbuser=$(DB_USER)

create-user-web:
	-createuser -l -i -S -R -D -P -E $(DB_WEB_USER)

create-test-user:
	-createuser -l -i -S -R -D -P -E $(DB_TEST_USER)

create-user:
ifeq ($(PASSWD),'')
	-createuser -l -i -S -R -D -P -E $(DB_USER)
	-createuser -l -i -S -R -D -P -E $(DB_WEB_USER)
	-createuser -l -i -S -R -D -P -E $(DB_READ_USER)
else
	$(PRIV_PSQL_DB) -c "CREATE ROLE $(DB_USER) ENCRYPTED PASSWORD '$(PASSWD)' NOSUPERUSER NOCREATEDB NOCREATEROLE INHERIT LOGIN;"
	$(PRIV_PSQL_DB) -c "CREATE ROLE $(DB_WEB_USER) ENCRYPTED PASSWORD '$(PASSWD)' NOSUPERUSER NOCREATEDB NOCREATEROLE INHERIT LOGIN;"
	$(PRIV_PSQL_DB) -c "CREATE ROLE $(DB_READ_USER) ENCRYPTED PASSWORD '$(PASSWD)' NOSUPERUSER NOCREATEDB NOCREATEROLE INHERIT LOGIN;"
endif

upgrade: 
	@for n in `ls changes/*.sql | sort -n | grep -A 100000 \`$(PRIV_PSQL_DB) -c "select id from darwin2.db_version order by update_at DESC LIMIT 1;" -t -A\` | tail -n +2`; do\
		echo "Upgrade to $$n"; \
		$(PRIV_PSQL_DB) -f $$n; \
		$(PRIV_PSQL_DB) -c "update darwin2.db_version set id=`echo $$n| sed 's/-.*//' | xargs  basename` , update_at=now();"; \
	done
	
drop-db:
	$(PRIV_PSQL_DB) -f dropdb.sql

love:
	@echo "Send Your Thanks to : darwin-ict@naturalsciences.be"
	@echo "And chocolates or postcards to : "
	@echo "   DBTeam - Service ICT"
	@echo "   29 Rue Vautier,"
	@echo "   1000 Brussels Belgium"

