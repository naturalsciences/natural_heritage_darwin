#/bin/sh
dir=$(dirname $(which $0));
#script launched by incron 
# First step : import the xml file into staging table
# Second step : check errors 
php $dir/../symfony darwin:load-import && php $dir/../symfony darwin:check-import
