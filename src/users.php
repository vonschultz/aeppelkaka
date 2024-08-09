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
unset($users_php);
require_once("config.php");
load_config();
require_once("html.php");
require_once("backend.php");
require_once("users_" . $c["lang"] . ".php");
require_once("index_" . $c["lang"] . ".php");

if (!is_admin()) {
    error_page($l["Permission denied"]);
    exit;
}

$smarty = get_smarty();

$db = get_db();
$stmt = $db->prepare(
    "SELECT " .
    "u.user_id, u.username, u.full_name, " .
    "s.session_last_active, s.session_start, " .
    "COUNT(DISTINCT l.lesson_id) AS number_of_lessons, " .
    "COUNT(DISTINCT c.card_id) AS number_of_cards " .
    "FROM `sessions` AS `s` " .
    "RIGHT JOIN `users` AS `u` USING (user_id) " .
    "LEFT JOIN `lessons` AS `l` USING(user_id) " .
    "LEFT JOIN `lesson2cards` AS `c` USING(lesson_id) " .
    "GROUP BY `user_id`"
);
$stmt->execute();
$result = $stmt->get_result();
$total_number_of_lessons = 0;
$total_number_of_cards = 0;
while ($row = $result->fetch_object()) {
    $smarty->append('users', $row);
    $total_number_of_lessons += $row->number_of_lessons;
    $total_number_of_cards += $row->number_of_cards;
}
$result->close();
$smarty->assign('total_number_of_lessons', $total_number_of_lessons);
$smarty->assign('total_number_of_cards', $total_number_of_cards);

if (!empty($_REQUEST['user'])) {
    $smarty->assign('user_id', $_REQUEST['user']);
    $smarty->assign('display_user_lessons', true);
    $db = get_db();
    $stmt = $db->prepare(
        "SELECT lesson_name, " .
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
        "FROM lessons where user_id=?"
    );
    $stmt->bind_param("i", $_REQUEST['user']);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_new = 0;
    $total_expired = 0;
    $total_learned = 0;
    $user_total_number_of_cards = 0;
    while ($row = $result->fetch_object()) {
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

    $result->close();
}

$url['this'] = $url['userlist'];
$smarty->assign('l', $l);
$smarty->assign('url', $url);
do_http_headers();
$smarty->display('users.tpl');
