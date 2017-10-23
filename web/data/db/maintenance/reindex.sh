#!/bin/bash
time psql -d darwin2 -h 192.168.20.17 -U darwin2 -f reindex.sql
time psql -d darwin2 -h 192.168.20.17 -U darwin2 -f vacuum.sql
