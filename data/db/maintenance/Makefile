DUMP_FILE = dbs.out
RESET_SCRIPT = demo_prepare.sql
PORT = 5432
HOST = 127.0.0.1
DB_USER = cebmpad
DB_NAME = darwin2
PSQL = /usr/bin/psql
PHP = /usr/bin/php
SYMFONY = /var/www/darwin2/web/symfony
DEMO_PSQL = $(PSQL) -q -p $(PORT)
RESET_DEMO_PSQL = $(DEMO_PSQL) -h $(HOST) -U $(DB_USER) -d $(DB_NAME)
PG_VER = 8.4
CLUSTER_PATH = $(shell pg_lsclusters -h | awk '{print $$6}' )

help: help-target help-param
help-target:
	@echo ""
	@echo "Darwin 2 Demo Reset Script :"
	@echo "========================="
	@echo ""
	@echo "Possible targets :"
	@echo "------------------"
	@echo ""
	@echo "help"
	@echo "   Display this help message"
	@echo ""
	@echo "reset-all"
	@echo "   install all the database.(must be run in privileged account) Execute targets  create-db, create-user, create-schema, install-lib, install-db"
	@echo ""
	@echo "reset-db"
	@echo "   install the darwin db into the $db_user schema : create types, tables, functions and indexes"
	@echo ""
	@echo ""
	@echo "Targets 'reset-all' and 'reset-db' must be run into a privileged account (often 'postgres')"
	@echo ""
help-param:
	@echo ""
	@echo "Env Parameters :"
	@echo "---------------"
	@echo ""
	@echo "HOST (Default: 127.0.0.1)"
	@echo "   host for the connection to the database"
	@echo ""
	@echo "DB_USER (Default: cebmpad)"
	@echo "   username for the connection to the database"
	@echo ""
	@echo "DB_NAME (Default: darwin2)"
	@echo "   database name to be created or to be used (depends of the target)"
	@echo ""
	@echo "PORT (Default: 5432)"
	@echo "   Port for the connection to the database"
	@echo ""
	@echo "DUMP_FILE (Default: dbs.out)"
	@echo "   Name of the dump file to be used for total db reset"
	@echo ""
	@echo "RESET_SCRIPT (Default: demo_prepare.sql)"
	@echo "   Name of the SQL script to be used for db reset with initial demo users"
	@echo ""
	@echo "CLUSTER_PATH (Default: first line  returned by pg_lsclusters. [/var/lib/postgresql/8.4/main] on debian)"
	@echo "   The path where to create the tablespace"
	@echo ""
	@echo "Other advanced parameters are available in the makefile"
	@echo ""
reset-db:
	$(RESET_DEMO_PSQL) -f $(RESET_SCRIPT)
	$(PHP) $(SYMFONY) darwin:add-widgets --env="dev" all

reset-all:
	$(DEMO_PSQL) -f $(DUMP_FILE)
	$(RESET_DEMO_PSQL) -f $(RESET_SCRIPT)
	$(PHP) $(SYMFONY) darwin:add-widgets --env="dev" all
