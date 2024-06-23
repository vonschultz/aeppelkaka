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

$l["page title %s"] = "Ta bort kort från %s";
$l["remove above"] = "Radera ovan visade kort";
$l["really remove %s?"] = wordwrap(
    "Vill du verkligen radera kortet " .
    "%s, som visas nedan?"
);
$l["really remove above"] = "Ja, ta bort detta kort.";
$l["removed %s"] = "Kortet %s har tagits bort.";
$l["search"] = "Sök efter ett kort";
$l["search intro"] = wordwrap(
    "För att ta bort ett kort måste man först " .
    "hitta det. Skriv in vad du vill söka efter " .
    "nedan. Du kan lämna ett eller fler fält " .
    "tomma. Att genomföra sökningen kan ta lite " .
    "tid, så ha tålamod efter att du skickar " .
    "iväg din sökning."
);
$l["card id"] = "Kortets ID:";
$l["cardfront"] = "Kortets framsida:";
$l["cardback"] = "Kortets baksida:";
$l["reset"] = "Återställ";
$l["submit"] = "Sök";
$l["No cards were found"] = wordwrap(
    "Inga kort hittades. Försök igen med " .
    "andra sökkriterier."
);
$l["Cards found"] = "Kort hittades";

$l["cards found, search at bottom of page"] = wordwrap(
    "Ett eller flera kort har hittats. Om du önskar radera något " .
    "av dem, klickar du bara på motsvarande knapp. För att ändra " .
    "sökkriterier, gå till slutet av sidan."
);

$l["all deleted"] = "Det verkar som alla kort tagits bort.";

$l["search new"] = "Sök bland nya kort.";

$l["search learned"] = "Sök bland åldrade/inlärda kort.";
$l["Card ID %s"] = "Kort %s";
$l["mysql fulltext"] = "Sök på hela ord och gör vissa undantag (snabbast).";
