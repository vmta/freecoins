FROM alpine as build
RUN apk add --no-cache git \
    && git clone --depth=1 https://github.com/vmta/freecoins.git freecoins \
    && mv /freecoins/include/config.php.example /freecoins/include/config.php \
    && sed -i 's/rpc_mainnet:6332/rpcm:6332/' /freecoins/include/config.php \
    && sed -i 's/rpc_testnet:16332/rpct:16332/' /freecoins/include/config.php \
    && sed -i 's/rpcmainnet:rpcmainnetpass/rpcuser:rpcpass/' /freecoins/include/config.php \
    && sed -i 's/rpctestnet:rpctestnetpass/rpcuser:rpcpass/' /freecoins/include/config.php

FROM php:apache
COPY --from=build /freecoins/. /var/www/html/

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && sed -i 's/error_reporting \= /error_reporting = E_ERROR ;/' /usr/local/etc/php/php.ini \
    && sed -i 's/log_errors \= /log_errors \= Off ;/' /usr/local/etc/php/php.ini
