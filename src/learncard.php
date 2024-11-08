<?php

//  Aeppelkaka, a program which can help a stundent learning facts.
//  Copyright (C) 2003, 2006, 2020, 2021, 2022, 2023, 2024 Christian von Schultz
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
require_once("learncard_" . $c["lang"] . ".php");
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$remembered = isset($_REQUEST['remembered']) ? $_REQUEST['remembered'] : "";
$time_for_next_action = isset($_REQUEST['time_for_next_action']) ? $_REQUEST['time_for_next_action'] : "";
$lesson = isset($_REQUEST['lesson']) ? $_REQUEST['lesson'] : "";
$card = isset($_REQUEST['card']) ? $_REQUEST['card'] : "";


assert_lesson($lesson);

function debug()
{
    global $l, $b, $c, $html, $action;
    echo "<h1>\$b</h1>\n";
    echo "<pre>\n";
    print_r($b);
    echo "</pre>\n";

    echo "<h1>\$c</h1>\n<pre>\n";
    print_r($c);
    echo "</pre>\n";

    echo "<h1>\$html</h1>\n<pre>\n";
    print_r($html);
    echo "</pre>\n";

    echo "<h1>\$action</h1>\n<pre>\n";
    print_r($action);
    echo "\n</pre>\n";
}

function learnform($card)
{
    global $l, $c, $time_for_next_action;

    echo "<div id=\"hide30\">\n";
    paragraph(sprintf(
        $l["got %s seconds"],
        "<input type=\"text\" id=\"seconds\" value=\"30\"/>"
    ));

    list($cardfront, $cardback) = get_card($card->card_id);
    print_card($card->card_id, $cardfront, $cardback);
    echo "</div>\n";

    begin_form("learncard");
    echo "<p>";
    hidden("action", "new2short-term");
    hidden("time_for_next_action", $time_for_next_action);
    hidden("card", $card->card_id);
    echo sprintf(
        $l["go on to %s"],
        "<input type=\"submit\" tabindex=\"2\" value=\"" . $l["next card"] . "\"/>"
    );
    echo "</p>\n";

    end_form();

    return $card->card_id;
}

function set_time_for_next_action()
{
    global $time_for_next_action;

    // Never use more than 15 minutes for learning cards: you will
    // start to forget the first ones before you have learned the
    // last. After 15 minutes the user is forced to go on.
    if (empty($time_for_next_action)) {
        $time_for_next_action = time() + 15 * 60;
    }
}

// If the user has used more than 15 minutes, tell him he was too slow
// and has to go to the test page without having learned everything.

function check_time_for_next_action()
{
    global $l, $b, $time_for_next_action, $action;

    if ($action != "short-term2long-term") {
        if (time() > $time_for_next_action) {
            if (!empty($card)) {
                make_short_term($card);
            }
            echo "<form action=\"learncard\" method=\"post\" accept-charset=\"UTF-8\">\n";
            echo "<p>";
            hidden("action", "short-term2long-term");
            echo $l["too slow"] . "</p>\n";
            echo "</form>\n";
            return "stop";
        } else {
            return "continue";
        }
    } else {
        return "continue";
    }
}

function do_new2short_term()
{
    global $card, $b, $l;

    if (!empty($card)) {
        make_short_term($card);
    }
    if ($b["number of new cards"] != 0) {
        $card_id = learnform(reset($b["new cards"]));
    } else {
        echo "<form action=\"learncard\" method=\"post\" accept-charset=\"UTF-8\">\n";
        echo "<p>";
        hidden("action", "short-term2long-term");
        echo $l["learned everything"] . "</p>\n";
        echo "</form>\n";
    }

    return $card_id ?? null;
}

function short_term2long_term()
{
    global $card, $b, $l, $remembered;

    if (!empty($card)) {
        if ($remembered == $l["yes"] || $remembered == $l["I did remember"]) {
            make_long_term($card);
        } elseif ($remembered == $l["no"] || $remembered == $l["I did not remember"]) {
            make_new($card);
        }
    }
    if ($b["number of short term cards"] != 0) {
        srand(make_seed());
        shuffle($b["short term cards"]); // array in random order
        $card_id = reset($b["short term cards"])->card_id;
        testform(
            $card_id,
            "learncard",
            array("action" => "short-term2long-term")
        );
    } else {
        paragraph($l["done"]);
    }

    return $card_id ?? null;
}


//* void main(void), so to speak

set_time_for_next_action();

ob_start();

read_card_directory();

echo "<h1>" . sprintf($l["learn cards in %s"], lesson_user()) . "</h1>\n\n";

$card_id = null;

if (check_time_for_next_action() == "continue") {
    if ($action != "short-term2long-term") {
        $card_id = do_new2short_term();
    } else {
        $card_id = short_term2long_term();
    }
}

//debug();

$body = ob_get_clean();

if (!empty($card_id)) {
    assert_card($card_id);
}

$url = path_join_urls('..', $url);
$url['this'] = 'learncard';
$url['thislesson'] = './';

$smarty = get_smarty();
if ($action === "short-term2long-term") {
    $smarty->assign('focus_element', sprintf('testinput_%d', $card_id));
}
$smarty->assign('title', sprintf($l["page title %s"], lesson_user()));
$smarty->assign('relative_url', urlencode(lesson_filename()) . "/learncard");
$smarty->assign('lesson_name', lesson_user());
$smarty->assign('body', $body);

$smarty->assign('l', $l);
$smarty->assign('url', $url);
do_http_headers();
$smarty->display('layout.tpl');
