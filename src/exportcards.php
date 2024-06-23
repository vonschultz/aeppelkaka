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
$lesson = $_REQUEST['lesson'];

assert_lesson($lesson);
forget_short_term_cards();

function get_cards()
{
    global $b, $l, $mysqlfulltext;

    $db = get_db();
    $stmt = $db->prepare(
        "SELECT * FROM cards JOIN lesson2cards " .
        "ON lesson2cards.card_id = cards.card_id " .
        "WHERE lesson_id=?"
    );
    $stmt->bind_param("i", $b["lesson"]);
    $stmt->execute();
    $result = $stmt->get_result();

    $r = array();

    while ($row = $result->fetch_assoc()) {
        $r[] = $row;
    }

    return $r;
}

//* void main(void), so to speak

if (set_lesson($lesson)) {
    if (read_card_directory() == 0) {
        header("Location: " . nice_url($c["webdir"] . "/" . $lesson));
        exit;
    }
} else {
    header("Location: ..");
    exit;
}

header('Content-Type: application/json;charset=UTF-8');
echo json_encode(get_cards());
