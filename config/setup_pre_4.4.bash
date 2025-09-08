#Create configuration files from conf template files
echo "Creating dbconfiguration file: /config/dbconnection.php"
cp -n ../config/dbconnection_template.php ../config/dbconnection.php
echo "Creating Symbiota configuration file: /config/symbini.php"
cp -n ../config/symbini_template.php ../config/symbini.php
echo "Creating homepage: /index.php"
cp -n ../index_template.php ../index.php
echo "Creating header include: /includes/header.php"
cp -n ../includes/header_template.php ../includes/header.php
echo "Creating Minimal header include: /includes/minimalheader.php"
cp -n ../includes/minimalheader_template.php ../includes/minimalheader.php
#echo "Creating Left Menu include: /includes/leftmenu.php"
#cp -n ../includes/leftmenu_template.php ../includes/leftmenu.php
echo "Creating footer include: /includes/footer.php"
cp -n ../includes/footer_template.php ../includes/footer.php
echo "Creating head include: /includes/head.php"
cp -n ../includes/head_template.php ../includes/head.php
echo "Creating usage policy include: /includes/usagepolicy.php"
cp -n ../includes/usagepolicy_template.php ../includes/usagepolicy.php

#Adjust file permission to give write access to certain folders and files
echo "Adjusting file permissions"
chmod -R 777 ../content/collicon
chmod -R 777 ../content/dwca
chmod -R 777 ../content/geolocate
chmod -R 777 ../content/imglib
chmod -R 777 ../content/logs 
chmod -R 777 ../api/storage/framework 
chmod -R 777 ../api/storage/logs 
