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

$l["page title %s"] = "Lär dig korten i %s";
$l["lesson %s"] = "Lektion %s";
$l["got %s seconds"] = "Lär dig följande kort. Du har %s sekunder på dig.";
$l["go on to %s"] = "Fortsätt till %s.";
$l["next card"] = "nästa kort";
$l["learn cards in %s"] = "Lär dig korten i <q>%s</q>";
$l["too slow"] = (
    "Du har nu använt mer än 15 minuter för att lära dig " .
    "korten. Det är nu dags att testa om du fortfarande " .
    "kommer ihåg dem. Om det återstår några kort som du " .
    "ännu ej har lärt dig, kan du återkomma till dem senare. " .
    "Fortsätt till <input type=\"submit\" tabindex=\"1\" " .
    "value=\"testet\"/>."
);
$l["learned everything"] = (
    "Du har nu lärt dig alla de nya korten. " .
    "De har placerats i ditt korttidsminne, " .
    "och nu är det dags att flytta dem till " .
    "ditt långtidsminne. Detta görs genom att " .
    "testa om du fortfarande kommer ihåg dem. " .
    "Fortsätt till " .
    "<input type=\"submit\" tabindex=\"1\" " .
    "value=\"testsidan\"/>."
);
$l["done"] = (
    "De kort som du fortfarande kunde har nu flyttats till " .
    "ditt långtidsminne. De kommer att föråldras i morgon, " .
    "vilket betyder att du bör komma tillbaka då för att " .
    "repetera."
);
