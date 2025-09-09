# Installing Symbiota

## REQUIREMENTS
GIT Client - not required, though recommend for installation and updating source code

### Web Server
Apache HTTP Server (2.x or better) - other PHP-enabled web servers will work, though the code has been well-tested using Apache HTTP Server and Nginx.

### PHP
PHP 8.2 or higher is recommended for the best performance, security, and feature support. The minimum requirement is PHP 8.1, but using older versions may cause security and performance issues over time. When third party authentication is enabled, PHP 8.2 or above is required.

Required extensions:
- mbstring
- openssl

```ini
extension=curl
extension=exif
extension=gd
extension=mysqli
extension=zip
```

Optional: Pear package [Image_Barcode2](https://pear.php.net/package/Image_Barcode2) – enables barcodes on specimen labels

Optional: Install Pear [Mail](https://pear.php.net/package/Mail/redirected) for SMTP mail support

Optional: Install pecl package [Imagick](https://pecl.php.net/package/imagick) alternative library for image processing.

Recommended configuration adjustments: 
```ini
; Maximum allowed size for uploaded files.
; https://php.net/upload-max-filesize
upload_max_filesize = 100M

; How many GET/POST/COOKIE input variables may be accepted
max_input_vars = 2000

; Maximum amount of memory a script may consume
; https://php.net/memory-limit
memory_limit = 256M

; Maximum size of POST data that PHP will accept.
; Its value may be 0 to disable the limit. It is ignored if POST data reading
; is disabled through enable_post_data_reading.
; https://php.net/post-max-size
post_max_size = 100M
```

### Database
MariaDB (v10.3+) or MySQL (v8.0+) - Development and testing performed using MariaDB. If you are using Oracle MySQL instead, please [report any issues](https://github.com/Symbiota/Symbiota/issues/new).

Recommended Settings:
```sql
SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
```

## INSTRUCTIONS

### STEP 1: Download Symbiota code

```
git clone https://github.com/Symbiota/Symbiota.git
```

or [Download Source Files From Latest Release](https://github.com/Symbiota/Symbiota/releases)

### STEP 2: Run setup script

Run /config/setup.bash (e.g. sudo bash setup.bash)

This script will attempt to:

Find all `_template.*` files and copy them to a new file at the same location without the `_template` suffix.

<!-- Output from: tree --prune --matchdirs -P '*_template.*' -I 'vendor' Symbiota -->
```
Symbiota
├── collections
│   ├── editor
│   │   └── includes
│   │       └── config
│   │           ├── occurVarColl1_template.php
│   │           ├── occurVarDefault_template.php
│   │           └── occurVarGenObsDefault_template.php
│   └── specprocessor
│       └── standalone_scripts
│           ├── ImageBatchConf_template.php
│           └── ImageBatchConnectionFactory_template.php
├── config
│   ├── auth_config_template.php
│   ├── dbconnection_template.php
│   └── symbini_template.php
├── content
│   ├── collections
│   │   └── reports
│   │       └── labeljson_template.php
│   └── lang
│       ├── index.es_template.php
│       └── misc
│           ├── aboutproject.en_template.php
│           └── aboutproject.es_template.php
├── docs
│   └── pull_request_template.md
├── includes
│   ├── citationcollection_template.php
│   ├── citationdataset_template.php
│   ├── citationgbif_template.php
│   ├── citationportal_template.php
│   ├── footer_template.php
│   ├── header_template.php
│   ├── head_template.php
│   ├── minimalheader_template.php
│   └── usagepolicy_template.php
├── index_template.php
└── misc
    ├── aboutproject_template.php
    ├── contacts_template.php
    ├── generalsimple_template.php
    ├── general_template.php
    └── partners_template.php
```

Then set ACL permissions on folders that need to be writable by the web server.
```
Symbiota
├── api
│   └── storage
│       └── framework
└── content
    ├── collections
    ├── collicon
    ├── dwca
    └── geolocate
```

### STEP 3: Configure the Symbiota Portal
Symbiota initialization configuration

Modify variables within 
<!-- Output from: tree --prune --matchdirs -P 'symbini.php' -I 'vendor' Symbiota -->
```
Symbiota
└── config
    └── symbini.php
```
to match your project environment. See instructions within configuration file.
<!-- TODO (Logan) Add mininum required symbini variables here -->

### STEP 4: Install and configure Symbiota database schema
<!-- 1. Create new database (e.g. CREATE SCHEMA symbdb CHARACTER SET utf8 COLLATE utf8_general_ci) -->

<!-- 2. Create read-only and read/write users for Symbiota database -->
Run sql to create database and create read and write users. Make sure to change passwords and database name as needed.

* Note make sure to run this sql as the root user or a user with proper permissions.
```sql
-- Create new database
CREATE SCHEMA symbdb CHARACTER SET utf8 COLLATE utf8_general_ci

-- Create read-only and read/write users for Symbiota database
CREATE USER 'symbreader'@'localhost' IDENTIFIED BY 'password1';
CREATE USER 'symbwriter'@'localhost' IDENTIFIED BY 'password2';
GRANT SELECT,EXECUTE ON `symbdb`.* TO `symbreader`@localhost;
GRANT SELECT,UPDATE,INSERT,DELETE,EXECUTE ON `symbdb`.* TO `symbwriter`@localhost;
```

Then modify `dbconnection.php` with read-only and read/write logins, passwords, and database name to the values you chose.
* Note - If running a php version prior to 8.1 you must add the following
```php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)
```

<!-- Output: tree --prune --matchdirs -P 'dbconnection.php' -I 'vendor' Symbiota  -->
```
Symbiota
└── config
    └── dbconnection.php
```

Note - If your php version lower than 8.1 you must add this line to `dbconnection.php` in the `getCon` function.
```php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)
```

Lastly, install database schema and schema patch files

#### Method 1: Web Browser Schema Manager
Navigate to `<SymbiotaServer>/admin/schemamanager.php`.

Selecting Sitemap from site menu will automatically forward to installer if database schema is missing.

Follow the prompts provided by the database schema assistant

#### Method 2: MySQL Command Line
Run the following sql source files in order from top to bottom
<!-- Output: tree --prune --matchdirs -P '*_patch-*|db_schema-*' -I 'vendor|1.0' Symbiota -->
```
Symbiota
└── config
    └── schema
        └── 3.0
            ├── db_schema-3.0.sql
            └── patches
                ├── db_schema_patch-3.1.sql
                ├── db_schema_patch-3.2.sql
                └── db_schema_patch-3.3.sql
```

`NOTE: At this point you should have an operational "out of the box" Symbiota portal.`

### STEP 5: Customize

#### Homepage
Modify index.php. This is your home page or landing page which will need introductory text, graphics, etc.

#### Layout
Layout - Within the /includes directory, the header.php and footer.php files are used by all pages to establish uniform layout.

<!-- Output: tree --prune --matchdirs -P 'header.php|footer.php' -I 'vendor' Symbiota -->
```
Symbiota
└── includes
    ├── footer.php - determines the content of the global page footer and menu navigation.
    └── header.php - determines the content of the global page header
```

#### Css Styles
Files for style control - Within the css/symbiota folder there are two files you can modify to change the appearance of the portal:
<!-- Output: tree --prune --matchdirs -P 'variables.css|customizations.css' -I 'vendor' Symbiota -->
```
Symbiota
└── css
    └── symbiota
        ├── customizations.css - Add css selectors to override Symbiota default styling
        └── variables.css - Set global values used across the portal
```
NOTE: Do not modify any other css files as these files may be overwritten in future updates

#### Customize language tags
Override existing language tags or create new tags by modifying the override files in content/lang/templates/
Example: modify content/lang/templates/header.es.override.php to replace the default values used when browsing the portal in Spanish.

#### Misc configurations and recommendations
Modify usagepolicy.php as needed

Install robots.txt file within root directory - The robots.txt file is a standard method used by websites to indicate to visiting web crawlers and other web robots which portions of the website they are allowed to visit and under what conditions. A robots.txt template can be found within the /includes directory. This file should be moved into the domain's root directory, which may or may not be the Symbiota root directory. The file paths listed within the file should be adjusted to match the portal installation path (e.g., start with $CLIENT_ROOT). See links below for more information:

https://developers.google.com/search/docs/crawling-indexing/robots/create-robots-txt
https://en.wikipedia.org/wiki/Robots.txt

Refer to the [third party authentication instructions](https://github.com/Symbiota/Symbiota/blob/master/docs/third_party_auth_setup.md) for specifics about third party authentication setup.

## DATA

Data - The general layers of data within Symbiota are: user, taxonomic, occurrence (specimen), images, checklist, identification key, and taxon profile (common names, text descriptions, etc).
While user interfaces have been developed for web management for most of these data layers, some table tables still need to be managed via the backend (e.g. loaded by hand).

### User and permissions
A default administrative user has been installed with following login: username = admin; password: admin.
It is highly recommended that you change the password, or better yet, create a new admin user, assign admin rights, and then delete default admin user.
A management control panel for User Permissions is available within Data Management Panel on the sitemap page.

### Occurrence (Specimen) Data
SuperAdmins can create new collection instances via the Data Management pane within sitemap. 
Within the collection's data management menu, one can provide admin and edit access to new users, add/edit occurrences, batch load data, etc.

### Taxonomic Thesaurus
Taxon names are stored within the 'taxa' table.
Taxonomic hierarchy and placement definitions are controlled in the 'taxstatus' table.
A recursive data relationship within the 'taxstatus' table defines the taxonomic hierarchy.
While multiple taxonomic thesauri can be defined, one of the thesauri needs to function as the central taxonomic authority.
Names must be added in order from upper taxonomic levels to lower (e.g. kingdom, class, order).
Accepted names must be loaded before non-accepted names.
  1. Names can be added one-by-one using taxonomic management tools (see sitemap.php)
  2. Names can be imported from taxonomic authorities (e.g., Catalog of Life, WoRMS, etc.) based on occurrence data loaded into the system.
     This is the recommended method since it will focus on only relevant taxonomic groups. First, load an occurrence dataset (see step 2 above), 
     then from the Collection Data Management menu, select Data Cleaning Tools => Analyze taxonomic names...
  3. Batch Loader - Multiple names can be loaded from a flat, tab-delimited text file. See instructions on the batch loader for loading multiple names from a flat file.
  4. Look in /config/schema/data/ folder to find taxonomic thesaurus data that may serve as a base for your taxonomic thesaurus.

### Futher Assistance
See <https://symbiota.org> for tutorials and more information on how load and manage data 

## UPDATES

Please read the [UPDATE.md](UPDATE.md) file for instructions on how to update Symbiota.
