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


// You must include config.php before including this file
require_once("menu_" . $c["lang"] . ".php");

$l["Setup"] = "Setup";
$l["Submit"] = "Submit";
$l["Reset"] = "Reset";
$l["Username"] = "Username:";
$l["Change username"] = "Change username";

$l["Language"] = "Language:";
$l["Timezone"] = "Timezone:";
$l["E-mail"] = "E-mail:";
$l["City"] = "City:";
$l["Country"] = "Country:";


$l["prefers"] = "Preferred format for new cards:";
$l["Plain text"] = "Plain text";
$l["XHTML 1.1"] = "XHTML 1.1";

$l["Diagram height"] = "Diagram height in pixels:";
$l["Only digits in diaheight"] = "You must only use digits in the diagram height.";

$l["Old password"] = "Current password:";
$l["Password"] = "New password:";
$l["Verify password"] = "The new one again:";
$l["Change password"] = "Change password";

$l["Impossible error: wrong language selected"] = wordwrap(
    "Wrong choice of language. This should be impossible. " .
    "Please contact the <a href=\"mailto:" .
    $c["webmaster mail"] . "\">webmaster</a> and " .
    "explain what you did to make this happen."
);
$l["Impossible error: wrong timezone selected"] = wordwrap(
    "Wrong choice of timezone. This should be impossible. " .
    "Please contact the <a href=\"mailto:" .
    $c["webmaster mail"] . "\">webmaster</a> and " .
    "explain what you did to make this happen."
);
$l["This was saved:"] = "The following was saved:";

$l["Username %s not unique"] = "The username \"%s\" has already been taken. Sorry.";

$l["Empty passwords won't do."] = "Empty passwords won't do.";

$l["Passwords don't match"] = (
    "You have entered different things in the fields " .
    "for the new password."
);

$l["Old password incorrect"] = "You have not entered your old password correctly.";

$l["Password updated"] = "Your password has now been changed.";
