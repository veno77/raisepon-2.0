FROM mysql:8.0
LABEL "Project"="Raisepon"
LABEL "Author"="Veno"

#ENV MYSQL_ROOT_PASSWORD="blahsux12321"
#ENV MYSQL_RANDOM_ROOT_PASSWORD="yes"
#ENV MYSQL_DATABSE="raisepon"
#ENV MYSQL_USER="raisepon"
#ENV MYSQL_PASSWORD="r41sepon"
COPY ./raisepon.sql /docker-entrypoint-initdb.d/raisepon.sql
