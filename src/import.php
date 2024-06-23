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
require_once("config.php");
load_config();
require_once("backend.php");
require_once("html.php");
require_once("import_" . $c["lang"] . ".php");
$lesson = $_REQUEST['lesson'];
$from_lesson_id = $_REQUEST['from_lesson_id'];
$offset = array_key_exists('offset', $_REQUEST) ? (int) $_REQUEST['offset'] : 0;

assert_lesson($lesson);
forget_short_term_cards();

header('Content-type: text/plain; charset=UTF-8');

$url['this'] = $url['lesson']['import_from_lesson'] . $from_lesson_id;
$smarty = get_smarty();

$db = get_db();

$stmt = $db->prepare(
    "SELECT 1 AS is_sharing FROM sharing " .
    "WHERE to_user_id=? AND to_lesson_id=? " .
    "AND from_lesson_id=?"
);
$stmt->bind_param("iii", $_COOKIE['user_id'], $b['lesson'], $from_lesson_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    $result->close();
    error("Permission denied");
    exit;
}

$row = $result->fetch_object();
$result->close();

if (!is_object($row) || $row->is_sharing != 1) {
    error("Permission denied");
    exit;
}

if (!empty($_POST['importcards']) && is_array($_POST['importcards'])) {
    $bound_params = [];
    $bound_param_types = "";
    $x = array();
    foreach ($_POST['importcards'] as $card_id) {
        $x[] = "card_id = ?";
        $bound_params[] = $card_id;
        $bound_param_types .= "i";
    }
    $stmt = $db->prepare(sprintf(
        "SELECT COUNT(DISTINCT card_id) AS count FROM lesson2cards " .
        "WHERE lesson_id = ? AND (%s)",
        implode(" OR ", $x)
    ));
    $stmt->bind_param(
        "i" . $bound_param_types,
        $from_lesson_id,
        ...$bound_params
    );
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_object();
    $result->close();
    if (count($_POST['importcards']) != $row->count) {
        error("Desired cards not found");
        mailerror(
            "Suspicious attempt to import cards that " .
            "aren't all shared or do not exist. Request:\n" .
            var_export($_REQUEST, true)
        );
        exit;
    }

    $stmt = $db->prepare(sprintf(
        "INSERT INTO lesson2cards " .
        "(lesson_id, card_id, expires, created, forgotten, remembered) " .
        "SELECT ?, card_id, NULL, NOW(), 0, 0 " .
        "FROM lesson2cards WHERE lesson_id=? AND (%s)",
        implode(" OR ", $x)
    ));
    $stmt->bind_param(
        "ii" . $bound_param_types,
        $b['lesson'],
        $from_lesson_id,
        ...$bound_params
    );
    $stmt->execute();

    $stmt = $db->prepare(sprintf(
        "SELECT card_id, cardfront, cardback " .
        "FROM lesson2cards JOIN cards USING (card_id) " .
        "WHERE lesson_id=? AND (%s)",
        implode(" OR ", $x)
    ));
    $stmt->bind_param("i" . $bound_param_types, $b['lesson'], ...$bound_params);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $smarty->append('cards', $row);
    }

    $result->close();

    $smarty->assign('l', $l);
    $smarty->assign('url', $url);
    do_http_headers();
    $smarty->display('imported.tpl');
} else {
    $cardfrontwhere = $cardbackwhere = "";
    $bound_params = [$from_lesson_id, $b['lesson']];
    $bound_param_types = "ii";
    if (!empty($_POST['cardfrontsearch'])) {
        $cardfrontwhere = "AND cardfront LIKE ? ";
        $bound_params[] = "%" . $_POST['cardfrontsearch'] . "%";
        $bound_param_types .= "s";
        $smarty->assign('cardfrontsearch', $_POST['cardfrontsearch']);
    }
    if (!empty($_POST['cardbacksearch'])) {
        $cardbackwhere = "AND cardback LIKE ? ";
        $bound_params[] = "%" . $_POST['cardbacksearch'] . "%";
        $bound_param_types .= "s";
        $smarty->assign('cardbacksearch', $_POST['cardbacksearch']);
    }
    $stmt = $db->prepare(
        "SELECT card_id, cardfront, cardback " .
        "FROM lesson2cards AS from_table JOIN cards USING (card_id) " .
        "WHERE lesson_id=? AND NOT EXISTS (" .
        "  SELECT * FROM lesson2cards AS to_table " .
        "  WHERE to_table.card_id = from_table.card_id " .
        "  AND lesson_id=?) $cardfrontwhere $cardbackwhere " .
        "ORDER BY real_created LIMIT ?, 30"
    );
    $bound_params[] = $offset;
    $bound_param_types .= "i";
    $stmt->bind_param($bound_param_types, ...$bound_params);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $smarty->append('cards', $row);
    }

    $result->close();

    $smarty->assign('offset', $offset);
    $smarty->assign('l', $l);
    $smarty->assign('url', $url);
    do_http_headers();
    $smarty->display('import.tpl');
}
