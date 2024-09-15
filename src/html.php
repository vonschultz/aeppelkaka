<?php

//  Aeppelkaka, a program which can help a stundent learning facts.
//  Copyright (C) 2003, 2006, 2021, 2022, 2023, 2024 Christian von Schultz
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


// This file includes the html functions. It should be included in the
// main files.

// Please note that that this file only contains functions, and no
// code is executed by including this file.

require_once("html_" . $c["lang"] . ".php");


function do_http_headers()
{
    global $c, $l, $html;

    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Script-Type: text/javascript');

    // Try to disable caching...
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    // always modified
    header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");                          // HTTP/1.0
}


function hidden($name, $value)
{
    echo (
        "<input type=\"hidden\" " .
        "name=\"" . htmlspecialchars($name) . "\" " .
        "id=\"" . htmlspecialchars($name) . "\" " .
        "value=\"" . htmlspecialchars($value) . "\" " .
        "/>"
    );
}

function inputtext($name, $default = true)
{
    printf(
        "<input type=\"text\" name=\"%1\$s\" id=\"%1\$s\" ",
        htmlspecialchars($name)
    );
    if (!empty($_REQUEST[$name]) && $default) {
        printf(
            "value=\"%s\" ",
            htmlspecialchars($_REQUEST[$name])
        );
    }
    echo "/>";
}

// FIXME: $stylename and $alternate do not work.

function nice_url($url)
{
    return htmlspecialchars(preg_replace("%([^:]/)/+%", "\\1", $url));
}

function error_page($message, $relative_url = '.')
{
    global $l, $url;
    $url['this'] = $relative_url;
    $smarty = get_smarty();
    $smarty->assign('title', $l['Error']);
    $smarty->assign('relative_url', $relative_url);
    $smarty->assign(
        'body',
        "<div class=\"error\"><p>" . $message . "</p></div>\n\n"
    );
    $smarty->assign('l', $l);
    $smarty->assign('url', $url);
    do_http_headers();
    $smarty->display('layout.tpl');
    exit;
}

function print_card($card_id, $cardfront, $cardback, $backvisible = true)
{
    global $l;
    echo "<p class=\"cardfronttitle\">" . $l["Front"] . "</p>\n";
    printf(
        "<div class=\"cardfront\" id=\"cardfront_%d\"><p>%s</p></div>\n",
        $card_id,
        $cardfront
    );
    echo "<p class=\"cardbacktitle\">" . $l["Back"] . "</p>\n";

    if ($backvisible) {
        printf("<div id=\"cardback_%d\" class=\"cardback\"><p>", $card_id);
    } else {
        printf("<div id=\"cardback_%d\" class=\"cardback black\"><p>", $card_id);
    }

    echo  $cardback . "</p></div>\n";

    if (!$backvisible) {
        $inputs = sprintf(
            '<input autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="testinput_%d" class="fake_password_input" type="password" ' .
            'style="width: 100%%; font-family: monospace; font-size: smaller"/>',
            $card_id
        );
        printf("<p>%s</p>\n", $inputs);
    }
}

function begin_form($url, $id = false)
{
    if ($id === false) {
        printf(
            "<form action=\"%s\" method=\"post\" accept-charset=\"UTF-8\">\n\n",
            htmlentities($url)
        );
    } else {
        printf(
            "<form action=\"%s\" id=\"%s\" method=\"post\" accept-charset=\"UTF-8\">\n\n",
            htmlentities($url),
            htmlentities($id)
        );
    }
}

function end_form()
{
    echo "</form>\n\n";
}

function paragraph($text)
{
    echo wordwrap("<p>" . $text . "</p>\n\n");
}

function testform($card_id, $target, $hiddens = array())
{
    global $l, $c;

    paragraph(sprintf($l["test if you know card %d"], $card_id));

    list($cardfront, $cardback) = get_card($card_id);
    print_card($card_id, $cardfront, $cardback, false); // back visible = false

    begin_form($target);
    hidden("card", $card_id);
    echo "<p id=\"goodquestion\" class=\"hidden\">";
    hidden("regex", cardback2regex($cardback));
    foreach ($hiddens as $key => $value) {
        hidden($key, $value);
    }
    echo wordwrap(sprintf(
        $l["you knew (if not %s) %s"],
        sprintf(
            "<a href=\"javascript: document.getElementById('wasbad')." .
            "style.setProperty('display', 'block', null);\" " .
            "tabindex=\"2\">%s</a>",
            $l["here"]
        ),
        sprintf(
            "<input type=\"submit\" tabindex=\"3\" " .
            "name=\"remembered\" value=\"%s\"/>",
            $l["yes"]
        )
    ));
    echo "</p>";
    echo "<p id=\"wasbad\" class=\"hidden\">";
    echo wordwrap(sprintf(
        "<input type=\"submit\" name=\"remembered\" value=\"%s\"/>",
        $l["I did not remember"]
    ));
    echo "</p>";

    echo "<p id=\"badquestion\" class=\"hidden\">";
    echo wordwrap(sprintf(
        $l["you didn't know (otherwise %s) %s"],
        sprintf(
            "<a href=\"javascript: document.getElementById('wasgood')." .
            "style.setProperty('display', 'block', null);\" " .
            "tabindex=\"2\">%s</a>",
            $l["here"]
        ),
        sprintf(
            "<input type=\"submit\" tabindex=\"3\" " .
            "name=\"remembered\" value=\"%s\"/>",
            $l["no"]
        )
    ));
    echo "</p>";
    echo "<p id=\"wasgood\" class=\"hidden\">";
    echo wordwrap(sprintf(
        "<input type=\"submit\" " .
        "name=\"remembered\" value=\"%s\"/>",
        $l["I did remember"]
    ));
    echo "</p>";

    end_form();
}

function exception_handler($exception)
{
    global $l, $b, $c, $html, $action;
    $mailed = mail(
        $c["webmaster mail"], // TO
        "Aeppelkaka exception in " . $exception->getFile() . ":" . $exception->getLine(), // Subject
        "Aeppelkaka failed to catch an exception. Catastrophe ensues.\n\n" .
        $exception->getMessage() . "\n\n" .
        $exception->getTraceAsString() . "\n\n" .
        // print_r($exception, true) . "\n\n" .  // Often, the $exception is way to big to print.
        "\$c[name]\n" . print_r(isset($c['name']) ? $c['name'] : null, true) . "\n\n" .
        "\$_SERVER\n" . print_r($_SERVER, true) . "\n\n",
        "From: " . $c["aeppelkaka mail"]
    );

    error_page(
        "An exception occurred. This is typically due to an error in the program. " .
        ($mailed ?
         "The webmaster has been contacted." :
         "The webmaster has <em>not</em> been contacted. Please contact him and say " .
         "precisely what you were doing that caused the error.")
    );
}

set_exception_handler("exception_handler");
