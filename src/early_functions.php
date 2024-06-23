<?php

//  Aeppelkaka, a program which can help a stundent learning facts.
//  Copyright (C) 2003, 2006, 2021, 2022, 2023, 2024 Christian von Schultz
//
//  Permission is hereby granted, free of charge, to any person
//  obtaining a copy of this software and associated documentation
//  files (the “Software”), to deal in the Software without
//  restriction, including without limitation the rights to use, copy,
//  modify, merge, publish, distribute, sublicense, and/or sell copies
//  of the Software, and to permit persons to whom the Software is
//  furnished to do so, subject to the following conditions:
//
//  The above copyright notice and this permission notice shall be
//  included in all copies or substantial portions of the Software.
//
//  THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND,
//  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
//  MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
//  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
//  BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
//  ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
//  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
//  SOFTWARE.
//
// SPDX-License-Identifier: MIT


// This file contains functions that must be available early; that is,
// before load_config(), because they are used by load_config(). They
// really belong in backend.php, but backend.php needs
// load_config()...


// This function returns the translated message if the language files
// have been loaded, otherwise the English original.
function lang($message)
{
    global $l;

    if (is_array($l)) {
        if (empty($l[$message])) {
            return $message;
        } else {
            return $l[$message];
        }
    } else {
        return $message;
    }
}

function early_error($message)
{
    if (function_exists('error')) {
        error(lang($message));
        exit;
    } else {
        if (!headers_sent()) {
            header("Content-type: text/plain; Charset=UTF-8");
        }
        echo lang($message);
        exit;
    }
}

//   resource get_db();
//            Gets a database resource identifier, creating one if necessary
function get_db()
{
    global $c, $B;
    if (is_array($B) && array_key_exists("database", $B)) {
        return $B["database"];
    } else {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $B["database"] = new mysqli($c["mysql host"], $c["mysql user"], $c["mysql password"]);
        if ($B["database"]->connect_errno) {
            early_error("Could not connect to database.");
            exit;
        }
        if (!$B["database"]->set_charset("utf8mb4")) {
            early_error("Could not set charset to UTF-8.");
            exit;
        }
        if (!$B["database"]->select_db($c["mysql database name"])) {
            early_error("Could not select database.");
            exit;
        }
        return $B["database"];
    }
}

//            Tells us wheter a user ($_COOKIE["user_id"]) has logged in.
//            As a side effect, it starts the session if needed.
function is_logged_in()
{
    global $c;

    if (empty($_COOKIE['PHPSESSID'])) {
        @session_start();
        return false;
    }

    if (!isset($_SESSION)) {
        session_start();
    }

    $db = get_db();
    $stmt = $db->prepare(
        "SELECT 1 AS is_logged_in FROM sessions " .
        "WHERE user_id=? AND session_id=? " .
        "AND NOW() < session_last_active + INTERVAL ? MINUTE " .
        "AND NOW() < session_start + INTERVAL ? MINUTE"
    );
    $session_id = session_id();
    $stmt->bind_param(
        "isii",
        $_COOKIE['user_id'],
        $session_id,
        $c['max inactive time'],
        $c['max session length']
    );

    $stmt->execute();

    $stmt->bind_result($is_logged_in);

    if (!$stmt->fetch()) {
        $is_logged_in = 0;
    }

    $stmt->close();
    return $is_logged_in == 1;
}
