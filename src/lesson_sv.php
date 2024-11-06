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

$l["page title %s"] = "Lektion %s";
$l["lesson %s"] = "Lektion %s";
$l["lesson properties for %s"] = "Egenskaper för lektion: %s";
$l["Change properties for %s"] = "Ändra egenskaper för \"%s\"";
$l["lesson name:"] = "Lektionens namn:";
$l["lesson filename:"] = "Namn i URL:er:";
$l["submit changes"] = "Genomför ändringar";
$l["reset"] = "Återställ";
$l["properties changing text"] = wordwrap(
    "<p>Här nedan kan du ändra lektionens namn " .
    "och vad den kallar sig i URL:er (det du " .
    "ser i adressraden i din webbläsare). I " .
    "URL:er bör man hålla sig till engelska " .
    "tecken. Du kan också byta repetitionsalgoritm " .
    "så att du blir förhörd oftare eller mer sällan, " .
    "efter vad som passar kortens svårighetsgrad " .
    "bäst.</p>"
) . "\n\n";
$l["Remove %s"] = "Ta bort lektionen \"%s\"";
$l["remove lesson text"] = wordwrap(
    "<p>Varning: att ta bort en lektion innebär att " .
    "lektionen, och alla kort i den, permanent tas " .
    "bort. Det går inte att ångra ett sådant beslut. " .
    "Tänk dig för innan du bestämmer dig för detta! " .
    "%s</p>"
) . "\n\n";

$l["changed ln and lfn"] = "<p>Ändrade lektionens namn och vad den kallar sig i URL:er.</p>\n";
$l["changed ln"] = "<p>Ändrade lektionens namn (men inte vad den kallar sig i URL:er).</p>\n";
$l["changed lfn"] = "<p>Ändrade vad lektionen kallar sig i URL:er.</p>\n";
$l["changed lra"] = "<p>Bytte repetitionsalgoritm.</p>\n";
$l["lesson filename must be ASCII"] = (
    "Det lektionen kallar sig i URL:er måste hålla " .
    "sig inom ramarna för vanliga engelska tecken."
);
$l["lesson filename not unique"] = (
    "Det lektionen kallar sig i URL:er får inte kunna " .
    "förväxlas med någon annan lektion eller någon fil " .
    "i systemet."
);

$l["number of cards:"] = "Antal kort: ";
$l["number of new cards:"] = "Antal nya kort:";
$l["number of expired cards:"] = "Antal åldrade kort:";
$l["number of learned cards:"] = "Antal ordentligt inlärda kort:";
$l["number of new tomorrow cards:"] = "Antal kort bordlagda tills imorgon:";
$l["list label"] = "Du kan nu:";
$l["Add card (%url)"] = "<a href=\"%s\">Lägga till nya kort</a>";
$l["Learn new (%url)"] = "<a href=\"%s\">Lära dig de nya korten</a>";
$l["Test expired (%url)"] = (
    "<a href=\"%s\">Testa om du fortfarande " .
    "kan de åldrade korten</a>"
);
$l["Test newly learnt (%url)"] = (
    "<a href=\"%s\">Testa nyligen inlärda kort</a> igen (frivilligt).");
$l["Remove a card (%url)"] = "<a href=\"%s\">Ta bort ett eller flera kort</a>";
$l["See graph (%url)"] = (
    "Se ett <a href=\"%s\">diagram</a> som visar " .
    "när kort föråldras, och hur <q>gamla</q> de " .
    "kommer att vara när de föråldras."
);
$l["See forget percentage (%url)"] = (
    "Se hur många <a href=\"%s\">procent</a> av " .
    "korten med en viss ålder som brukar glömmas bort."
);
$l["Change lesson properties (%url)"] = (
    "Ändra <a href=\"%s\">lektionens egenskaper</a> eller ta bort den."
);

$l["repetition algorithm"] = "Repetitionsalgoritm:";
$l["lesson repetition algorithms"] = array(
    2 => "2&#8319;: hyfsat svårt att komma ihåg",
    3 => "3&#8319;: normalsvårt att komma ihåg",
    4 => "4&#8319;: lätt att komma ihåg"
);

$l['is sharing with you.'] = "delar med sig av sina kort till dig.";
$l['Import cards'] = "Importera kort.";

$l["Enable Aeppelchess plugin"] = "Aktivera schackfunktioner";
$l["Chessboard width:"] = "Schackbrädets storlek i pixlar";
