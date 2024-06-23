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


// You must include config.php before including this file.
require_once("menu_" . $c["lang"] . ".php");

$l["Help for Aeppelkaka"] = "Help for Aeppelkaka";

$l["What is Aeppelkaka"] = "<h2>What is Aeppelkaka?</h2>\n" .
    wordwrap(
        "<p>Aeppelkaka is a program that can assist " .
        "you with learning. It works using virtual <em>cards</em>. You can " .
        "write a question on the front side, and the answer on the back of " .
        "the card. When you have entered a number of cards, you can start "  .
        "learning them. First, each card is shown during 30 seconds. Then "  .
        "you can test yourself: the front is displayed and you should try "  .
        "to remember the other side. When you think you know what was written " .
        "there, you take a look at the back side and tell the program whether " .
        "you knew what stood there.</p>"
    ) . "\n\n" . wordwrap(
        "<p>The cards are stored by the program. After some time they will " .
        "expire, and you will once again do the test. If you knew the card, " .
        "it will be stored again, a little longer this time. If you have " .
        "forgotten, the card will once again be treated as new.</p>"
    ) . "\n\n";

$l["Browser requirements"] = "<h2>Browser requirements</h2>\n" .
    wordwrap(
        "<p>To use Aeppelkaka you need a fairly new web browser. I have not " .
        "written the program for any particular browser, but kept " .
        "to the web standards. Any browser supporting the web technologies " .
        "involved should work. The web technologies are XHTML 1.1, Cascading " .
        "Style Sheets (CSS), Portable Network Graphics (PNG), UTF-8 and JavaScript. " .
        "Mozilla 1.0 and later is known to work, browsers based on Mozilla " .
        "technology should work.</p>"
    ) . "\n\n";
