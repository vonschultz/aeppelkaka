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
require_once("lesson_" . $c["lang"] . ".php");

$l["page title %s"] = "Add cards to %s";
$l["lesson %s"] = "Lesson %s";
$l["add to %s"] = "Add cards to the lesson &ldquo;%s&rdquo;";
$l["intro paragraph"] = wordwrap(
    "<p>You may now add cards to the selected " .
    "lesson. Either you choose to enter plain " .
    "text and you will get what you see, or you " .
    "can use the entire feature set of XHTML 1.1 " .
    "and XML. If you choose to do this, please " .
    "remember that the card will be embedded in " .
    "a <code>&lt;p&gt;</code> element. You can set " .
    "your prefered mode in the <a href=\"../setup\" " .
    "title=\"" . $l["Aeppelkaka settings"] . "\">" .
    "setup page</a>.</p>"
) . "\n\n" . wordwrap(
    "<p>You can specify the repetition algorithm on " .
    "a per lesson basis (see <a href=\"properties\">" .
    "the lesson properties</a>), but if a card differs " .
    "in difficulty from others in this lesson, you can " .
    "choose repetition algorithm here.</p>"
) . "\n\n";

$l["Front"] = "Front side of the card:";
$l["Back"] = "Back side of the card:";
$l["entered is"] = "The entered card is to be treated as:";
$l["Plain text"] = "Plain text";
$l["XHTML 1.1"] = "XHTML 1.1";
$l["submit"] = "Add card";
$l["No cardfront"] = "You have not entered any front side of the card.";
$l["No cardback"] = "You have not entered any back side of the card.";
$l["Card added. Number of new cards: %s"] = "The card was added. Number of new cards: %s";
$l["use default algorithm"] = "Use lesson default";
