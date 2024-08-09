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
require_once("removecard_" . $c["lang"] . ".php");
$lesson = $_REQUEST['lesson'];
$card = isset($_REQUEST["card"]) ? $_REQUEST["card"] : null;
$really = isset($_REQUEST["really"]) ? $_REQUEST["really"] : null;
$card_id = isset($_REQUEST["card_id"]) ? $_REQUEST["card_id"] : null;
$cardfront = isset($_REQUEST["cardfront"]) ? $_REQUEST["cardfront"] : null;
$cardback = isset($_REQUEST["cardback"]) ? $_REQUEST["cardback"] : null;
$searchnew = isset($_REQUEST["searchnew"]) ? $_REQUEST["searchnew"] : null;
$searchlearned = isset($_REQUEST["searchlearned"]) ? $_REQUEST["searchlearned"] : null;
$mysqlfulltext = isset($_REQUEST["mysqlfulltext"]) ? $_REQUEST["mysqlfulltext"] : null;

forget_short_term_cards();

function debug()
{
    global $l, $b, $c, $html, $action;
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

function show_card($card_id)
{
    global $l;
    begin_form("removecard");

    echo "<h3>" . sprintf($l["Card ID %s"], $card_id) . "</h3>\n\n";

    list($cardfront, $cardback) = get_card($card_id);
    print_card($cardfront, $cardback);

    echo "<p>";
    hidden("card", $card_id);
    echo "<input type=\"submit\" value=\"" . $l["remove above"] . "\"/>";
    echo "</p>\n\n";

    end_form();
}

function confirm_remove($card_id)
{
    global $l;

    begin_form("removecard");

    paragraph(sprintf($l["really remove %s?"], $card_id));

    list($cardfront, $cardback) = get_card($card_id);
    print_card($cardfront, $cardback);

    echo "<p>";
    hidden("card", $card_id);
    hidden("really", "yes");
    echo "<input type=\"submit\" value=\"" . $l["really remove above"] . "\"/>";
    echo "</p>\n\n";

    end_form();
}

function do_card($card_id, $really)
{
    global $l;
    if ($really == "yes") {
        remove_card($card_id);
        paragraph(sprintf($l["removed %s"], $card_id));
        if (no_cards()) {
            return "no cards";
        } else {
            return "continue";
        }
    } else {
        confirm_remove($card_id);
        return "abort";
    }
}

function search_form()
{
    global $l, $card_id, $cardfront, $cardback, $searchnew, $searchlearned, $mysqlfulltext;
    begin_form("removecard");

    echo "<h2>" . $l["search"] . "</h2>\n\n";

    paragraph($l["search intro"]);

    echo "<table>\n";

    echo "  <tr>\n";
    echo "    <td id=\"card_id\">" . $l["card id"] . "</td>\n";
    echo "    <td headers=\"card_id\">";
    echo "<input type=\"text\" name=\"card_id\" value=\"";
    echo htmlspecialchars($card_id ?? "") . "\"/></td>\n";
    echo "  </tr>\n";

    echo "  <tr>\n";
    echo "    <td id=\"cardfront\">" . $l["cardfront"] . "</td>\n";
    echo "    <td headers=\"cardfront\">";
    echo "<input type=\"text\" name=\"cardfront\" value=\"";
    echo htmlspecialchars($cardfront ?? "") . "\"/></td>\n";
    echo "  </tr>\n";

    echo "  <tr>\n";
    echo "    <td id=\"cardback\">" . $l["cardback"] . "</td>\n";
    echo "    <td headers=\"cardback\">";
    echo "<input type=\"text\" name=\"cardback\" value=\"";
    echo htmlspecialchars($cardback ?? "") . "\"/></td>\n";
    echo "  </tr>\n";

    echo "  <tr>\n";
    echo "    <td colspan=\"2\"><input type=\"checkbox\" ";
    echo "name=\"searchnew\" ";

    if (
        $searchnew == "yes" ||
        (empty($card_id) && empty($cardback) && empty($cardfront))
    ) {
        echo "checked=\"checked\" ";
    }

    echo "value=\"yes\"/>";

    echo " " . $l["search new"] . "</td>\n";
    echo "  </tr>";

    echo "  <tr>\n";
    echo "    <td colspan=\"2\"><input type=\"checkbox\" ";
    echo "name=\"searchlearned\" ";

    if (
        $searchlearned == "yes" ||
        (empty($card_id) && empty($cardback) && empty($cardfront))
    ) {
        echo "checked=\"checked\" ";
    }

    echo "value=\"yes\"/>";
    echo " " . $l["search learned"] . "</td>\n";
    echo "  </tr>";

    echo "  <tr>\n";
    echo "    <td colspan=\"2\"><input type=\"checkbox\" ";
    echo "name=\"mysqlfulltext\" ";
    if (
        $mysqlfulltext == "yes" ||
        (empty($card_id) && empty($cardback) && empty($cardfront))
    ) {
        echo "checked=\"checked\" ";
    }
    echo "value=\"yes\"/>";
    echo " " . $l["mysql fulltext"] . "</td>\n";
    echo "  </tr>\n";


    echo "  <tr>\n";
    echo "    <td><input type=\"reset\" value=\"" . $l["reset"] . "\"/></td>\n";
    echo "    <td><input type=\"submit\" value=\"" . $l["submit"] . "\"/></td>\n";
    echo "  </tr>\n";

    echo "</table>\n\n";

    end_form();
}

// Returns a prepared statement, or false if we should abort the search.
function get_prepared_statement($db)
{
    global $card_id, $cardfront, $cardback, $searchnew, $searchlearned, $mysqlfulltext;

    $query  = "SELECT cards.card_id";
    $bound_params = [];
    $bound_param_types = "";

    // If we are forced to check cardfront and cardback ourselves, we
    // need cardback and cardfront too.
    if ($mysqlfulltext != "yes" && (!empty($cardfront) || !empty($cardback))) {
        $query .= ", cards.cardback, cards.cardfront ";
    } else {
        $query .= " ";
    }

    $query .= "FROM cards JOIN lesson2cards ";
    $query .= "ON lesson2cards.card_id = cards.card_id ";
    $query .= "WHERE 1 ";
    // The "1" doesn't really do anything in the query, it's here so
    // that we may assume that we should start a new condition with
    // "AND".

    if (!empty($card_id)) {
        $query .= "AND cards.card_id=? ";
        $bound_params[] = $card_id;
        $bound_param_types .= "i";
    }
    if ($searchlearned != "yes" && $searchnew != "yes") {
        // Well, that leaves no cards left at all to search among.
        return false;
    } elseif ($searchlearned == "yes" && $searchnew != "yes") {
        $query .= "AND lesson2cards.expires IS NOT NULL "; // No new cards...
        $query .=       "AND lesson2cards.expires != lesson2cards.created ";    // ...or short-term ones.
    } elseif ($searchlearned != "yes" && $searchnew == "yes") {
        $query .= "AND (lesson2cards.expires IS NULL ";         // New cards...
        $query .= "OR lesson2cards.expires = lesson2cards.created) ";   // and short-term ones.
    }
    // If both are yes we search among all cards.

    // Now we tackle $cardfront, $cardback and $mysqlfulltext:
    if ($mysqlfulltext == "yes") {
        if (!empty($cardfront)) {
            $query .= "AND MATCH(cardfront) AGAINST(?) ";
            $bound_params[] = $cardfront;
            $bound_param_types .= "s";
        }

        if (!empty($cardback)) {
            $query .= "AND MATCH(cardback) AGAINST(?) ";
            $bound_params[] = $cardback;
            $bound_param_types .= "s";
        }
    }

    echo "<!-- $query -->\n";
    $stmt = $db->prepare($query);
    if ($bound_param_types !== "") {
        $stmt->bind_param($bound_param_types, ...$bound_params);
    }
    return $stmt;
}

function search_and_list($card_id, $cardfront, $cardback)
{
    global $l, $mysqlfulltext;

    $db = get_db();
    $stmt = get_prepared_statement($db);
    if ($stmt !== false) {
        $stmt->execute();
        $result = $stmt->get_result();
    }

    if ($stmt === false || $result === false || $result->num_rows == 0) {
        echo "<div class=\"error\">\n";
        echo "  <p>" . $l["No cards were found"] . "</p>\n";
        echo "</div>\n\n";
    } else {
        if ($mysqlfulltext == "yes" || (empty($cardfront) && empty($cardback))) {
            $r = array();
            while ($row = $result->fetch_object()) {
                $r[] = $row->card_id;
            }
        } else {
            $r = array();
            // OK, we have to do a manual full text search. Yippee!
            while ($row = $result->fetch_object()) {
                if (empty($cardfront)) {
                    $front = true;
                } else {
                    $front = (strpos($row->cardfront, $cardfront) !== false);
                }

                if (empty($cardback)) {
                    $back = true;
                } else {
                    $back = (strpos($row->cardback, $cardback) !== false);
                }

                if ($front && $back) {
                    $r[] = $row->card_id;
                }
            }
        }
        if (empty($r)) {
            echo "<div class=\"error\">\n";
            echo "  <p>" . $l["No cards were found"] . "</p>\n";
            echo "</div>\n\n";
        } else {
            echo "<h2>" . $l["Cards found"] . "</h2>\n";
            paragraph($l["cards found, search at bottom of page"]);
            foreach ($r as $key => $value) {
                show_card($value);
            }
        }
    }
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

ob_start();

echo "<h1>" . sprintf($l["page title %s"], lesson_user()) . "</h1>\n\n";

if (!empty($card)) {
    $status = do_card($card, $really);
    switch ($status) {
        case "abort":
            //debug();
            goto end;
            break;

        case "no cards":
            paragraph($l["all deleted"]);
            //debug();
            goto end;
            break;
    }
}

if (!empty($card_id) || !empty($cardfront) || !empty($cardback)) {
    search_and_list($card_id, $cardfront, $cardback);
}

search_form();

//debug();

end:
$body = ob_get_clean();

$url = path_join_urls('..', $url);
$url['this'] = 'removecard';
$url['thislesson'] = './';

$smarty = get_smarty();
$smarty->assign('title', sprintf($l["page title %s"], lesson_user()));
$smarty->assign('relative_url', urlencode(lesson_filename()) . "/removecard");
$smarty->assign('lesson_name', lesson_user());
$smarty->assign('body', $body);

$smarty->assign('l', $l);
$smarty->assign('url', $url);
do_http_headers();
$smarty->display('layout.tpl');
