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

$l["Help for Aeppelkaka"] = "Manual för Aeppelkaka";

$l["What is Aeppelkaka"] = "<h2>Vad är Aeppelkaka?</h2>\n" .
    wordwrap(
        "<p>Aeppelkaka är ett program som kan hjälpa dig att lära dig " .
        "olika saker. Programmet använder virtuella <em>kort</em>. På " .
        "kortets framsida skriver du frågan, på baksidan svaret. När " .
        "du har skrivit in ett antal kort är det dags att börja lära " .
        "sig dem. Det går till så att varje kort först visas under 30 " .
        "sekunder. Under denna tid ska du försöka lära dig vad som står " .
        "på kortet. Efter att du tittat igenom alla korten, kommer du " .
        "till en testsida. Kortets framsida visas, men inte baksidan. " .
        "När du tror att du vet vad som stod där, skriver du in den " .
        "lilla rutan som finns strax nedanför och klickar att du vill " .
        "se baksidan. Programmet kommer då att kolla om det " .
        "du skrev in stämde med baksidan.</p>"
    ) . "\n\n" . wordwrap(
        "<p>Programmet lagrar alla kort. Efter en viss tid kommer de " .
        "att <em>föråldras</em>, och då är det dags att återigen testa " .
        "om du kommer ihåg kortet. Om så var fallet, lagras " .
        "det igen, lite längre den här gången. Om du glömmer ett kort, " .
        "kommer detta kort återigen att betraktas som ett nytt kort.</p>"
    ) . "\n\n";

$l["Browser requirements"] = "<h2>Krav på webläsaren</h2>\n" .
    wordwrap(
        "<p>För att använda Aeppelkakan måste du ha en rätt så ny " .
        "webläsare. Jag har inte skrivit programmet för någon särskild " .
        "läsare, utan hållit mig till gällande webstandarder. Alla " .
        "läsare som stödjer dessa webteknologier ska fungera. Dessa " .
        "teknologier är XHTML 1.1, Cascading Style Sheets (CSS), " .
        "Portable Network Graphics (PNG), UTF-8 och JavaScript. " .
        "Mozilla 1.0 och senare har testats och skall fungera, " .
        "vilket även läsare som baserar sig på Mozilla skall göra.</p>"
    ) . "\n\n";
