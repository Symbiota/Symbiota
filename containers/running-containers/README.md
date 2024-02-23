# Build
1. Navigate to the directory where this readme and the docker-compose yaml file resides. Run `docker-compose up -d`.
2. Docker should start building the containers. If there are no errors, the containers should be started automatically.

Note: the symbiota code is mounted instead of being pulled from a repo. Database can be imported using a backup file or through the patches used by symbiota and also the HUH patch developed in summer 2023 by BU Spark! (instructions to be added).

# Mysql container setup:
1. In the compose.yaml file, make sure to set up a bind mount from your local directory that stores the sql backups into the Mysql container. If you have another way to import your schema, skip until step 3.
2. Run bash in the symbiota-db container, and use `mysql -u root --password=password symbiota < <backup file name>` to import the schema.
3. Run the Mysql daemon using `mysql -u root -p` and enter the password as 'password' when prompted.
4. Run the following commands to create symbiota users:
```
CREATE USER 'symbiota-r'@'%' IDENTIFIED BY 'symbiota-r-pass';
GRANT SELECT ON symbiota.* TO 'symbiota-r'@'%';

CREATE USER 'symbiota-rw'@'%' IDENTIFIED BY 'symbiota-rw-pass';
GRANT SELECT, INSERT, UPDATE, DELETE ON symbiota.* TO 'symbiota-rw'@'%';

FLUSH PRIVILEGES;
```
5. In `/symbiota/config/dbconnection.php` in your mount, the connection info should be:
```
array(
			'type' => 'readonly',
			'host' => 'symbiota-db',            //which is the name of the container running mysql
			'username' => 'symbiota-r',
			'password' => 'symbiota-r-pass',
			'database' => 'symbiota',           //which is the name of the database in mysql
			'port' => '3306',
			'charset' => 'utf8'
        ),
        array(
			'type' => 'write',
			'host' => 'symbiota-db',
			'username' => 'symbiota-rw',
			'password' => 'symbiota-rw-pass',
			'database' => 'symbiota',
			'port' => '3306',
			'charset' => 'utf8'
        )
```