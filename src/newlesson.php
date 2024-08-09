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
require_once("newlesson_" . $c["lang"] . ".php");
$newlesson = $_POST['newlesson'] ?? null;

function assert_lesson_not_empty($lesson)
{
    global $l;
    if (empty($lesson)) {
        error_page($l["Lesson empty"], relative_url: 'newlesson');
        exit;
    }
}

//* void main(void), so to speak

assert_lesson_not_empty($newlesson);
$lesson_filename = lessonuser2lessonfilename($newlesson);

if (!new_lesson($newlesson, $lesson_filename)) {
    error_page(
        sprintf($l["failure lesson %s"], $newlesson),
        relative_url: 'newlesson'
    );
    exit;
}

ob_start();

echo "<h1>" . $l["success"] . "</h1>\n";
paragraph(sprintf(
    $l["lesson %s created"],
    (
        "<a href=\"" .
        nice_url($c["webdir"] . "/" . urlencode($lesson_filename)) .
        "\">" . htmlspecialchars($newlesson) . "</a>"
    )
));

$body = ob_get_clean();

$url['this'] = 'newlesson';

$smarty = get_smarty();
$smarty->assign('title', $l["page title"]);
$smarty->assign('relative_url', 'newlesson');
$smarty->assign('body', $body);

$smarty->assign('l', $l);
$smarty->assign('url', $url);
do_http_headers();
$smarty->display('layout.tpl');
