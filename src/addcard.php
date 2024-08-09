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
require_once("addcard_" . $c["lang"] . ".php");
$cardfront = $_POST['cardfront'] ?? null;
$cardback = $_POST['cardback'] ?? null;
$type = $_POST['type'] ?? null;
$lesson = $_REQUEST['lesson'] ?? null;
$repetition_algorithm = $_POST['repetition_algorithm'] ?? null;

assert_lesson($lesson);

function debug()
{
    global $l, $b, $c, $html;
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
}

function form()
{
    global $l, $c, $cardfront, $cardback;
    echo "<form action=\"addcard\" method=\"post\" accept-charset=\"UTF-8\">\n";

    // If the user forgot to enter either $cardfront or $cardback, we
    // supply the already entered one as default vaule.
    echo "<p>" . $l["Front"] . "<br/>\n";
    echo "<textarea name=\"cardfront\" id=\"cardfront\" rows=\"7\" cols=\"50\">";
    echo htmlspecialchars($cardfront ?? "") . "</textarea></p>\n\n";

    echo "<p>" . $l["Back"] . "<br/>\n";
    echo "<textarea name=\"cardback\" rows=\"7\" cols=\"50\">";
    echo htmlspecialchars($cardback) . "</textarea></p>\n\n";

    echo "<table>\n";
    echo "  <tr>\n";
    echo "    <td id=\"head\">" . $l["entered is"] . "</td>\n";
    echo "    <td headers=\"head\">\n";
    echo "      <input type=\"radio\" name=\"type\" value=\"text/plain\"";
    if ($c["prefers"] != "application/xhtml+xml") {
        echo " checked=\"checked\"";
    }
    echo "/>\n";
    echo "      " . $l["Plain text"] . "<br/>\n";
    echo "      <input type=\"radio\" name=\"type\" value=\"application/xhtml+xml\"";
    if ($c["prefers"] == "application/xhtml+xml") {
        echo " checked=\"checked\"";
    }
    echo "/>\n";
    echo "      " . $l["XHTML 1.1"] . "\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["repetition algorithm"] . "</td>\n";
    echo "    <td>\n";
    echo "      <select name=\"repetition_algorithm\">\n";
    echo "        <option value=\"lesson\" selected=\"selected\">";
    echo $l["use default algorithm"] . "</option>\n";
    foreach ($l["lesson repetition algorithms"] as $algorithm_number => $description) {
        echo "        <option value=\"$algorithm_number\">" . $description . "</option>\n";
    }
    echo "      </select>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "</table>\n\n";

    paragraph("<input type=\"submit\" value=\"" . $l["submit"] . "\"/>");

    echo "</form>\n";
}

// Creates a string suitable for xml validation. We include the
// doctype to define all the entities.
function test_xml_string($cardside)
{
    $r = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1 plus MathML 2.0//EN\"\n";
    $r .= "     \"http://www.w3.org/Math/DTD/mathml2/xhtml-math11-f.dtd\">\n";
    $r .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
    $r .= "<head><title></title></head><body><div><p>" . $cardside;
    $r .= "</p></div></body></html>";
    return $r;
}


function add_card()
{
    global $l, $b, $c, $type, $cardfront, $cardback, $repetition_algorithm;

    if (empty($cardfront) && !empty($cardback)) {
        echo "<div class=\"error\"><p>" . $l["No cardfront"] . "</p></div>\n";
    } elseif (!empty($cardfront) && empty($cardback)) {
        echo "<div class=\"error\"><p>" . $l["No cardback"] . "</p></div>\n";
    } else {
        if ($type != "application/xhtml+xml") {
            $cardfront = htmlspecialchars($cardfront);
            $cardfront = str_replace("\r\n", "\n", $cardfront);
            $cardfront = str_replace("\n", "<br/>\n", $cardfront);

            $cardback = htmlspecialchars($cardback);
            $cardback = str_replace("\r\n", "\n", $cardback);
            $cardback = str_replace("\n", "<br/>\n", $cardback);
        }
        // If the card is plain text, or well-formed XHTML, add the card.
        if (
            ($type != "application/xhtml+xml") ||
            (wellformed_xml(test_xml_string($cardfront)) &&
             wellformed_xml(test_xml_string($cardback)))
        ) {
            make_card($cardfront, $cardback, $repetition_algorithm);
            // We read_card_directory() after having added the card, naturally.
            read_card_directory();
            paragraph(sprintf($l["Card added. Number of new cards: %s"], $b["number of new cards"]));

            // Reset, so that the card does not appear in the form, which is about
            // to be printed when this function exist.
            $cardfront = "";
            $cardback = "";
        } else {
            echo "<div class=\"error\"><p>" . xml_error_message() . "</p></div>\n";
            // $carfront and $cardback are not reset, so that the user gets to
            // edit them and make them well-formed. The card is, of course, not added.
        }
    }
}

//* void main(void), so to speak

ob_start();

echo "<h1>" . sprintf($l["add to %s"], lesson_user()) . "</h1>\n\n";

if (!empty($type) && !empty($cardfront) && !empty($cardback)) {
    add_card();
}

echo $l["intro paragraph"];

form();

// debug();

$body = ob_get_clean();

$url = path_join_urls('..', $url);
$url['this'] = 'learncard';
$url['thislesson'] = './';

if (!empty($card_id)) {
    $url['card'] = array(
        'removecard' => sprintf('removecard/card=%d', $card_id)
    );
}

$smarty = get_smarty();
$smarty->assign('title', sprintf($l["page title %s"], lesson_user()));
$smarty->assign('relative_url', urlencode(lesson_filename()) . "/addcard");
$smarty->assign('lesson_name', lesson_user());
$smarty->assign('body', $body);
$smarty->assign('l', $l);
$smarty->assign('url', $url);

do_http_headers();
$smarty->display('layout.tpl');
