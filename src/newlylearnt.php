<?php

//  Aeppelkaka, a program which can help a stundent learning facts.
//  Copyright (C) 2003, 2006, 2011, 2021, 2022, 2023, 2024 Christian von Schultz
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

unset($c);
unset($b);  // backend: lesson-dependent variables
unset($B);  // backend: variables independent of current lesson
unset($html);
unset($l);
require_once("config.php");
load_config();
require_once("backend.php");
require_once("html.php");
require_once("testexpired_" . $c["lang"] . ".php");
$remembered = $_REQUEST['remembered'];
$card = $_REQUEST['card'];
$lesson = $_REQUEST['lesson'];

assert_lesson($lesson);

function add_menu_items()
{
    global $l;
    menu_item(
        lesson_user(),
        "./",
        sprintf($l["lesson %s"], lesson_user())
    );
    menu_item($l["Lessons"], "../", $l["Main page with lessons"]);
    menu_item($l["Setup"], "../setup", $l["Aeppelkaka settings"]);
    menu_item($l["Help"], "../help", $l["The Aeppelkaka manual"]);
    menu_item($l["Logout"], "../logout", $l["Logout of Aeppelkaka"]);
}

function read_newly_learnt()
{
    /* We read in the card_id of all the cards that were created today
     * and expire tomorrow — in other words, the words that the user has
     * learnt today. Those are the words that the user can safely repeat
     * when he feels like he has some time for additional revision. If
     * we allowed later words, it would not be obvious when the words
     * should expire next, but now we just let them expire tomorrow (if
     * remembered) or let the user re-learn them (if forgotten).
     */
    global $b;

    $_SESSION['tomorrowcards'] = array();

    $db = get_db();
    $stmt = $db->prepare(
        "SELECT card_id FROM lesson2cards " .
        "WHERE lesson_id=? AND created=? AND expires=?"
    );
    $now = now();
    $expires = date('Y-m-d', time() + 86400);
    $stmt->bind_param(
        "iss",
        $b["lesson"],
        $now,
        $expires
    );
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_object()) {
        $_SESSION['tomorrowcards'][] = $row->card_id;
    }
    $result->close();
}

function test_newly_learnt()
{
    global $card, $b, $l, $remembered;

    if (!empty($card) && ($remembered == $l["no"] || $remembered == $l["I did not remember"])) {
        $db = get_db();
        $stmt = $db->prepare(
            "UPDATE lesson2cards SET forgotten=forgotten+1, " .
            "created=?, expires=NULL " .
            "WHERE card_id=? AND lesson_id=?"
        );
        $now = now();
        $stmt->bind_param("sii", $now, $card, $b["lesson"]);
        $stmt->execute();
    }
    if (!empty($_SESSION['tomorrowcards'])) {
        $random_key = array_rand($_SESSION['tomorrowcards']);
        testform($_SESSION['tomorrowcards'][$random_key], "newlylearnt");
        unset($_SESSION['tomorrowcards'][$random_key]);
    } else {
        paragraph($l["done"]);
    }
}


//* void main(void), so to speak

begin_html();

add_menu_items();
add_stylesheet($c["webdir"] . "/" . $c["manifest"]["main.css"], "");

head(
    sprintf($l["page title %s"], lesson_user()),
    "/" . urlencode(lesson_filename()) . "/newlylearnt"
);

body("testinput");

if (empty($_SESSION['tomorrowcards']) && empty($card) && empty($remembered)) {
    read_newly_learnt();
}

echo "<h1>" . sprintf($l["test cards in %s"], lesson_user()) . "</h1>\n\n";

test_newly_learnt();

//debug();

end_body();

end_html();
