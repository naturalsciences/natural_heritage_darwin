#!/usr/bin/env bash
sudo -u postgres psql  darwin2 <<END
    SELECT * FROM darwin2.fct_rmca_reporting_refresh_views();
END
