#
1 If your use GIT
------------------
create a local repository (optionnally placed directly in  the web folder) to get the source code
and then from within this folder :
 sudo git clone https://github.com/naturalsciences/natural_heritage_darwin

2 Requirements
---------------

Documentation is written for Linux systems using the apt package manager (Debian or Ubuntu).
I used Ubuntu 20

**Hardware requirements ;**
Min 8 Gigas of RAM (note : but default PostgreSQL can not more than 50% of the RAM).
Better to install the server and the PHP interface on two different machines if it  is possible.


3 Enable PostgreSQL repository
--------------------------------
**3.1 Create the file repository configuration:**

    sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list'

**3.2 Import the repository signing key:**

    wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -

**3.3 Update the package lists:**

    sudo apt-get update

4 Enable PostGIS repository
-------------------------------

See: https://wiki.ubuntu.com/UbuntuGIS

    sudo add-apt-repository ppa:ubuntugis/ppa
    sudo apt-get update

5 Install PostgreSQL 
--------------------
Default version (14 at the time of the writing of this documentation)

    sudo apt-get install postgresql

Specific version 

    sudo apt-get install postgresql-14

Install PostGIS

    sudo apt-get install postgresql-14-postgis-3

Optionnally, install Python3 module

    sudo apt-get install postgresql-contrib postgresql-plpython3-14 
(this will be bound to PL SQL function **darwin2.fct_rmca_py_webservice**)

6 Configure PostgreSQL
-----------------------
We recommend to install PgADMIN 6.4/+ on your own computer to administer PostgreSQL (connecting the server remotely)
https://www.pgadmin.org/download/

Note command to :
stop : `sudo systemctl stop postgresql`
start :  `sudo systemctl start postgresql`
restart :  `sudo systemctl restart postgresql`

Configuration is in :

    /etc/postgresql/{VERSION}/main/

e.g : 

    /etc/postgresql/14/main/

Edit  ***/etc/postgresql/{VERSION}/main/postgresql.conf***

    listen_addresses="*"

(this allows remote access, note that the firewall in pg_hba_conf must also be set afterwards)

    shared_buffers = 128MB 

=> can be replaced by roughly 25% of the RAM
e.g (on a 16 GB machine)

    shared_buffers = 4096MB

    work_mem=1024MB

(memory for sort) 
effective_cache_size in QUERY TUNING => can be 50% of the RAM

    effective_cache_size=4GB

See also (for memory settings) https://blog.crunchydata.com/blog/optimize-postgresql-server-performance#:~:text=begin%20with%20shared_buffers%20.-,shared_buffers,PostgreSQL%20will%20use%20for%20cache.

Edit postgresql firewall in ***/etc/postgresql/{VERSION}/main/pg_hba_conf***
Allow: 
	-the station that has pg admin
	-the server having the PHP interface (if different)
EG. 

    host    all             all             192.168.1.0/24          scram-sha-256

=>allows machine in the range 192.168.1.XX to connect to PostgreSQL
Note port 5432 (default) must also be open between machines in your organization firewall	

Assign a password for the postgresql account

    sudo -s -u postgres
    psql 
    ALTER USER postgres WITH password 'MY_PWD';
    #exit sql console
    \q
    #back to original user
    exit

7 INSTALL PHP 7.4 and apache2

-----------------------------
(package names may differ in upur distribution)

    sudo apt-get install php-7.4
    sudo apt-get install apache2
    sudo apt-get install libapache2-mod-php7.4 
    sudo apt-get install php-apcu
	sudo apt-get install php-apcu-bc
	sudo apt-get install php-ldap
	
    sudo a2enmod php7.4
     sudo systemctl restart apache2

 
Enable PHP dependencies
------------------------------
#postgresql driver

    sudo apt-get install php7.4-pgsql
    
    sudo apt-get install php7.4-xml 

#(to complete)



8 create database 
------------------
In GitHub consider the https://github.com/naturalsciences/natural_heritage_darwin/tree/Branch_DISTRI_2022 branch

https://github.com/naturalsciences/natural_heritage_darwin/tree/Branch_DISTRI_2022/db_schema

