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

unset($c);
unset($b);  // backend: lesson-dependent variables
unset($B);  // backend: variables independent of current lesson
unset($html);
unset($l);
unset($index_php);
require_once("config.php");
load_config();
require_once("backend.php");
require_once("html.php");
require_once("index_" . $c["lang"] . ".php");

forget_short_term_cards();

$smarty = get_smarty();
$db = get_db();
$stmt = $db->prepare(
    "SELECT lesson_name, lesson_filename, " .
    "(SELECT COUNT(card_id) FROM `lesson2cards` " .
    "  WHERE lesson2cards.lesson_id = lessons.lesson_id " .
    "  AND expires IS NULL) AS new, " .
    "(SELECT COUNT(card_id) FROM `lesson2cards` " .
    "  WHERE lesson2cards.lesson_id = lessons.lesson_id " .
    "  AND expires <= CURDATE()) AS expired, " .
    "(SELECT COUNT(card_id) FROM `lesson2cards` " .
    "  WHERE lesson2cards.lesson_id = lessons.lesson_id " .
    "  AND expires > CURDATE()) AS learned, " .
    "(SELECT COUNT(card_id) FROM `lesson2cards` " .
    "WHERE lesson2cards.lesson_id = lessons.lesson_id) AS total " .
    "FROM lessons where user_id=? " .
    "ORDER BY lesson_name"
);
$stmt->bind_param("i", $_COOKIE["user_id"]);
$stmt->execute();
$result = $stmt->get_result();

$total_new = 0;
$total_expired = 0;
$total_learned = 0;
$user_total_number_of_cards = 0;
while ($row = $result->fetch_object()) {
    $row->url = urlencode($row->lesson_filename) . "/";
    $smarty->append('lessons', $row);
    $total_new += $row->new;
    $total_expired += $row->expired;
    $total_learned += $row->learned;
    $user_total_number_of_cards += $row->total;
}
$smarty->assign('total_new', $total_new);
$smarty->assign('total_expired', $total_expired);
$smarty->assign('total_learned', $total_learned);
$smarty->assign('user_total_number_of_cards', $user_total_number_of_cards);

$result->free_result();

$url['this'] = $url['listoflessons'];
$smarty->assign('name', $c['name']);
$smarty->assign('l', $l);
$smarty->assign('url', $url);
do_http_headers();
$smarty->display('index.tpl');
