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

$l["Error"] = "Error";
$l["Front"] = "The front side:";
$l["Back"] = "The back side:";

// The testform things (valid XHTML again, please)
$l["test if you know card %d"] = (
    "Try to remember the other side of the card. " .
    "When you think you know what was written there, " .
    "you look at <a tabindex=\"1\" href=\"javascript:" .
    "showCardback(%1\$d);\" id=\"show_card_%1\$d\">the back side</a>."
);
//// %s is $l["yes"] and $l["no"]
//$l["did you know? %s or %s"] = ("Did you remember the back side? %s or %s.");
$l["yes"] = "Yes";
$l["no"] = "No";

$l["you knew (if not %s) %s"] = (
    "I think you knew this (if you did not, " .
    "click %s). You knew? %s"
);
$l["here"] = "here";

$l["you didn't know (otherwise %s) %s"] = (
    "I don't think you knew this (if you did, click %s) " .
    "You didn't know? %s"
);

$l["I did not remember"] = "No, I did'nt";
$l["I did remember"] = "Yes, I did remember";

$l["Remove this card"] = "Remove this card";
