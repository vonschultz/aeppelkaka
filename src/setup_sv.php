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

$l["Submit"] = "Skicka";
$l["Reset"] = "Återställ";
$l["Username"] = "Användarnamn:";
$l["Change username"] = "Byt användarnamn";

$l["Language"] = "Språkval:";
$l["Timezone"] = "Tidszon:";
$l["E-mail"] = "E-postadress:";
$l["City"] = "Stad:";
$l["Country"] = "Land:";

$l["prefers"] = "Föredraget format för nya kort:";
$l["Plain text"] = "Vanlig text";
$l["XHTML 1.1"] = "XHTML 1.1";

$l["Diagram height"] = "Diagrammets höjd i bildpunkter (pixlar):";
$l["Only digits in diaheight"] = "Du får bara använda siffror när du uttrycker diagramhöjden.";

$l["Old password"] = "Nuvarande lösenord:";
$l["Password"] = "Nytt lösenord:";
$l["Verify password"] = "En gång till:";
$l["Change password"] = "Byt lösenord";

$l["Impossible error: wrong language selected"] = wordwrap(
    "Felaktigt språkval. Detta borde vara omöjligt. " .
    "Kontakta gärna <a href=\"mailto:" .
    $c["webmaster mail"] . "\">webmaster</a> och " .
    "berätta hur detta gick till."
);
$l["Impossible error: wrong timezone selected"] = wordwrap(
    "Felaktigt val av tidszon. Detta borde vara omöjligt. " .
    "Kontakta gärna <a href=\"mailto:" .
    $c["webmaster mail"] . "\">webmaster</a> och " .
    "berätta hur detta gick till."
);
$l["This was saved:"] = "Följande sparades:";

$l["Username %s not unique"] = "Tyvärr. Användarnamnet \"%s\" är upptaget.";

$l["Empty passwords won't do."] = "Tomma lösenord duger inte.";

$l["Passwords don't match"] = "Du har skrivit in olika saker i fälten för nytt lösenord.";

$l["Old password incorrect"] = "Det där var inte ditt gamla lösenord. Försök inte med mig.";

$l["Password updated"] = "Du har nu bytt lösenord.";
