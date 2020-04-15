FROM php:fpm-buster

ENV DEBIAN_FRONTEND noninteractive

RUN set -e \
      && ln -sf /bin/bash /bin/sh

RUN set -e \
      && apt-get -y update \
      && apt-get -y install locales dirmngr apt-transport-https ca-certificates software-properties-common gnupg2 \
      && apt-key adv --keyserver keys.gnupg.net --recv-key 'E19F5F87128899B192B1A2C2AD5F960A256A04AF' \
      && locale-gen en_US.UTF-8 \
      && update-locale LC_ALL=en_US.UTF-8 LANG=en_US.UTF-8 \
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
#RUN R -e "install.packages('PhantomJS',dependencies=TRUE, repos='http://cran.rstudio.com/')" unavailable package in R 3.6.3

WORKDIR /var/www/rlang-php