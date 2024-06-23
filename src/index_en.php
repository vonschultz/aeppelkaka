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

require_once("config.php");
require_once("menu_" . $c["lang"] . ".php");

$l["page title"] = "Aeppelkaka";
$l["welcome"] = "Welcome to Aeppelkaka, %s!";
$l["No lessons"] = "No lessons have been created yet.";
$l["Lessons:"] = "Select one of the following lessons:";
$l["Lesson"] = "Lesson";
$l["lessonid"] = "Lesson";
$l["New"] = "New";
$l["newid"] = "New";
$l["Expired"] = "Expired";
$l["expiredid"] = "Expired";
$l["Learned"] = "Learned";
$l["learnedid"] = "Learned";
$l["Total"] = "Total";
$l["totalid"] = "Total";
$l["All in all"] = "All in all";
$l["<p>intro</p>"] = (
    "<p>This is Aeppelkaka, a card based system for " .
    "learning facts. If you would like to learn more \n" .
    "about the system, please read the <a href=\"help\" " .
    "title=\"Help for Aeppelkaka\">manual</a>.</p>\n\n"
);
$l["Enter new lesson name"] = (
    "If you would like to create a new lesson, " .
    "you can do so by entering the name here: "
);
$l["Submit"] = "Submit";
