
1 If you use GIT
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


3 Enable PostgreSQL  APT repository
--------------------------------
**3.1 Create the file repository configuration:**

    sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list'

**3.2 Import the repository signing key:**

    wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -

**3.3 Update the package lists:**

    sudo apt-get update

4 Enable PostGIS APT repository
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

7 INSTALL PHP 7.4 and apache2.4
-----------------------------


    sudo apt-get install php-7.4
    sudo apt-get install apache2
    sudo apt-get install libapache2-mod-php7.4 
    sudo apt-get install php-apcu
	sudo apt-get install php-apcu-bc
	sudo apt-get install php-ldap
	sudo apt-get install php-mbstring
    sudo apt-get install php-imagick
	sudo apt-get install php-raphf
	sudo apt-get install php-propro
(package names may differ in your distribution)

Link PHP to Apache2 (note : *fpm* is another, more performant, possibility) :
	
    sudo a2enmod php7.4
   
   To start Apache 
   
     sudo systemctl restart apache2

 
8 Enable PHP dependencies for PostgreSQL
------------------------------
#postgresql driver

    sudo apt-get install php7.4-pgsql
    
    sudo apt-get install php7.4-xml 




9 Create the database 
------------------
In GitHub consider the https://github.com/naturalsciences/natural_heritage_darwin/tree/public_1.0  branch

Installation scripts are in the ***db_schema***  subfolder

Go to the server folder containing them and switch to  "postgres" system user

    sudo -s -u postgres

 

 - Create database and users

     psql < create_database.sql

Three users are created :

>  1. darwin2 	=> main user having the full privileges(insertion and
>     administration via web interface)  
>     
>  2. d2viewer 	=> read-only user for Internet consultation 
>  3.  ipt_viewer => read-only user to connect a GBIF IPT (and.or similar web services)

 

 - By default, passwords of these account are the name of the user itself.
 After installation change it via

     

>  psql (Linux) or SQL pgadmin
>   
>       ALTER USER [USER_NAME] WITH PASSWORD 'NEW_PASSWORD';

  
 - Install extensions in "darwin2" database.

     psql darwin2 < install_extensions_and_configure.sql

 

>  These extensions are :
> 
>  1. postgis	=> GIS functionnalities  hstore		=> key/value dictionnary ,
>     used to log the history of modifications  
>  2. pg_trgm	=> phonetical  algorithms (trigrams), used to suggest translation of geographic names  
>  3. fuzzystrmatch	=> phonetial algorithms (levenshtein), used to suggest
>     translation of geographic names)
> 
>   4. pgcrypto	=> cryptogtraphy . md5 is used to rewrite the name of uploaded files in the server 
>   
>  5. uuid-ossp	=>  used to generate UUIDs
> 
>    6. plpython3u => optional, used
>     to have REST webservice available from within SQL queries (useful to
>     query geonames, GBIF etc...) and compare with darwin data

The script also configures the search_path, this the list of database schemas that will contains the tables and functions
  
 -install the main database schema :

     psql darwin2 < darwin2_rbins_schema.sql

 - Initialise data:

       psql -U darwin2 -h 127.0.0.1 -W darwin2  < initiate_data.sql

 1. create eucaryota and animals (kingdom and phylum) at the top of the hierarchy  
 2. create levels of taxonomic hierarchy  
 3. create widgets

note you will need to provide the darwin2 password in a shell
connection through IP to connect with darwin2 and access triggers and functions

## Web app installation

10 Create webfolder
-------------------

Download the "darwin" subfolder and move it into the web folder of your server
e.g :

    mv [your folder]/darwin /var/www/html/

Gie it the rights associated to apache

    sudo chown -R www-data:www-data /var/www/html
    sudo chmod -R 755 /var/www/html

11 Configure Apache
-------------------
Put these lines into the Apache config of your site
(e.g.  **/etc/apache2/sites-available/default-ssl** )
Beware of choosing the right VirtualHost (DNS name is defined in ServerName).
Module rewrite must be enabled 'Rewrite rule are in web.htaccess
Adapt paths and RewriteBase if needed

