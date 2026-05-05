# Pinned to the exact upstream tag the existing heimdall-1 container runs.
# linuxserver/heimdall stages the app at /app/www-tmp at build time; the s6 init
# copies /app/www-tmp -> /app/www at container startup. Our overlay therefore
# targets /app/www-tmp.
ARG UPSTREAM_TAG=v2.7.6-ls341
FROM lscr.io/linuxserver/heimdall:${UPSTREAM_TAG}

USER root

# jumbojett/openid-connect-php needs ext-sodium (token crypto); not in upstream image.
RUN apk add --no-cache php84-sodium

# linuxserver/heimdall doesn't ship composer; install it so we can pull in jumbojett.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app/www-tmp

# Add the OIDC client into Heimdall's vendor tree.
# --no-scripts: upstream's barryvdh/ide-helper post-install hook needs an .env at
# build time and we don't have one (it's created by the s6 init at runtime).
# Don't strip dev deps: AppServiceProvider conditionally registers IdeHelperServiceProvider
# when APP_ENV=local (Heimdall's default), so the dev package must remain present at runtime.
RUN composer require jumbojett/openid-connect-php:^1.0 --no-interaction --no-progress --no-scripts

# Layer our overlay files on top of the stock app.
COPY overlay/app/                                /app/www-tmp/app/
COPY overlay/config/services.php                 /app/www-tmp/config/services.php
COPY overlay/routes/web.php                      /app/www-tmp/routes/web.php
COPY overlay/resources/views/auth/login.blade.php /app/www-tmp/resources/views/auth/login.blade.php
COPY overlay/resources/views/layouts/app.blade.php /app/www-tmp/resources/views/layouts/app.blade.php

# Clear any stale bootstrap caches so our config/routes changes take effect.
RUN rm -f /app/www-tmp/bootstrap/cache/config.php \
          /app/www-tmp/bootstrap/cache/routes-v7.php \
          /app/www-tmp/bootstrap/cache/services.php

# Restore ownership for the runtime user used by linuxserver init.
RUN chown -R abc:abc /app/www-tmp
