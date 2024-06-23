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

$l["page title %s"] = "Learn cards in %s";
$l["lesson %s"] = "Lesson %s";
$l["got %s seconds"] = "Learn the following card. You have %s seconds.";
$l["go on to %s"] = "Go on to %s.";
$l["next card"] = "the next card";
$l["learn cards in %s"] = "Learn cards in &ldquo;%s&rdquo;";
$l["too slow"] = (
    "You have now used more than 15 minutes to learn the " .
    "cards. It is now time to test if you still know them. " .
    "Remaining cards (if any) can be dealt with after that. " .
    "Proceed to <input type=\"submit\" tabindex=\"1\" " .
    "value=\"the test\"/>."
);
$l["learned everything"] = (
    "You have now learned all the new cards. " .
    "They have been put in your short-term " .
    "memory, and now it is time to put them " .
    "in the long-term memory. This is done " .
    "by testing if you now know them. Go to " .
    "<input type=\"submit\" tabindex=\"1\" " .
    "value=\"the test page\"/>."
);

$l["done"] = (
    "The cards you knew have now been put in your long term " .
    "memory. They will expire tomorrow, so please check back " .
    "then."
);