Go to the server folder containing them and switch to  "postgres" system user

    sudo -s -u postgres

-Create database and users

     psql < create_database.sql

 Note three users are created :

 1. darwin2 	=> main user having the full privileges(insertion and
    administration via web interface)  
    
 2. d2viewer 	=> read-only user for Internet consultation 
 3.  ipt_viewer => read-only user to connect a GBIF IPT (and.or similar web services)

 By default the password is the name of the user itself
 change it via
  psql (Linux) or SQL pgadmin

      ALTER USER [USER_NAME] WITH PASSWORD 'NEW_PASSWORD';

  
 -Install extensions in "darwin2" database.

     psql darwin2 < install_extensions_and_configure.sql

 
 These extensions are :

 1. postgis	=> GIS functionnalities  hstore		=> key/value dictionnary ,
    used to log the history of modifications  
 2. pg_trgm	=> phonetical  algorithms (trigrams), used to suggest translation of geographic names  
 3. fuzzystrmatch	=> phonetial algorithms (levenshtein), used to suggest
    translation of geographic names)

  4. pgcrypto	=> cryptogtraphy . md5 is used to rewrite the name of uploaded files in the server 
  
 5. uuid-ossp	=>  used to generate UUIDs

   6. plpython3u => optional, used
    to have REST webservice availabe from within sQL queries (useful to
    query geonames, GBIF etc...) and compare with darwin data4

The script also configures the search_path and schema darwin2 that will contains the tables and functions
 
 
 -install the main database schema :

     psql darwin2 < darwin2_rbins_schema.sql

 Initialisze data:

      psql -U darwin2 -h 127.0.0.1 -W darwin2  < initiate_data.sql

 1. create eucaryota and animals (kingdom and phylum) at the top of the hierarchy  
 2. create levels of taxonomic hierarchy  
 3. create widgets

note you will need to provide the darwin2 password in a shell
connection through IP to connect with darwin2 and access triggers and functions

9 Create webfolder
-------------------

Download the "darwin" subfolder and move it into the web folder of your server
e.g :
mv [your folder]/darwin /var/www/html/

Gie it the rights associated to apache
sudo chown -R www-data:www-data /var/www/html
sudo chmod -R 755 /var/www/html

+ cron pour stats
+ securité backend_dev

10 Configure Apache
-------------------
Put these lines into the Apache config of your site
(e.g. /etc/apache2/sites-available/default-ssl)
Beware of choosing the right VirtualHost (DNS name is defined in ServerName).
Adapt the path if needed

Alias "/darwin" "/var/www/html/darwin/web/"
    <Directory "/var/www/html/darwin/web">
         RewriteEngine On
         RewriteBase "/darwin/"
         DirectoryIndex index.php
         Options Indexes FollowSymLinks
         AllowOverride All
         Require all granted
    </Directory>

Create the config files
------------------------
give tempoarily writing permission on the darwin folder (e.g chmod -R 777 YOUR_FOLDER)
+
cd /var/www/html/darwin/config
 1. database connection
 cp database.yml.init database.yml
 
 adapt the content of the file to your actual connection parameters
 all:
  doctrine:
    class: sfDoctrineDatabase
    param:
      dsn: 'pgsql:host=localhost;dbname=darwin2'
      username: mydw2user
      password: mydw2password
 
 2. web folder config
 cp darwin.yml.init darwin.yml
 
 3. note app.yml contains the parameter of the LDAP server (Darwin can bridge user accounts with passwords defined in a LDAP server)
 
 (attention, check that these files do not contains tab, replace them with 4 spaces, otherwise errors when starting Darwin)
 
 Note, if you change the password salt connect to PostgreSQL with the darwin2 user and and change the password of the admin account accodingly:
 UPDATE users_login_infos set password =sha1('YOUR_SALTYOUR_ADMIN_PASSWORD') where user_ref=1 and login_type='local'; 
 
 Check the logs of the server do not start
 Take a look at the logs of the server do not start 
 tail -n 100 /var/log/apache2/error.log
 
 4=> salt update
 5=> $go in _dev.php