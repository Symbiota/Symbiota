# Use the official MySQL 5.7 image
FROM docker.io/library/mysql:5.7.44

# RUN sed -i -e "s//innodb_file_format = Barracuda/g" /etc/mysql/my.cnf
# RUN echo innodb_file_format = Barracuda >> /etc/my.cnf
# Copy custom mysql configuration file into the image
# COPY my.cnf /etc

# Set environment variables for MySQL configuration
ENV MYSQL_ROOT_PASSWORD=password
ENV MYSQL_DATABASE=symbiota
ENV MYSQL_USER=user
ENV MYSQL_PASSWORD=password

# Copy the SQL initialization script into the container
COPY db_schema-3.0.sql /docker-entrypoint-initdb.d/

# Expose the MySQL port
EXPOSE 3306

# Start bash
# CMD ["/bin/bash"]
CMD ["mysqld"]
