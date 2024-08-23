<?php

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

function sodium_encode($plaintext, $sodium_key)
{
    global $c;

    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $ciphertext = sodium_crypto_secretbox(
        $plaintext,
        $nonce,
        sodium_hex2bin($sodium_key)
    );
    return $nonce . $ciphertext;
}

$randomizer = new \Random\Randomizer();
$characters_for_use_in_passwords = (
    '0123456789' .
    'abcdefghijklmnopqrstuvwxyz' .
    'ABCDEFGHIJKLMNOPQRSTUVWXYZ' .
    '!#%&()*+,-./<=>?@[]^_`{|}~'
);

$sodium_key = sodium_bin2hex(sodium_crypto_secretbox_keygen());
$mysql_password = $randomizer->getBytesFromString(
    $characters_for_use_in_passwords,
    32
);
$mysql_root_password = $randomizer->getBytesFromString(
    $characters_for_use_in_passwords,
    32
);
$admin_user_password = $randomizer->getBytesFromString(
    $characters_for_use_in_passwords,
    32
);
$encoded_admin_user_password = '0x' . sodium_bin2hex(sodium_encode(
    plaintext: password_hash($admin_user_password, PASSWORD_DEFAULT),
    sodium_key: $sodium_key
));
$user_password = $randomizer->getBytesFromString(
    $characters_for_use_in_passwords,
    32
);
$encoded_user_password = '0x' . sodium_bin2hex(sodium_encode(
    plaintext: password_hash($user_password, PASSWORD_DEFAULT),
    sodium_key: $sodium_key
));
$insert_users_sql = <<<END_OF_SQL
    INSERT INTO `users` (
        `user_id`,
        `username`,
        `password_hash`,
        `password_inner_hash_algo`,
        `full_name`,
        `lang`,
        `timezone`,
        `cardformat`,
        `graphheight`,
        `email`,
        `city`,
        `country`,
        `mobile_phone`,
        `joinedus`
    ) VALUES
    (
        1,
        'a.admin',
        $encoded_admin_user_password,
        '',
        'Alfred Adminsson',
        'en',
        'Europe/Stockholm',
        'application/xhtml+xml',
        550,
        'a.admin@example.com',
        'Gotham',
        'Low Earth Orbit',
        '0000 00 00 00',
        '2006-08-07 17:28:11'
    ),
    (
        2,
        'b.user',
        $encoded_user_password,
        '',
        'Bertil Usersson',
        'sv',
        'Europe/Stockholm',
        'application/xhtml+xml',
        700,
        'b.user@example.com',
        'Amundsen-Scott',
        'Antarctica',
        '0000 00 00 00',
        '2016-12-03 10:18:13'
    );
    END_OF_SQL;

file_put_contents(
    filename: "/secrets/sodium_key",
    data: $sodium_key
);
file_put_contents(
    filename: "/secrets/mysql_password",
    data: $mysql_password
);
file_put_contents(
    filename: "/secrets/mysql_root_password",
    data: $mysql_root_password
);
file_put_contents(
    filename: "/secrets/admin_user_password",
    data: $admin_user_password
);
file_put_contents(
    filename: "/secrets/user_password",
    data: $user_password
);
file_put_contents(
    filename: "/secrets/insert_users.sql",
    data: $insert_users_sql
);
