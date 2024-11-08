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
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
// When a user changes the lesson properties, these may be set:
$lessonname = isset($_POST['lessonname']) ? $_POST['lessonname'] : null;
$lessonfilename = isset($_POST['lessonfilename']) ? $_POST['lessonfilename'] : null;
$remove = isset($_POST['remove']) ? $_POST['remove'] : null;
$repetition_algorithm = isset($_POST['repetition_algorithm']) ? $_POST['repetition_algorithm'] : null;

assert_lesson($lesson);
forget_short_term_cards();

function table($n)
{
    global $l, $b;
    echo "<table>\n";
    echo "  <tr>\n";
    echo "    <td id=\"num\">" . $l["number of cards:"] . "</td>\n";
    echo "    <td headers=\"num\">" . $n . "</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td id=\"new\">" . $l["number of new cards:"] . "</td>\n";
    echo "    <td headers=\"new\">" . $b["number of new cards"] . "</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td id=\"exp\">" . $l["number of expired cards:"] . "</td>\n";
    echo "    <td headers=\"exp\">" . $b["number of expired cards"] . "</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td id=\"learned\">" . $l["number of learned cards:"] . "</td>\n";
    echo "    <td headers=\"learned\">" . $b["number of learned cards"] . "</td>\n";
    echo "  </tr>\n";
    echo "</table>\n\n";
}

function remove_lesson()
{
//   // How can anyone be so mean that they would actually call this
//   // funtion and... and kill the lesson? No, that can't be allowed:
//   return false;


    global $remove, $c, $b;

    if (empty($remove)) {
        return false;
    }

    if (headers_sent()) {    // If headers have been sent, removing
        // the lesson would result in landing
        // the user on a page that does not
        // exist.
        return false;
    }

    $db = get_db();
    $stmt = $db->prepare(
        "DELETE FROM lessons WHERE lesson_id=? AND user_id=?"
    );
    $stmt->bind_param("ii", $b["lesson"], $_COOKIE['user_id']);
    $stmt->execute();

    $stmt = $db->prepare(
        "SELECT card_id FROM lesson2cards WHERE lesson_id=?"
    );
    $stmt->bind_param("i", $b["lesson"]);
    $stmt->execute();

    $result = $stmt->get_result();
    $cards_to_remove = array();
    while ($row = $result->fetch_object()) {
        $cards_to_remove[] = $row->card_id;
    }
    $result->close();

    foreach ($cards_to_remove as $key => $card_to_remove) {
        remove_card($card_to_remove);
    }

    header("Location: " . $c["webdir"]);

    exit;
}

//* void main(void), so to speak

