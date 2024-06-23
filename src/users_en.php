<?php

//  Aeppelkaka, a program which can help a stundent learning facts.
//  Copyright (C) 2003, 2006, 2022, 2024 Christian von Schultz
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


// You must include config.php first.
require_once("menu_" . $c["lang"] . ".php");
require_once("index_" . $c["lang"] . ".php");

$l["page title: users"] = "List of users";

$l["Users:"] = "We have now the following users:";
$l["Username"] = "Username";
$l["Full name"] = "Full name";
$l["Number of lessons"] = "Number of lessons";
$l["Number of cards"] = "Number of Cards";
$l["Session start"] = "Session started";
$l["Last activity"] = "Last activity";

$l["Number of users: %s"] = "All in all %s users.";

$l["No users in system"] = (
    "There are no users in the systemet. " .
    "(You don't exist. Go away.)'"
);

$l['hijack user'] = "Log in as this user";
