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
require_once("lesson_" . $c["lang"] . ".php");
$lesson = $_REQUEST['lesson'];

//* void main(void), so to speak

assert_lesson($lesson);

ob_start();

echo "<h1>" . sprintf($l["lesson %s"], lesson_user()) . "</h1>\n\n";

$db = get_db();
$stmt = $db->prepare(
    "SELECT `cardback`, DATEDIFF(`expires`, `created`) AS expirationage " .
    "FROM `cards` JOIN `lesson2cards` USING(`card_id`) " .
    "WHERE lesson_id=? ORDER BY `cardback`"
);
$stmt->bind_param("i", $b["lesson"]);
$stmt->execute();
$result = $stmt->get_result();
echo "<table class=\"main\">\n";
echo "  <tr><th class=\"numbers\"><code>expirationage</code></th><th><code>cardback</code></th></tr>\n";
while ($row = $result->fetch_assoc()) {
    printf(
        "  <tr><td class=\"numbers\" style=\"padding-right: 7mm\"><code>%s</code></td><td><code>%s</code></td></tr>\n",
        htmlspecialchars(is_null($row["expirationage"]) ? "null" : $row["expirationage"]),
        htmlspecialchars($row["cardback"])
    );
}
echo "</table>\n";
$result->close();

$body = ob_get_clean();

$url = path_join_urls('..', $url);
$url['this'] = 'list_cardbacks';
$url['thislesson'] = './';


$smarty = get_smarty();

$smarty->assign('title', sprintf($l["page title %s"], lesson_user()));
$smarty->assign('relative_url', urlencode(lesson_filename()) . "/list_cardbacks");
$smarty->assign('lesson_name', lesson_user());
$smarty->assign('body', $body);

$smarty->assign('l', $l);
$smarty->assign('url', $url);
do_http_headers();
$smarty->display('layout.tpl');
