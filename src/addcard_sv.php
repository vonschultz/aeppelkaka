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

$l["page title %s"] = "Lägg till kort till %s";
$l["lesson %s"] = "Lektion %s";
$l["add to %s"] = "Lägg till kort till lektionen <q>%s</q>";
$l["intro paragraph"] = wordwrap(
    "<p>Du kan nu lägga till kort till den valda lektionen. Antingen kan " .
    "du välja att skriva in kortet som vanlig (ren) text och du får det du " .
    "ser, eller så kan du använda de i det närmaste obegränsade möjligheter " .
    "som XHTML 1.1 och XML ger dig. Väljer du att göra detta skall du komma " .
    "ihåg att kortet läggs i ett <code>&lt;p&gt;</code>-element. Du kan " .
    "ställa in vad du föredrar på <a href=\"../setup\" title=\"" .
    $l["Aeppelkaka settings"] . "\">inställningssidan</a>.</p>"
) . "\n\n" . wordwrap(
    "<p>Repetitionsalgoritmen " .
    "kan du ställa in på lektionsnivå (se <a " .
    "href=\"properties\">lektionens egenskaper</a>), " .
    "men om detta kort avviker i svårighetsgrad kan du ställa " .
    "in det här.</p>"
) . "\n\n";

$l["Front"] = "Kortets framsida:";
$l["Back"] = "Kortets baksida:";
$l["entered is"] = "Det inskrivna kortet skall behandlas som:";
$l["Plain text"] = "Vanlig text";
$l["XHTML 1.1"] = "XHTML 1.1";
$l["submit"] = "Lägg till kort";
$l["No cardfront"] = "Du har inte skrivit in någon framsida till kortet.";
$l["No cardback"] = "Du har inte skrivit in någon baksida till kortet.";
$l["Card added. Number of new cards: %s"] = "Kortet har lagts till. Antal nya kort: %s";
$l["use default algorithm"] = "Den som valts för lektionen";
