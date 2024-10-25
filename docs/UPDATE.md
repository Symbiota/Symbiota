# Updating Symbiota

## Before Updating

1. Read the release notes (https://github.com/BioKIC/Symbiota/releases) to confirm that your server meets the minimum requirements and that all dependencies are installed.
2. Make a backup of your Symbiota web folder
3. Make a backup your Symbiota database
    
## Code Updates

If you installed Symbiota through a GitHub code repository using the clone command (git clone https://github.com/BioKIC/Symbiota.git), code changes and bugs fixes can be integrated simply by running "git pull" via command line. After updating the code, make sure your database schema matches your code version (see section below).   

## Database Schema Updates

Some Symbiota code updates will require database schema modifications. Schema changes can be applied by running new schema patches added since the last update installed. Current code and database schema version numbers are listed at the bottom of `sitemap.php` page. Make sure the code and database schema version have the same major and minor version numbers (e.g. Symbiota v3.1.x, Schema v3.1). Otherwise, run the database schema patch(es) in the correct order until they match.

There are two methods for running a schema patch:

1. Database Schema Assistant (Recommended)
    - Using a web browser navigate to `<SymbiotaServer>/admin/schemamanager.php`
        - The assistant will automatically detect if there are patches that need be run.
        - re-run the assistant for each patch that needs to be installed

2. MySQL command line
    - Run `source db_schema_patch_3.x.sql` from the MySQL client command line.
    - For example, run `config\schema\1.0\patches\db_schema_patch-1.2.sql`, and then `config\schema\1.0\patches\db_schema_patch_3.0.sql`, and finally `config\schema\3.0\patches\db_schema_patch-3.1.sql` to bring the database schema version to 3.1.

## Misc

Some files (such as those generated from _template.* files) may need to be reviewed and modified to take advantage of updated Symbiota Code. It may be helpfull to review the current installation [INSTALL.md](INSTALL.md) file for changes in configuration.