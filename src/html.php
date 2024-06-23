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

$html["has_begun"] = false;
function begin_html()
{
    global $c, $l, $html;

    if (!headers_sent()) {
        do_http_headers();
    }

    echo "<!DOCTYPE html>\n";
    echo "<html lang=\"";
    echo $c["lang"] . "\">\n\n";
    $html["has_begun"] = true;
}

function end_html()
{
    echo "</html>\n";
}

function add_stylesheet($filename, $stylename, $alternate = false)
{
    global $html;
    $html["stylesheets"][] = array($filename, $stylename, $alternate);
}

function add_script($filename)
{
    global $html;
    $html["scripts"][] = $filename;
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

function head($title, $relative_url)
{
    global $html, $c;

    $stylesheets = isset($html["stylesheets"]) ? $html["stylesheets"] : array();
    $scripts = isset($html["scripts"]) ? $html["scripts"] : array();

    echo "<head>\n";
    echo "  <meta charset=\"utf-8\" />\n";
    echo ("  <meta name=\"viewport\" content=\""
          . "width=device-width, "
          . "height=device-height, "
          . "user-scalable=no, "
          . "initial-scale=1, "
          . "target-densitydpi=device-dpi, "
          . "maximum-scale=1.0"
          . "\" />\n");

    foreach ($stylesheets as $style) {
        echo "  <link href=\"" .
            nice_url($style[0]) .
            "\" rel=\"stylesheet\" type=\"text/css\"/>\n";
    }

    foreach ($scripts as $key => $script) {
        echo "  <script type=\"text/javascript\" src=\"";
        echo nice_url($script);
        echo "\" ></script>\n";
    }

    // The ereg_replace might be good if relative references is used
    // in the code, and it also makes it look nicer to someone viewing
    // the html source code.
    echo "  <base href=\"";
    echo nice_url($c["webdir"] . "/" . $relative_url);
    echo "\"/>\n";
    echo "  <title>" . htmlspecialchars($title) . "</title>\n";
    echo "</head>\n\n";
    $html["has_head"] = true;
}

function menu_item($alt, $url, $title, $img_url = "")
{
    global $html;
    $html["menu"][] = array(
        "alt" => $alt,
        "url" => $url,
        "title" => $title,
        "img_url" => $img_url
    );
}

function body($focus = "")
{
    global $html;

    if (empty($focus)) {
        echo "<body>\n\n";
    } else {
        echo "<body onload=\"document.getElementById('" . htmlentities($focus) .
            "').focus()\">\n\n";
    }

    echo "<div id=\"content\">\n";

    if (!empty($html["menu"])) {
        echo "<table id=\"menu\">\n";
        foreach ($html["menu"] as $item) {
            if (empty($item["img_url"])) {
                echo "  <tr class=\"menuline\">\n";
                echo "    <td class=\"menuitem\">";
                echo "<a class=\"menulink\" href=\"" .
                    htmlspecialchars($item["url"]) . "\" ";
                echo "title=\"" . $item["title"] . "\">";
                echo $item["alt"];
                echo "</a></td>\n  </tr>\n";
            }
        }
        echo "</table>\n\n";
    }
    $html["has_body"] = true;
}

function end_body()
{
    echo "</div>\n";
    echo "</body>\n\n";
}

function error($message, $end = true)
{
    global $html, $l, $c;
    if (empty($html["has_begun"])) {
        begin_html();
    }
    if (empty($html["has_head"])) {
        add_stylesheet(nice_url($c["webdir"] . "/" . $c["manifest"]["main.css"]), "");
        head($l["Error"], ".");
    }
    if (empty($html["has_body"])) {
        body();
    }
    echo "<div class=\"error\"><p>" . $message . "</p></div>\n\n";
    if ($end) {
        end_body();
        end_html();
        exit;
    }
}

function print_card($cardfront, $cardback, $backvisible = true)
{
    global $l;
    echo "<p class=\"cardfronttitle\">" . $l["Front"] . "</p>\n";
    echo "<div class=\"cardfront\"><p>" . $cardfront . "</p></div>\n";
    echo "<p class=\"cardbacktitle\">" . $l["Back"] . "</p>\n";

    if ($backvisible) {
        echo "<div id=\"cardback\" class=\"cardback\"><p>";
    } else {
        echo "<div id=\"cardback\" class=\"cardback black\"><p>";
    }

    echo  $cardback . "</p></div>\n";

    if (!$backvisible) {
        $inputs = (
            '<input autocomplete="off" id="testinput" type="text" ' .
            'style="width: 100%; font-family: monospace; font-size: smaller"/>'
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

    paragraph($l["test if you know"]);

    list($cardfront, $cardback) = get_card($card_id);
    print_card($cardfront, $cardback, false); // back visible = false

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


    begin_form("removecard");
    echo "<p id=\"removepara\">";
    hidden("card", $card_id);
    echo "<input type=\"submit\" value=\"" . $l["Remove this card"] . "\"/>";
    echo "</p>\n\n";
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

    error(
        "An exception occurred. This is typically due to an error in the program. " .
        ($mailed ?
         "The webmaster has been contacted." :
         "The webmaster has <em>not</em> been contacted. Please contact him and say " .
         "precisely what you were doing that caused the error.")
    );
}

set_exception_handler("exception_handler");
