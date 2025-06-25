# Installing Symbiota

## REQUIREMENTS

- Apache HTTP Server (2.x or better) - other PHP enabled web servers will work, though the code has been well tested using Apache HTTP Server and Nginx
- PHP 8.2 or higher is recommended for the best performance, security, and feature support. The minimum requirement is PHP 8.1, but using older versions may cause security and performance issues over time. When third party authentication is enabled, PHP 8.2 or above is required.
  - Required extensions: mysqli, gd, mbstring, zip, curl, exif, openssl
  - Recommended configuration adjustments: upload_max_filesize = 100M (or expected file size upload), max_input_vars = 2000, memory_limit = 256M, post_max_size = 100M
  - Optional: Pear package Image_Barcode2 (https://pear.php.net/package/Image_Barcode2) – enables barcodes on specimen labels
  - Optional: Install Pear Mail for SMTP mail support: https://pear.php.net/package/Mail/redirected
- MariaDB (v10.3+) or MySQL (v8.0+) - Development and testing performed using MariaDB. If you are using Oracle MySQL instead, please [report any issues](https://github.com/BioKIC/Symbiota/issues/new).
- GIT Client - not required, though recommend for installation and updating source code

## INSTRUCTIONS

1. Download Symbiota code from GitHub repository
   - https://github.com/BioKIC/Symbiota
   - Command line checkout (recommended): sudo git clone https://github.com/BioKIC/Symbiota.git
2. Configure the Symbiota Portal
   1. Run /config/setup.bash (e.g. sudo bash setup.bash)
      This script will attempt to:
         - find all _template.* files and copy them to a new file at the same location without the '_template' suffix 
         - set ACL permissions on folders that need to be writable by the web server:
            - /api/storage/framework
            - /api/storage/logs
            - /content/collections/
            - /content/collicon/
            - /content/dwca/
            - /content/geolocate/
            - /content/logs/
            - /temp/
   2. Symbiota initialization configuration
      - Modify variables within config/symbini.php to match your project environment. See instructions within configuration file.
3. Install and configure Symbiota database schema
   1. Create new database (e.g. CREATE SCHEMA symbdb CHARACTER SET utf8 COLLATE utf8_general_ci)
   2. Create read-only and read/write users for Symbiota database
      - CREATE USER 'symbreader'@'localhost' IDENTIFIED BY 'password1';
      - CREATE USER 'symbwriter'@'localhost' IDENTIFIED BY 'password2';
      - GRANT SELECT,EXECUTE ON `symbdb`.\* TO `symbreader`@localhost;
      - GRANT SELECT,UPDATE,INSERT,DELETE,EXECUTE ON `symbdb`.\* TO `symbwriter`@localhost;
   3. Modify config/dbconnection.php with read-only and read/write logins, passwords, and schema (DB) name.
   4. Install database schema and schema patch files
      - Location: <SymbiotaBaseFolder>/config/schema/3.0/
      - Method 1: Using a web browser
         - Navigate to <SymbiotaServer>/admin/schemamanager.php. Selecting Sitemap from site menu will automatically forward to installer if database schema is missing. 
         - Follow the prompts provided by the database schema assistant
      - Method 2: MySQL command-line
         - Run db_schema-3.0.sql to install the core table structure
            - From MySQL command-line: SOURCE <BaseFolderPath>/config/schema/3.0/db_schema-3.0.sql
         - From MySQL command-line run each schema patch: SOURCE /BaseFolderPath/config/schema/3.0/patches/db_schema_patch-3.x.sql
         - Make sure to run the scripts in the correct order e.g. db_schema_patch-3.1.sql, db_schema_patch-3.2.sql, etc.
      `NOTE: At this point you should have an operational "out of the box" Symbiota portal.`
4. Customize
   1. Homepage
      - Modify index.php. This is your home page or landing page to which will need introductory text, graphics, etc.
   2. Layout - Within the /includes directory the header.php and footer.php files are used by all  
      pages to establish uniform layout. 
      - header.php: determines the content of the global page header and menu navigation.  
      - footer.php: determines the content of the global page footer.  
   3. Files for style control - Within the css/symbiota folder there are two files you can modify to change the appearance of the portal:
      - variables.css - Modify this file to set global values used across the portal
      - customization.css - Add css selectors to this file to override Symbiota's default styling on specific html elements 
      - NOTE: Do not modify any other css files as these files may be over written in future updates
   4. Customize language tags
      - Overide existing language tags or create new tags by modifying the override files in content/lang/templates/
         - Example: modify content/lang/templates/header.es.override.php to replace the defualt values used when browsing the portal in Spanish.
5. Misc configurations and recommendations
   - Modify usagepolicy.php as needed 
   - Install robots.txt file within root directory - The robots.txt file is a standard method used by websites to indicate to visiting web crawlers and other web robots which portions of the website they are allowed to visit and under what conditions. A robots.txt template can be found within the /includes directory. This file should be moved into the domain's root directory, which may or may not be the Symbiota root directory. The file paths listed within the file should be adjusted to match the portal installation path (e.g., start with $CLIENT_ROOT). See links below for more information:
     - https://developers.google.com/search/docs/crawling-indexing/robots/create-robots-txt
     - https://en.wikipedia.org/wiki/Robots.txt
   - Refer to the [third party authentication instructions](https://github.com/BioKIC/Symbiota/blob/master/docs/third_party_auth_setup.md) for specifics about third party authentication setup.

## DATA

Data - The general layers of data within Symbiota are: user, taxonomic, occurrence (specimen), images, checklist, identification key, and taxon profile (common names, text descriptions, etc).
While user interfaces have been developed for web management for most of these data layers, some table tables still need to be managed via the backend (e.g. loaded by hand).

   1. User and permissions - Default administrative user has been installed with following login: username = admin; password: admin.
      It is highly recommended that you change the password, or better yet, create a new admin user, assign admin rights, and then delete default admin user.
      Management control panel for permissions is available within Data Management Panel on the sitemap page.
   2. Occurrence (Specimen) Data: SuperAdmin can create new collection instances via Data Management pane within sitemap. 
      Within the collection's data management menu, one can provide admin and read access to new users, add/edit occurrences, batch load data, etc.
   3. Taxonomic Thesaurus - Taxon names are stored within the 'taxa' table.
      Taxonomic hierarchy and placement definitions are controlled in the 'taxstatus' table.
      A recursive data relationship within the 'taxstatus' table defines the taxonomic hierarchy.
      While multiple taxonomic thesauri can be defined, one of the thesauri needs to function as the central taxonomy.
      Names must be added in order from upper taxonomic levels to lower (e.g. kingdom, class, order, variety).
      Accepted names must be loaded before non-accepted names.
      1. Names can be added one-by-one using taxonomic management tools (see sitemap.php)
      2. Name can be imported from taxonomic authorities (e.g. Catalog of Life, WoRMS, TROPICOS, etc) based on occurrence data loaded into the system.
         This is the recommended method since it will focus on only relevant taxonomic groups. First, load an occurrence dataset (see step 2 above), 
         then from the Collection Data Management menu, select Data Cleaning Tools => Analyze taxonomic names...
      3. Batch Loader - Multiple names can be loaded from a flat, tab-delimited text file. See instructions on the batch loader for loading multiple names from a flat file.
      4. Look in /config/schema/data/ folder to find taxonomic thesaurus data that may serve as a base for your taxonomic thesaurus.
   4. See <https://symbiota.org> for tutorials and more information on how load and manage data 

## UPDATES

Please read the [UPDATE.md](UPDATE.md) file for instructions on how to update Symbiota.
