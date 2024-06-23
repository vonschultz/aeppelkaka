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

$l["page title %s"] = "Remove card from %s";
$l["remove above"] = "Remove the card shown above";
$l["really remove %s?"] = wordwrap(
    "Do you really want to delete the " .
    "card %s, which is shown below?"
);
$l["really remove above"] = "Yes, do remove this card";
$l["removed %s"] = "The card %s has been removed.";
$l["search"] = "Search for a card";
$l["search intro"] = wordwrap(
    "To remove a card, you must first find it. " .
    "Enter your search criteria below. You can " .
    "leave one or more fields empty. Doing the " .
    "search can take some time, so please be " .
    "patient after submiting your search."
);
$l["card id"] = "The ID of the card:";
$l["cardfront"] = "Front side of card:";
$l["cardback"] = "Back side of card:";
$l["reset"] = "Reset";
$l["submit"] = "Submit search";
$l["No cards were found"] = wordwrap(
    "No cards were found. Try again with " .
    "different search criteria."
);
$l["Cards found"] = "Some cards were found";

$l["cards found, search at bottom of page"] = wordwrap(
    "One or more cards have been found. If you wish to delete " .
    "any of them, please click the appropriate button. To change " .
    "your search criteria, please see the bottom of the page."
);

$l["all deleted"] = "It seems all cards have been deleted.";

$l["search new"] = "Search among new cards.";
$l["Card ID %s"] = "Card %s";
$l["search learned"] = "Search among expired/learned cards.";
$l["mysql fulltext"] = "Search on whole words and make certain exceptions (fastest)";
