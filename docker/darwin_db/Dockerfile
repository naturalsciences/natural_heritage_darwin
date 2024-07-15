FROM postgres:14.9

ENV POSTGRES_USER=postgres \
    POSTGRES_PASSWORD=postgres_dw \
    PG_DATA=/var/lib/postgresql/data
	
WORKDIR /opt/darwin_db

RUN apt-get update
RUN apt-get -y install postgresql-14-postgis-3
RUN apt-get -y install postgresql-plpython3-14

CMD ["postgres"]