<?php

//  Aeppelkaka, a program which can help a stundent learning facts.
//  Copyright (C) 2003, 2006, 2022, 2023, 2024 Christian von Schultz
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

$l["page title %s"] = "Lesson %s";
$l["lesson %s"] = "Lesson %s";
$l["lesson properties for %s"] = "Properties for lesson: %s";
$l["Change properties for %s"] = "Change properties for \"%s\"";
$l["lesson name:"] = "Name of the lesson:";
$l["lesson filename:"] = "Name in URLs:";
$l["submit changes"] = "Submit changes";
$l["reset"] = "Reset";
$l["properties changing text"] = wordwrap(
    "<p>Here you can change the name of the " .
    "lesson, and the name used in URLs (what " .
    "you see in your browser's address bar). " .
    "In URLs you avoid the use of certain " .
    "characters, e.g. umlauts and spaces. You " .
    "can also change the repetition algorithm " .
    "to question you more often or less often, " .
    "depending on the difficulty of the cards " .
    "in this lesson.</p>"
) . "\n\n";
$l["Remove %s"] = "Remove lesson: \"%s\"";
$l["remove lesson text"] = wordwrap(
    "<p>Warning: removing a lesson means that the " .
    "lesson, including all cards in it, will be permanently " .
    "and irrevocably deleted. There is no way to " .
    "recover a deleted lesson or its cards. %s</p>"
) . "\n\n";

$l["changed ln and lfn"] = (
    "<p>Changed the name of the lesson, and the name used in URLs.</p>\n"
);
$l["changed ln"] = (
    "<p>Changed the name of the lesson (but not the name appearing in URLs).</p>\n"
);
$l["changed lfn"] = "<p>Changed the name appearing in URLs.</p>\n";
$l["changed lra"] = "<p>Changed repetition algorithm.</p>\n";
$l["lesson filename must be ASCII"] = (
    "The name used in URLs must not " .
    "contain strange characters."
);
$l["lesson filename not unique"] = (
    "The name used in URLs must not coincide with any " .
    "other lesson or file in the system."
);

$l["number of cards:"] = "Total number of cards:";
$l["number of new cards:"] = "Number of new cards:";
$l["number of expired cards:"] = "Number of expired cards:";
$l["number of learned cards:"] = "Number of learned cards:";
$l["number of new tomorrow cards:"] = "Number of new cards postponed to tomorrow:";
$l["list label"] = "You may now:";
$l["Add card (%url)"] = "<a href=\"%s\">Add a new card</a>";
$l["Learn new (%url)"] = "<a href=\"%s\">Learn the new cards</a>";
$l["Park new (%url)"] = "<a href=\"%s\">Postpone the new cards and do them tomorrow instead</a>";
$l["Unpark new (%url)"] = "<a href=\"%s\">Revive postponed cards and do them today instead</a>";
$l["Test expired (%url)"] = (
    "<a href=\"%s\">Test if you sill " .
    "know the expired cards</a>"
);
$l["Test newly learnt (%url)"] = (
    "(Optionally) <a href=\"%s\">test the newly learned cards</a> again."
);
$l["Remove a card (%url)"] = "<a href=\"%s\">Remove one or more cards</a>";
$l["See graph (%url)"] = (
    "See a <a href=\"%s\">graph</a> showing " .
    "when cards will expire, and how <q>old</q> " .
    "they will be when they expire."
);
$l["See forget percentage (%url)"] = (
    "See how many <a href=\"%s\">percent</a> of the cards of a " .
    "given age are normally forgotten."
);
$l["Change lesson properties (%url)"] = (
    "Change <a href=\"%s\">lesson properties</a> or remove the lesson."
);

$l["repetition algorithm"] = "Repetition algorithm:";
$l["lesson repetition algorithms"] = array(
    2 => "2&#8319;: rather difficult to remember",
    3 => "3&#8319;: normal difficulty",
    4 => "4&#8319;: easily remembered"
);

$l['is sharing with you.'] = "is sharing some cards with you.";
$l['Import cards'] = "Import cards.";
