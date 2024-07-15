#https://github.com/docker-library/php/blob/7f89b1440e0122cc0edaf4ee0dbd8e1d7510df73/8.2/alpine3.18/fpm/Dockerfile
FROM httpd:2.4.58


COPY httpd.conf /usr/local/apache2/conf/

RUN chmod -R 777 /usr/local/apache2/conf

EXPOSE 80
CMD ["httpd-foreground"]