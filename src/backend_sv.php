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

$l["No lesson exists!"] = wordwrap(
    "Ingen giltig lektion har valts. " .
    "Det här borde vara omöjligt!"
);

$l["error code %s: %s"] = "XML-fel %s: %s";

$l["card not added"] = (
    "Kortet lades ej till på grund av fel. Var god redigera " .
    "kortet och försök igen."
);

$l["Could not find card with ID %s"] = "Kunde inte hitta kortet med ID \"%s\".";

$l["Could not connect to database."] = (
    "Kunde inte ansluta till databasen. " .
    "Var god försök igen senare."
);
$l["Could not select database."] = ("Kunde inte välja databasen.");

$l["Database error: could not read user table"] = (
    "Databasfel: kunde inte läsa användartabellen."
);