if ($action == "properties") {
    $ln = false;     // true if lesson_name has been updated
    $lfn = false;    // true if lesson_filename has been updated
    $lra = false;    // true if repetition_algorithm has been updated
    if (!empty($lessonname) && $lessonname != lesson_user()) {
        $db = get_db();
        $stmt = $db->prepare(
            "UPDATE lessons SET lesson_name=? WHERE lesson_id=?"
        );
        $stmt->bind_param("si", $lessonname, $b["lesson"]);
        $stmt->execute();
        $b["lesson name"] = $lessonname;
        $ln = true;
    }
    if (!empty($lessonfilename) && $lessonfilename != lesson_filename()) {
        if ($lessonfilename != lessonuser2lessonfilename($lessonfilename)) {
            $error = $l["lesson filename must be ASCII"];
        } elseif (!lesson_filename_unique($lessonfilename)) {
            $error = $l["lesson filename not unique"];
        } else {
            $db = get_db();
            $stmt = $db->prepare(
                "UPDATE lessons SET lesson_filename=? WHERE lesson_id=?"
            );
            $stmt->bind_param("si", $lessonfilename, $b["lesson"]);
            $stmt->execute();
            $b["lesson filename"] = $lessonfilename;
            $lfn = true;
        }
    }
    if (!empty($repetition_algorithm) && $repetition_algorithm != lesson_repetition_algorithm()) {
        $db = get_db();
        $stmt = $db->prepare(
            "UPDATE lessons SET repetition_algorithm=? WHERE lesson_id=?"
        );
        $stmt->bind_param("ii", $repetition_algorithm, $b["lesson"]);
        $stmt->execute();
        $b["repetition algorithm"] = $repetition_algorithm;
        $lra = true;
    }

    if (!empty($remove)) {
        remove_lesson();
    }


    ob_start();

    printf(
        "<h1>" . $l["Change properties for %s"] . "</h1>\n",
        lesson_user()
    );

    if ($ln && $lfn) {
        echo $l["changed ln and lfn"];
    } elseif ($ln) {
        echo $l["changed ln"];
    } elseif ($lfn) {
        echo $l["changed lfn"];
    }
    if ($lra) {
        echo $l["changed lra"];
    }

    if (!empty($error)) {
        echo "<div class=\"error\"><p>" . $error . "</p></div>\n";
    }

    echo $l["properties changing text"];

    begin_form("properties");
    echo "<table>\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["lesson name:"] . "</td>\n";
    printf(
        "    <td><input type=\"text\" name=\"lessonname\" value=\"%s\" /></td>\n",
        lesson_user()
    );
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["lesson filename:"] . "</td>\n";
    printf(
        "    <td><input type=\"text\" name=\"lessonfilename\" value=\"%s\" /></td>\n",
        lesson_filename()
    );
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["repetition algorithm"] . "</td>\n";
    echo "    <td>\n";
    echo "      <select name=\"repetition_algorithm\">\n";
    foreach ($l["lesson repetition algorithms"] as $algorithm_number => $description) {
        echo "        <option value=\"$algorithm_number\"";
        if (lesson_repetition_algorithm() == $algorithm_number) {
            echo " selected=\"selected\"";
        }
        echo ">" . $description . "</option>\n";
    }
    echo "      </select>\n";
    echo "    </td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    printf(
        "    <td><input type=\"submit\" value=\"%s\" /></td>\n",
        $l["submit changes"]
    );
    printf(
        "    <td><input type=\"reset\" value=\"%s\" /></td>\n",
        $l["reset"]
    );
    echo "  </tr>\n";
    echo "</table>\n";
    end_form();

    $pluginsettings_schema = json_decode(
        file_get_contents('pluginsettings-schema.json')
    );
    begin_form("properties", id: "pluginSettings");
    echo "<table>\n";
    echo "  <tr>\n";
    echo (
        "    <th><label for=\"enable_aeppelchess\">" .
        $l["Enable Aeppelchess plugin"] .
        "</label></th>\n"
    );
    echo "    <th>";
    echo (
        '<input' .
        ' type="checkbox"' .
        ' id="enable_aeppelchess"' .
        ' name="enable_aeppelchess"' .
        ' class="enable plugin aeppelchess"'
    );
    if (isset($b['lesson plugins']->aeppelchess)) {
        echo "checked ";
    }
    echo "/></th>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo (
        "    <td><label for=\"aeppelchess_width\">" .
        $l["Chessboard width:"] .
        "</label></td>\n"
    );
    printf(
        "    <td>%s</td>\n",
        input_using_schema(
            property: 'aeppelchess.width',
            schema: $pluginsettings_schema,
            value: $b['lesson plugins']->aeppelchess->width ?? null,
            class: 'plugin aeppelchess',
        )
    );
    echo "  </tr>\n";
    echo "  <tr>\n";
    printf(
        "    <td><input type=\"submit\" value=\"%s\" /></td>\n",
        $l["submit changes"]
    );
    printf(
        "    <td><input type=\"reset\" value=\"%s\" /></td>\n",
        $l["reset"]
    );
    echo "  </tr>\n";
    echo "</table>\n";
    end_form();

    printf(
        "<h2>" . $l["Remove %s"] . "</h2>\n",
        lesson_user()
    );

    begin_form("properties");
    printf(
        $l["remove lesson text"],
        sprintf(
            "<input type=\"submit\" name=\"remove\" class=\"dangerous\" value=\"%s\" />",
            htmlspecialchars(sprintf($l["Remove %s"], lesson_user()))
        )
    );
    end_form();

    $body = ob_get_clean();

    $url = path_join_urls('..', $url);
    $url['this'] = 'properties';
    $url['thislesson'] = './';

    $smarty = get_smarty();
    $smarty->assign(
        'title',
        sprintf($l["lesson properties for %s"], lesson_user())
    );
    $smarty->assign('relative_url', urlencode(lesson_filename()) . "/properties");
    $smarty->assign('lesson_name', lesson_user());
    $smarty->assign('body', $body);

    $smarty->assign('l', $l);
    $smarty->assign('url', $url);
    do_http_headers();
    $smarty->display('layout.tpl');
} else {
    $url['this'] = $b['lesson filename'];
    $url['thislesson'] = $b['lesson filename'];
    $smarty = get_smarty();

    $db = get_db();
    $already_created = "(created IS NULL OR created <= CURRENT_DATE())";
    $stmt = $db->prepare(
        "SELECT lesson_name, lesson_filename, " .
        "(SELECT COUNT(card_id) FROM `lesson2cards` " .
        "  WHERE lesson2cards.lesson_id = lessons.lesson_id " .
        "  AND $already_created " .
        "  AND expires IS NULL) AS new, " .
        "(SELECT COUNT(card_id) FROM `lesson2cards` " .
        "  WHERE lesson2cards.lesson_id = lessons.lesson_id " .
        "  AND $already_created " .
        "  AND expires <= CURDATE()) AS expired, " .
        "(SELECT COUNT(card_id) FROM `lesson2cards` " .
        "  WHERE lesson2cards.lesson_id = lessons.lesson_id " .
        "  AND created = CURDATE() " .
        "  AND expires = CURDATE() + INTERVAL 1 DAY) AS tomorrow, " .
        "(SELECT COUNT(card_id) FROM `lesson2cards` " .
        "  WHERE lesson2cards.lesson_id = lessons.lesson_id " .
        "  AND created = CURDATE() + INTERVAL 1 DAY " .
        "  AND expires IS NULL) AS new_tomorrow, " .
        "(SELECT COUNT(card_id) FROM `lesson2cards` " .
        "  WHERE lesson2cards.lesson_id = lessons.lesson_id " .
        "  AND $already_created " .
        "  AND expires > CURDATE()) AS learned, " .
        "(SELECT COUNT(card_id) FROM `lesson2cards` " .
        "WHERE lesson2cards.lesson_id = lessons.lesson_id " .
        "  AND $already_created " .
        ") AS total " .
        "FROM lessons " .
        "WHERE user_id=? AND lesson_id=?"
    );
    $stmt->bind_param("ii", $_COOKIE['user_id'], $b['lesson']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_object();
    $result->free_result();

    $smarty->assign('number_of_cards', $row->total);
    $smarty->assign('number_of_new_cards', $row->new);
    $smarty->assign('number_of_expired_cards', $row->expired);
    $smarty->assign('number_of_learned_cards', $row->learned);
    $smarty->assign('number_of_new_tomorrow_cards', $row->new_tomorrow);

    $smarty->assign('newexist', $row->new > 0);
    $smarty->assign('expiredexist', $row->expired > 0);
    $smarty->assign('tomorrowcardsexist', $row->tomorrow > 0);
    $smarty->assign('cardsexist', $row->total > 0);
    $smarty->assign('learnedexist', $row->learned > 0);
    $smarty->assign('forgetstatsready', forget_stats_ready());

    $stmt = $db->prepare(
        "SELECT username, from_lesson_id " .
        "FROM sharing JOIN users ON from_user_id = user_id " .
        "WHERE to_user_id=? AND to_lesson_id=?"
    );
    $stmt->bind_param("ii", $_COOKIE['user_id'], $b['lesson']);
    $stmt->execute();
    $result = $stmt->get_result();
    $smarty->assign('sharing', array());
    while (($row = $result->fetch_object())) {
        $smarty->append(
            'sharing',
            array( 'username' => $row->username,
                   'from_lesson_id' => $row->from_lesson_id)
        );
    }
    $result->free_result();

    $smarty->assign('l', $l);
    $smarty->assign('url', $url);
    do_http_headers();
    $smarty->display('lesson.tpl');
}
