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
$remembered = isset($_REQUEST['remembered']) ? $_REQUEST['remembered'] : null;
$card = isset($_REQUEST['card']) ? $_REQUEST['card'] : null;
$lesson = isset($_REQUEST['lesson']) ? $_REQUEST['lesson'] : null;
$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : false;

assert_lesson($lesson);

function debug()
{
    global $l, $b, $c, $html, $action;
    mail(
        $c["webmaster mail"], // TO
        "testexpired.php debugging information", // Subject
        "testexpired.php debugging information\n\n" .
        "\$_REQUEST\n" . print_r($_REQUEST, true) . "\n\n" .
        //          "\$b\n" . print_r($b, true) . "\n\n" .
        "\$c[name]\n" . print_r($c['name'], true) . "\n\n" .
        "\$html\n" . print_r($html, true) . "\n\n" .
        "\$_SERVER\n" . print_r($_SERVER, true) . "\n\n",
        $c['extra mail headers']
    );
}

function test_expired()
{
    global $card, $b, $l, $remembered;

    if (!empty($card)) {
        if ($remembered == $l["yes"] || $remembered == $l["I did remember"]) {
            unexpire($card);
        } elseif ($remembered == $l["no"] || $remembered == $l["I did not remember"]) {
            make_new($card, true);            // true = increment the count of forgotten cards.
        }
    }
    if ($b["number of expired cards"] != 0) {
        $random_key = array_rand($b["expired cards"]);
        $card_id = $b["expired cards"][$random_key]->card_id;
        testform($card_id, "testexpired");
    } else {
        paragraph($l["done"]);
    }
    return $card_id ?? null;
}


//* void main(void), so to speak

ob_start();

read_card_directory();

echo "<h1>" . sprintf($l["test cards in %s"], lesson_user()) . "</h1>\n\n";

$card_id = test_expired();

if ($debug) {
    debug();
}

$body = ob_get_clean();

$url = path_join_urls('..', $url);
$url['this'] = 'learncard';
$url['thislesson'] = './';

if (!empty($card_id)) {
    $url['card'] = array(
        'removecard' => sprintf('removecard/card=%d', $card_id)
    );
}

$smarty = get_smarty();
$smarty->assign('focus_element', sprintf('testinput_%d', $card_id));
$smarty->assign('title', sprintf($l["page title %s"], lesson_user()));
$smarty->assign('relative_url', urlencode(lesson_filename()) . "/testexpired");
$smarty->assign('lesson_name', lesson_user());
$smarty->assign('card_id', $card_id);
$smarty->assign('body', $body);
$smarty->assign('l', $l);
$smarty->assign('url', $url);
do_http_headers();
$smarty->display('layout.tpl');
