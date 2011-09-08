TESTS = $(wildcard tests/*.sql)
HOST = 127.0.0.1
DB_USER = darwin2
DB_WEB_USER = cebmpad
DB_READ_USER = d2viewer
DB_NAME = darwin2
TEST_CMD = pg_prove
PSQL = /usr/bin/psql
PARAM_PSQL = $(PSQL) -q -h $(HOST) -U $(DB_USER) -d $(DB_NAME)
PRIV_PSQL = $(PSQL) -q -v dbname=$(DB_NAME) -v importuser=$(DB_IMPORT_USER)
PRIV_PSQL_DB = $(PRIV_PSQL) -d $(DB_NAME)
PG_VER = 8.4

PG_PROVE = /usr/local/bin/pg_prove
#CLUSTER_NAME = darwin
#CLUSTER_PATH = /var/lib/postgresql/$(PG_VER)/$(CLUSTER_NAME)
CLUSTER_PATH = $(shell pg_lsclusters -h | awk '{print $$6}' )
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
	@echo "create-test-schema"
	@echo "    Create the schema for the unit tests"
	@echo ""
	@echo "test"
	@echo "   Unit test the database installation in a schema 'unittest'"
	@echo ""
	@echo "create-schema"
	@echo "   Create the schema for the install of the db "
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
	@echo "DB_NAME (Default: darwin2)"
	@echo "   database name to be created or to be used (depends of the target)"
	@echo ""
	@echo "PORT (Default: 5432)"
	@echo "   Port for the connection to the database"
	@echo ""
	@echo "CLUSTER_PATH (Default: first line  returned by pg_lsclusters. [/var/lib/postgresql/8.4/main] on debian)"
	@echo "   The path where to create the tablespace"
	@echo ""
	@echo "Other advanced parameters are available in the makefile"
	@echo ""

test:
ifeq ($(TEST_CMD),pg_prove)
	$(PG_PROVE) -h $(HOST) -U $(DB_USER) $(TESTS)
else ifeq ($(TEST_CMD),psql)
	for file in $(TESTS) ; \
	do \
		$(PSQL) -h $(HOST) -U $(DB_USER) -f $$file ; \
	done
endif

install-db:
	$(PARAM_PSQL) -f createdomains.sql
	$(PARAM_PSQL) -f createtypes.sql
	$(PARAM_PSQL) -f createtables.sql
	$(PARAM_PSQL) -f initiate_data.sql
	$(PARAM_PSQL) -f createfunctions.sql
	$(PARAM_PSQL) -f createtriggers.sql
	$(PARAM_PSQL) -f addchecks.sql
	$(PARAM_PSQL) -f createindexes.sql
	$(PARAM_PSQL) -f grant_d2_to_read_user.sql

install-all: create-db create-user create-schema install-lib install-db

uninstall-db:
	$(PARAM_PSQL) -f droptriggers.sql
	$(PARAM_PSQL) -f dropfunctions.sql
	$(PARAM_PSQL) -f dropindexes.sql
	$(PARAM_PSQL) -f droptables.sql
	$(PARAM_PSQL) -f droptypes.sql
	$(PARAM_PSQL) -f dropdomains.sql

create-schema:
	$(PRIV_PSQL_DB) -f createschema.sql

create-test-schema:
	$(PRIV_PSQL_DB) -f create_testschema.sql

grant-d2-to-web:
	$(PRIV_PSQL_DB) -f grant_d2_to_web_user.sql

grant-d2-to-read-user:
	$(PRIV_PSQL_DB) -f grant_d2_to_read_user.sql

create-db:
	-createlang -d template1 plpgsql
	$(PRIV_PSQL) -f createdb.sql

install-lib:
	$(PRIV_PSQL_DB) -f lib/accent.sql
	#Make this more dynamical
	$(PRIV_PSQL_DB) -f /usr/share/postgresql/$(PG_VER)/contrib/pg_trgm.sql
	$(PRIV_PSQL_DB) -f /usr/share/postgresql/$(PG_VER)/contrib/pgcrypto.sql
	#Test version and launch hstore scripts from adapted place
	if [ "$(PG_VER)" = "9.0" ] ; then \
		$(PRIV_PSQL_DB) -f /usr/share/postgresql/$(PG_VER)/contrib/hstore.sql ; \
	else \
		$(PRIV_PSQL_DB) -f /usr/share/postgresql/$(PG_VER)/contrib/hstore-new.sql ; \
	fi

	if [ -f /usr/share/postgresql/$(PG_VER)/contrib/postgis.sql ] ; then \
		$(PRIV_PSQL_DB) -f /usr/share/postgresql/$(PG_VER)/contrib/postgis.sql; \
	else \
		$(PRIV_PSQL_DB) -f /usr/share/postgresql/$(PG_VER)/contrib/postgis-1.5/postgis.sql; \
	fi

	if [ -f /usr/share/postgresql/$(PG_VER)/contrib/spatial_ref_sys.sql ] ; then \
		$(PRIV_PSQL_DB) -f /usr/share/postgresql/$(PG_VER)/contrib/spatial_ref_sys.sql; \
	else \
		$(PRIV_PSQL_DB) -f /usr/share/postgresql/$(PG_VER)/contrib/postgis-1.5/spatial_ref_sys.sql; \
	fi

	$(PRIV_PSQL_DB) -f postgisgrant.sql -v dbuser=$(DB_USER)

create-user-web:
	-createuser -l -i -S -R -D -P -E $(DB_WEB_USER)

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

drop-db:
	$(PRIV_PSQL_DB) -f dropdb.sql

love:
	@echo "Send Your Thanks to : collections@naturalsciences.be"
	@echo "And chocolates or postcards to : "
	@echo "   DBTeam - Service ICT"
	@echo "   29 Rue Vautier,"
	@echo "   1000 Brussels Belgium"
