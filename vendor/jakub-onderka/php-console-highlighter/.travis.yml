language: php

php:
  - 5.3.3
  - 5.4
  - 5.5
  - 5.6
  - 7.0
  - hhvm
  - hhvm-nightly

matrix:
  allowed_failures:
    - php: 7.0
    - php: hhvm-nightly

before_script:
  - composer install --no-interaction --prefer-source

script:
  - ant