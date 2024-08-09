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
$remembered = $_REQUEST['remembered'] ?? null;
$card = $_REQUEST['card'] ?? null;
$lesson = $_REQUEST['lesson'];

assert_lesson($lesson);

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
        $card_id = $_SESSION['tomorrowcards'][$random_key];
        testform($card_id, "newlylearnt");
        unset($_SESSION['tomorrowcards'][$random_key]);
        return $card_id;
    }
    paragraph($l["done"]);
    return null;
}


//* void main(void), so to speak

ob_start();

if (empty($_SESSION['tomorrowcards']) && empty($card) && empty($remembered)) {
    read_newly_learnt();
}

echo "<h1>" . sprintf($l["test cards in %s"], lesson_user()) . "</h1>\n\n";

$card_id = test_newly_learnt();

//debug();

$body = ob_get_clean();

$url = path_join_urls('..', $url);
$url['this'] = 'newlylearnt';
$url['thislesson'] = './';

if (!empty($card_id)) {
    $url['card'] = array(
        'removecard' => sprintf('removecard/card=%d', $card_id)
    );
}

$smarty = get_smarty();
$smarty->assign('focus_element', 'testinput');
$smarty->assign('title', sprintf($l["page title %s"], lesson_user()));
$smarty->assign('relative_url', urlencode(lesson_filename()) . "/newlylearnt");
$smarty->assign('lesson_name', lesson_user());
$smarty->assign('card_id', $card_id);
$smarty->assign('body', $body);

$smarty->assign('l', $l);
$smarty->assign('url', $url);
do_http_headers();
$smarty->display('layout.tpl');
