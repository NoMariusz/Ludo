FROM mysql:8.0

COPY ludo.sql /docker-entrypoint-initdb.d/
COPY ./docker/mysql/my.cnf /etc/my.cnf

CMD ["mysqld"]