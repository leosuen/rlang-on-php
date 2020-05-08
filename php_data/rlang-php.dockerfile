FROM php:fpm-buster

ENV DEBIAN_FRONTEND noninteractive

RUN set -e \
      && ln -sf /bin/bash /bin/sh

RUN apt-get update && apt-get install locales locales-all -y
ENV LANG=en_US.UTF-8 \
      LC_NUMERIC=en_US.UTF-8 \
      LC_TIME=en_US.UTF-8 \
      LC_MONETARY=en_US.UTF-8 \
      LC_PAPER=en_US.UTF-8 \
      LC_NAME=en_US.UTF-8 \
      LC_ADDRESS=en_US.UTF-8 \
      LC_TELEPHONE=en_US.UTF-8 \
      LC_MEASUREMENT=en_US.UTF-8 \
      LC_IDENTIFICATION=en_US.UTF-8
RUN locale-gen en_US.UTF-8 && apt-get clean

RUN set -e \
      && apt-get -y update \
      && apt-get -y install dirmngr apt-transport-https ca-certificates software-properties-common gnupg2 \
      && apt-key adv --keyserver keys.gnupg.net --recv-key 'E19F5F87128899B192B1A2C2AD5F960A256A04AF' \
      && add-apt-repository 'deb https://cran.csie.ntu.edu.tw//bin/linux/debian buster-cran35/' \
      && apt-get -y update \
      && apt-get install r-base curl openssl -y \
      && apt-get -y autoremove \
      && apt-get clean \
      && rm -rf /var/lib/apt/lists/*



#RUN R -e "install.packages('httr',dependencies=TRUE, repos='http://cran.rstudio.com/')"
RUN R -e "install.packages('psych',dependencies=TRUE, repos='http://cran.rstudio.com/')"
RUN R -e "install.packages('lavaan',dependencies=TRUE, repos='http://cran.rstudio.com/')"
RUN R -e "install.packages('lavaanPlot',dependencies=TRUE, repos='http://cran.rstudio.com/')"
RUN R -e "install.packages('plotly',dependencies=TRUE, repos='http://cran.rstudio.com/')"
RUN R -e "install.packages('htmlwidgets',dependencies=TRUE, repos='http://cran.rstudio.com/')"
RUN R -e "install.packages('webshot',dependencies=TRUE, repos='http://cran.rstudio.com/')"
RUN R -e "install.packages('rjson',dependencies=TRUE, repos='http://cran.rstudio.com/')"
RUN R -e "install.packages('jsonlite',dependencies=TRUE, repos='http://cran.rstudio.com/')"
#RUN R -e "install.packages('PhantomJS',dependencies=TRUE, repos='http://cran.rstudio.com/')" unavailable package in R 3.6.3

USER www-data
WORKDIR /var/www/rlang-php
RUN chown -R www-data:www-data /var/www/rlang-php