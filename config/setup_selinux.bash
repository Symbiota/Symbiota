#!/bin/bash

chcon -R -t httpd_sys_rw_content_t ../content/collections/
chcon -R -t httpd_sys_rw_content_t ../content/collicon/
chcon -R -t httpd_sys_rw_content_t ../content/geolocate/
chcon -R -t httpd_sys_rw_content_t ../content/logs/
chcon -R -t httpd_sys_rw_content_t ../content/sitemaps/
chcon -R -t httpd_sys_rw_content_t ../api/storage/framework/
chcon -R -t httpd_sys_rw_content_t ../api/storage/logs/
