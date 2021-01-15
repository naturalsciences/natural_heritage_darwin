#!/bin/bash

if [ $# -lt 2 ]
  then
    echo "Input file as forst argument, target as second"
fi


FILE=$1
OUTPUT=$2
echo "Go $1"
cp $1 $2

sed -i "s/$(echo -e "\x90")/É/g" $2
sed -i "s/$(echo -e "\x91")/AE/g" $2
sed -i "s/$(echo -e "\x93")/ô/g" $2
sed -i "s/$(echo -e "\x94")/ö/g" $2
sed -i "s/$(echo -e "\x9A")/Ü/g" $2
sed -i "s/$(echo -e "\x9B")/ø/g" $2
sed -i "s/$(echo -e "\xA2")/ó/g" $2
sed -i "s/$(echo -e "\xA1")/í/g" $2
sed -i "s/$(echo -e "\xA4")/ñ/g" $2
sed -i "s/$(echo -e "\xA0")/á/g" $2
sed -i "s/$(echo -e "\xC6")/ã/g" $2
sed -i "s/$(echo -e "\xE4")/õ/g" $2
sed -i "s/$(echo -e "\xF8")/°/g" $2
sed -i "s/$(echo -e "\x81")/ü/g" $2
sed -i "s/$(echo -e "\x82")/é/g" $2
sed -i "s/$(echo -e "\x83")/â/g" $2
sed -i "s/$(echo -e "\x84")/ä/g" $2
sed -i "s/$(echo -e "\x85")/à/g" $2
sed -i "s/$(echo -e "\x87")/ç/g" $2
sed -i "s/$(echo -e "\x88")/ê/g" $2
sed -i "s/$(echo -e "\x8A")/è/g" $2
sed -i "s/$(echo -e "\x8B")/ï/g" $2
sed -i "s/$(echo -e "\x8C")/î/g" $2
echo "Done $2"
