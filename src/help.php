<?php

//  Aeppelkaka, a program which can help a student learning facts.
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
unset($html);
unset($l);
require_once("config.php");
load_config();
require_once("html.php");
require_once("help_" . $c["lang"] . ".php");

ob_start();

echo "<h1>" . $l["Help for Aeppelkaka"] . "</h1>\n";
echo $l["What is Aeppelkaka"];
echo $l["Browser requirements"];

$body = ob_get_clean();

$url['this'] = 'help';

$smarty = get_smarty();
$smarty->assign('title', $l["Help for Aeppelkaka"]);
$smarty->assign('relative_url', "help");
$smarty->assign('body', $body);

$smarty->assign('l', $l);
$smarty->assign('url', $url);
do_http_headers();
$smarty->display('layout.tpl');
