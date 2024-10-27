#!/bin/bash

#  Aeppelkaka, a program which can help a stundent learning facts.
#  Copyright (C) 2024 Christian von Schultz
#
#  Permission is hereby granted, free of charge, to any person
#  obtaining a copy of this software and associated documentation
#  files (the “Software”), to deal in the Software without
#  restriction, including without limitation the rights to use, copy,
#  modify, merge, publish, distribute, sublicense, and/or sell copies
#  of the Software, and to permit persons to whom the Software is
#  furnished to do so, subject to the following conditions:
#
#  The above copyright notice and this permission notice shall be
#  included in all copies or substantial portions of the Software.
#
#  THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND,
#  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
#  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
#  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
#  BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
#  ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
#  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
#  SOFTWARE.
#
# SPDX-License-Identifier: MIT

set -euxo pipefail

function secret_envsubst()
{
    set +x  # We don't want to echo the password to the console
    MYSQL_PASSWORD=$(</secrets/mysql_password) \
    SODIUM_KEY=$(</secrets/sodium_key) \
    envsubst '$MYSQL_HOST
              $MYSQL_USER
              $MYSQL_PASSWORD
              $MYSQL_DATABASE_NAME
              $SODIUM_KEY'
}

npm install
npm run start
cp .htaccess "$PWD/dist/"
find src -maxdepth 1 -mindepth 1 \
     '(' \
     -name '*schema.json' -o \
     -name '*.php' -not -name config.php \
     ')' \
     -execdir cp -v '{}' "$PWD/dist/" ';'
envsubst '
    $ADMIN_USER_ID
    $AEPPELKAKA_MAIL
    $URL_AUTHORITY
    $URL_SCHEME
    $WEBMASTER_MAIL
    ' < src/config.php | secret_envsubst > "dist/config.php"
mkdir dist/smarty
mkdir dist/smarty/templates
cp src/smarty/templates/*.tpl dist/smarty/templates/
mkdir dist/smarty/templates_c
chmod 0777 dist/smarty/templates_c