> Alias "/darwin" "/var/www/html/darwin/web/"
>     <Directory "/var/www/html/darwin/web">
>          RewriteEngine On
>          DirectoryIndex index.php
>          Options Indexes FollowSymLinks
>          AllowOverride All
>          Require all granted
>     </Directory>

	
Check also the content of the web/.htaccess file, especially if you use another RewriteBase than "darwin" (base folder URL).
	Note that the entry point is located in the ./web subfolder

	e.g (in **.htaccess**)
	
	Options +FollowSymLinks +ExecCGI

    <IfModule mod_rewrite.c>
      RewriteEngine On
    
      # uncomment the following line, if you are having trouble
      # getting no_script_name to work
      RewriteBase /**[your darwin folder here]**
    
      # we skip all files with .something
      RewriteCond %{REQUEST_URI} \..+$
      RewriteCond %{REQUEST_URI} !\.html$
      RewriteRule .* - [L]
    
      # we check if the .html version is here (caching)
      RewriteRule ^$ index.html [QSA]
      RewriteRule ^([^.]+)$ $1.html [QSA]
      RewriteCond %{REQUEST_FILENAME} !-f
    
      # no, so we redirect to our front web controller
      RewriteRule ^(.*)$ public.php [QSA,L]
    </IfModule>

Note : use RewriteBase "/" if you put Darwin at the root of the URL

	
12 Create the config files
-------------------------------
The config files of Darwin are located in the **./config** subfolder. Their format is yaml (without tab) such as defined by Symfony 1.
The Git branch doesndt provide the yml filesn but templates file shaving the "**yml.init**" extensions, that needes to be copied to "**.yml**" files and having the right values declared.
The aim is to prevent the accidental publishing of connection credentials to GitHub.


You must give temporarily writing permission on the darwin folder top copy them (e.g `chmod -R 777 YOUR_FOLDER`) or copy them via sudo


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

Two of the mot important parameters of this file are **salt** to hash passwords (see below) and **root_url_darwin**, to declare the root URL of the database (e.g. https://darwin.naturalsciences.be/ ).  Declare your IP if the server doesn't have a domain name.
 
 3. note :  **app.yml** contains the parameter of the LDAP server (Darwin can bridge user accounts with passwords defined in a LDAP server)
 
 Beware ! Check that these yml files do not contains tab, replace them with 4 spaces, otherwise you'll get errors (logged in Apache) when starting Darwin...
 

 
 Check the logs if the server does not start...

     tail -n 100 /var/log/apache2/error.log
     

13 Salt for web-interface passwords
-------------------------------------
 In case of local (i.e from database) authentication, a "salt" (suffix) is used to generate user passwords that will get the sha1 hash. This makes the original password harder to find.
 The salt is defined in ./darwin/config/darwin.yml
 Its default value is "salt". It his strongly advised to change it.
 

>  all:   .general:
>     salt: [YOURPWD_SALT]

Once it is change you must also connect to the database and adapt the password of the initial admin user.
e.g.

    UPDATE users_login_infos set password=sha1('USER_DEFINED_SALT'||'ADMIN_PASSWORD') where user_ref=1;

**Note** : this admin account are those internal Darwin, to log to the web interface, different from the accounts defined at point 9 that are working at lower level, to connect directly the PostgreSQL database è!

14 Enable debug rights
-------------------
At this stage, Darwin should be available on the URL
https://YOUR_SERVER/darwin

The user to log is admin with the  'ADMIN_PASSWORD' you defined.


There are two debug URL, displaying the HTTP variables, the underlying SQL queries (as long as they are produced by Dostribe or PDO), with benchmarks for time performances.

    https://YOUR_SERVER/darwin/public_dev.php (public interface)
    https://YOUR_SERVER/darwin/backend_dev.php (main enriched interface for logged users)

By default they are publicly not available.
You can enable them by providing the IP of your server in the **./darwin/web/ips.cgf file**
e.g
	

    192.168.0.20

You can provide several IPs on several  different lines.
Shorten the URL for network masks (e.g. "192.168.0" to declare 192.168.0.0/24)
This will work only if Darwin is also configured on a port 80 address (without SSL/HTTPS)

15 Cron jobs
-------------------
Darwin features an embedded statistical tool, running asynchronously, allowing the user to export statistics by collections on tab-delimited formats.
These statistics provides :

 1. the amount of specimen present in the main collection and its subcollections
 2. the corresponding amount  of types specimens
 3. the amount of present taxa (by ranks)
 4. the MIDS level (https://www.tdwg.org/community/cd/mids/)

The statistics are based on Materialized view that need to be refreshed via a PostgreSQL function

     SELECT * FROM darwin2.fct_rmca_reporting_refresh_views();

./cronjons/ contains a script to link it to a cron job :

    #!/usr/bin/env bash
    sudo -u postgres psql  darwin2 <<END
        SELECT * FROM darwin2.fct_rmca_reporting_refresh_views();
    END

exemple of configuration 

    crontab -e
 and then (to refresh at every hours)
 
    0 * * * *   /[FOLDER_FOR_CRONJOBS]/darwin_collection_statistics.sh

Other cron jobs to set on the apache user (legacy statistic widget developped by RBINS)
e.g. 
           

    sudo -s -u www-data
    crontab -e

And then

    # m h  dom mon dow   command
    00 12 * * * /var/www/html/darwin/tools/compute_stats.sh
    00 19 * * * /var/www/html/darwin/tools/clean_report.sh

(check of the content of these files correspond to your server path and adapt accordingly)

    #cron for www-data user
    php /var/www/html/darwin/symfony darwin:gen-stats