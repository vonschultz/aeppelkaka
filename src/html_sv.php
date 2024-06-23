<?php

//  Aeppelkaka, a program which can help a stundent learning facts.
//  Copyright (C) 2003, 2006, 2021, 2022, 2024 Christian von Schultz
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

$l["Error"] = "Fel";
$l["Front"] = "Framsidan: ";
$l["Back"] = "Baksidan:";

// The testform things (valid XHTML again, please)
$l["test if you know"] = (
    "Försök komma ihåg vad som står på kortets andra sida. " .
    "När du tror du vet vad som stod där, tar du en titt " .
    "på <a tabindex=\"1\" href=\"javascript:showCardback();\">baksidan</a>."
);
//// %s is $l["yes"] and $l["no"]
//$l["did you know? %s or %s"] = ("Kunde du komma ihåg baksidan? %s eller %s.");
$l["yes"] = "Ja";
$l["no"] = "Nej";

$l["you knew (if not %s) %s"] = (
    "Jag tror du kunde detta (om inte, " .
    "klicka %s). Du kunde? %s"
);
$l["here"] = "här";

$l["you didn't know (otherwise %s) %s"] = (
    "Jag tror inte du kunde detta (gjorde du det, klicka %s) " .
    "Du kunde inte? %s"
);

$l["I did not remember"] = "Nej, jag kunde inte alls";
$l["I did remember"] = "Jo, jag kunde visst";

$l["Remove this card"] = "Ta bort detta kort";
