<?php

//  Aeppelkaka, a program which can help a stundent learning facts.
//  Copyright (C) 2003, 2006, 2022, 2023, 2024 Christian von Schultz
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


// This file includes the backend functions. It should be included in the
// main files.

// Please note that that this file only contains functions, and no
// code is executed by including this file.

// You must include config.php before including this file.
require_once("html.php");
require_once("backend_" . $c["lang"] . ".php");
require_once("early_functions.php");

//*Lesson Functions:
//   bool   set_lesson($lesson_filename)
//            Sets the lesson for future operations. Returns false if
//            the lesson does not exist.
//
//   void   assert_lesson($lesson_filename)
//            Like set_lesson, but prints an error message if the lesson
//            does not exist.
//
//   string lesson_filename($lesson = "");
//            Returns the name of the directory holding the $lesson.
//            If you leave $lesson empty it assumes it should use the
//            set_lesson().
//
//   string lesson_user($lesson = "");
//            Returns the lesson name as shown to the user.
//
//   int lesson_repetition_algorithm()
//            Returns the lesson repetition algorithm.
//
//   int card_repetition_algorithm($card_id)
//            Returns the repetition algorithm set for $card_id, or
//            else the lesson repetition algorithm.
//
//   string lessonuser2lessonfilename($lessonuser);
//            Converts from user-readable format to internal lesson
//            format.  Used when creating new lessons: does not use
//            intelligent MySQL stuff.
//
//   bool   lesson_filename_unique($filename)
//            True if the user has no lesson with this lesson filename
//
//   bool   new_lesson($lessonuser, &$lessonfilename);
//            Creates a new lesson. The function may change the
//            $lessonfilename, that's why it's passed by reference.
//
//   int read_card_directory()
//            Reads the card directory and returns the number of found
//            cards (old, new and short term).
//
//   bool no_cards()
//            If there are no cards in this lesson, return true.
//
//   bool forget_stats_ready()
//            True if this lesson has forgetstats, needed for certain
//            graphs.
//
//*Card Functions:
//   bool   is_old_card($entry)
//            Tests if $entry is an old card.
//
//   bool   is_new_card($entry)
//            Tests if $entry is a new card.
//
//   bool   is_short_term_card($filename)
//            Tests if $entry is a short-term.
//
//   bool   has_expired($entry)
//            Tests if the card represented by $entry has expired.
//
//   void   forget_short_term_cards()
//            Turns all the short term cards into new cards.
//
//   list(cardfront, cardback) = get_card($filename)
//            Fetches a card.
//
//   bool   wellformed_xml($xmltext)
//            Checks $xmltext for well-formedness
//
//   string xml_error_message()
//            If wellformed_xml returns false, this function returns the reason.
//
//   void   make_card($carfront, $cardback, $repetition_alogrithm = "lesson", $add_to_lesson = true)
//            Creates a new card, $cardfront and $cardback are in XHTML
//
//   void   make_short_term($filename)
//            Makes $filename a short-term card.
//
//   void   make_long_term($short_term_card)
//            Makes $short_term_card a long term card.
//
//   void   make_new($card_id, $increment_forgotten = false)
//            Makes $card_id a new (unlearned) card. If
//            $increment_forgotten this counts as forgetting a card.
//
//   void   unexpire($filename)
//            Makes the expired card $filename a learned card. The older
//            the card is, the longer the period in which the card is
//            treated as learned.
//
//   void   remove_card($filename)
//            Removes the card named $filename.
//
//   string cardback2regex($cardback)
//            Creates a regex to match user input against the card back.
//
//*Date Functions:
//   string now()
//            The current date, in YYYY-MM-DD format.
//
//   list(year, month, day) = parse_date($date)
//            Parses a date in YYYYMMDD or YYYY-MM-DDformat.
//
//   string make_date($timestamp)
//            Timestamp to YYYY-MM-DD
//
//   int   make_timestamp($date)
//            Makes a UNIX timestamp corresponding to noon on $date.
//
//   int   difference($date1, $date2)
//            Returns the difference in days between two dates
//            in YYYY-MM-DD format.
//
//   string date_plus($date, $number)
//            $date plus $number of days
// 
//* Internal database functions:
//   resource get_db();    ("Early function")
//            Gets a database resource identifier, creating one if necessary
//
// 
//* User handling functions:
//   bool is_logged_in();    ("Early function")
//            Tells us wheter a user ($_COOKIE["user"]) has logged in.
//            As a side effect, it starts the session if needed.
//
//   void expire_sessions();
//            Removes expired sessions from the database.
//
//   void logout();
//            Removes the current user from the sessions database.
//
//   bool username_unique($username);
//            True if the $username isn't already taken.
//
//   bool mobile_phone_unique($number);
//            True if the $number isn't already taken.
// 
//*Other Functions:
//   int  make_seed()
//            Makes a number for srand()
//   array path_join_urls($path, $urls)
//            Adds the $path prefix to the array of URLs in $urls.
//            All paths and URLs are assumed to be relative.

function set_lesson($lesson_filename)
{
    global $b, $c, $url;

    if (empty($lesson_filename)) {
        return false;
    }

    $db = get_db();
    $stmt = $db->prepare(
        "SELECT " .
        "lesson_id, " .
        "lesson_filename, " .
        "lesson_name, " .
        "repetition_algorithm " .
        "FROM lessons WHERE lesson_filename=? AND user_id=?"
    );
    $stmt->bind_param("si", $lesson_filename, $_COOKIE['user_id']);
    $stmt->bind_result(
        $lesson_id,
        $lesson_filename,
        $lesson_name,
        $repetition_algorithm
    );
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows != 1) {
        return false;
    }

    $stmt->fetch();

    $b["lesson"] = $lesson_id;
    $b["lesson filename"] = $lesson_filename;
    $b["lesson name"] = $lesson_name;
    $b["repetition algorithm"] = $repetition_algorithm;

    $smarty = get_smarty();
    $smarty->assign('lesson_id', $b['lesson']);
    $smarty->assign('lesson_name', $b['lesson name']);
    $smarty->assign('lesson_filename', $b['lesson filename']);

    $url['lesson'] = array(
        'addcard' => "$lesson_filename/addcard",
        'removecard' => "$lesson_filename/removecard",
        'learncard' => "$lesson_filename/learncard",
        'parknew' => "$lesson_filename/parknew",
        'unparknew' => "$lesson_filename/unparknew",
        'testexpired' => "$lesson_filename/testexpired",
        'newlylearnt' => "$lesson_filename/newlylearnt",
        'graph' => "$lesson_filename/graph",
        'forgetstats' => "$lesson_filename/forgetstats",
        'properties' => "$lesson_filename/properties",
        'listcardbacks' => "$lesson_filename/list_cardbacks",
        'import_from_lesson' => "$lesson_filename/import_from_lesson_"
    );
    return true;
}

function assert_lesson($lesson_filename)
{
    global $l;
    if (!set_lesson($lesson_filename)) {
        error_page(sprintf($l["No lesson exists!"], $lesson_filename));
        exit;
    }
}

// 

function lesson_filename($lesson = "")
{
    global $b;
    // This function and lesson_user exist to enable us to use more sofisticated
    // lesson objects in the future without changing anything but the backend.

    // 2006 update: now we use more sophisticated lesson objects. Glad I
    // thought of this.
    if (empty($lesson)) {
        return $b["lesson filename"];
    } else {
        mailerror("It looks like you will have to implement lesson_filename() properly.");
        return $lesson;
    }
}

function lesson_user($lesson = "")
{
    global $b;
    if (empty($lesson)) {
        return $b["lesson name"];
    } else {
        mailerror("It looks like you will have to implement lesson_user() properly.");
        return $lesson;
    }
}

function lesson_repetition_algorithm()
{
    global $b, $c;

    return (empty($b["repetition algorithm"])?
            $c["default repetition algorithm"]:
            $b["repetition algorithm"]);
}

function card_repetition_algorithm($card_id)
{
    global $b;

    $db = get_db();
    $stmt = $db->prepare(
        "SELECT repetition_algorithm FROM lesson2cards " .
        "WHERE lesson_id=? AND card_id=?"
    );
    $stmt->bind_param("ii", $b["lesson"], $card_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows != 1) {
        return lesson_repetition_algorithm();
    } else {
        $row = $result->fetch_object();
        $result->close();
        if ($row->repetition_algorithm !== null) {
            return $row->repetition_algorithm;
        } else {
            return lesson_repetition_algorithm();
        }
    }
}

function lessonuser2lessonfilename($lessonuser)
{
    $asciify = array(
        "¥" => "Y",
        "µ" => "u",
        "À" => "A",
        "Á" => "A",
        "Â" => "A",
        "Ã" => "A",
        "Ä" => "A",
        "Å" => "A",
        "Æ" => "A",
        "Ç" => "C",
        "È" => "E",
        "É" => "E",
        "Ê" => "E",
        "Ë" => "E",
        "Ì" => "I",
        "Í" => "I",
        "Î" => "I",
        "Ï" => "I",
        "Ð" => "D",
        "Ñ" => "N",
        "Ò" => "O",
        "Ó" => "O",
        "Ô" => "O",
        "Õ" => "O",
        "Ö" => "O",
        "Ø" => "O",
        "Ù" => "U",
        "Ú" => "U",
        "Û" => "U",
        "Ü" => "U",
        "Ý" => "Y",
        "ß" => "ss",
        "à" => "a",
        "á" => "a",
        "â" => "a",
        "ã" => "a",
        "ä" => "a",
        "å" => "a",
        "æ" => "a",
        "ç" => "c",
        "è" => "e",
        "é" => "e",
        "ê" => "e",
        "ë" => "e",
        "ì" => "i",
        "í" => "i",
        "î" => "i",
        "ï" => "i",
        "ð" => "o",
        "ñ" => "n",
        "ò" => "o",
        "ó" => "o",
        "ô" => "o",
        "õ" => "o",
        "ö" => "o",
        "ø" => "o",
        "ù" => "u",
        "ú" => "u",
        "û" => "u",
        "ü" => "u",
        "ý" => "y",
        "ÿ" => "y",
        // OK, this isn't really asciifying, but anyway:
        " " => "_",
        "&" => "E"
    );
    $ascii = strtr($lessonuser, $asciify);
    mb_substitute_character(0x5F); // underscore
    return mb_convert_encoding($ascii, "US-ASCII", "UTF-8");
}

function lesson_filename_unique($lessonfilename)
{
    $db = get_db();
    $stmt = $db->prepare(
        "SELECT 1 FROM lessons WHERE user_id=? AND lesson_filename=? LIMIT 1"
    );
    $stmt->bind_param("is", $_COOKIE['user_id'], $lessonfilename);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows != 0) {
        $stmt->free_result();
        return false;
    } elseif (
        file_exists($lessonfilename) ||
        (bool)(glob($lessonfilename . ".*"))
    ) {
        return false;
    } else {
        return true;
    }
}

function new_lesson($lessonuser, &$lessonfilename)
{
    global $c;

    if (empty($lessonuser)) {
        return false;
    }

    if (empty($lessonfilename)) {
        $lessonfilename = lessonuser2lessonfilename($lessonuser);
    }
    if (empty($lessonfilename)) {
        $lessonfilename = time();
    }

    $called = 0;
    while (!lesson_filename_unique($lessonfilename) && ++$called <= 10) {
        $lessonfilename .= "-";
    }
    if ($called >= 10) {
        mailerror("\$called = $called in " . __FILE__ . ":" . __LINE__);
        return false;                 // Something is seriously wrong.
    }


    $db = get_db();
    $stmt = $db->prepare(
        "INSERT INTO lessons (lesson_name, lesson_filename, " .
        "user_id, repetition_algorithm) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ssii",
        $lessonuser,
        $lessonfilename,
        $_COOKIE['user_id'],
        $c["default repetition algorithm"]
    );
    return $stmt->execute();
}

function read_card_directory()
{
    global $b, $c, $l;

    $a = 0; // counts the number of found learned cards
    $e = 0; // counts the number of expired cards
    $n = 0; // counts the number of found new cards
    $s = 0; // counts the number of short term (nearly learned) cards
    $t = 0; // counts the number of tomorrow (newly learned) cards

    $b["expired cards"] = array();
    $b["learned cards"] = array();
    $b["new cards"] = array();
    $b["short term cards"] = array();

    $db = get_db();
    $stmt = $db->prepare(
        "SELECT * FROM lesson2cards WHERE lesson_id=? " .
        "AND (created IS NULL OR created <= CURRENT_DATE())"
    );
    $stmt->bind_param("i", $b["lesson"]);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($entry = $result->fetch_object()) {
        if (is_old_card($entry)) {
            if (has_expired($entry)) {
                $b["expired cards"][] = $entry;
                $e++;
            } else {
                $b["learned cards"][] = $entry;
                $a++;

                if (
                    $entry->expires == date('Y-m-d', time() + 86400) &&
                    $entry->created == date('Y-m-d', time())
                ) {
                    $t++;
                }
            }
        }
        if (is_new_card($entry)) {
            $b["new cards"][] = $entry;
            $n++;
        }
        if (is_short_term_card($entry)) {
            $b["short term cards"][] = $entry;
            $s++;
        }
    }
    $result->close();
    $b["number of expired cards"] = $e;
    $b["number of learned cards"] = $a;
    $b["number of new cards"] = $n;
    $b["number of short term cards"] = $s;
    $b["number of tomorrow cards"] = $t;
    return $a + $e + $n + $s;     /* tomorrow already counted in learned cards */
}

function no_cards()
{
    global $b;
    return($b["number of expired cards"] +
           $b["number of learned cards"] +
           $b["number of new cards"] +
           $b["number of short term cards"] == 0);
}

function forget_stats_ready()
{
    global $b;

    $db = get_db();
    $stmt = $db->prepare(
        "SELECT * FROM forget_stats_rem WHERE lesson_id=?"
    );
    $stmt->bind_param("i", $b["lesson"]);
    $stmt->execute();
    $stmt->store_result();
    $num_rem = $stmt->num_rows();
    $stmt->free_result();

    $stmt = $db->prepare(
        "SELECT * FROM forget_stats_forg WHERE lesson_id=?"
    );
    $stmt->bind_param("i", $b["lesson"]);
    $stmt->execute();
    $stmt->store_result();
    $num_forg = $stmt->num_rows();
    $stmt->free_result();

    return $num_rem + $num_forg > 100;
}

// 

function is_old_card(&$entry)
{
    // New cards have expires = NULL, short-term cards have expires = created.
    return !is_null($entry->expires) && $entry->expires != $entry->created;
}

function is_new_card(&$entry)
{
    return is_null($entry->expires);
}

function is_short_term_card(&$entry)
{
    return $entry->expires == $entry->created;
}

function has_expired(&$entry)
{
    if (make_timestamp($entry->expires) > make_timestamp(now())) {
        return false;
    } else {
        return true;
    }
}

function forget_short_term_cards()
{
    global $b;

    $db = get_db();
    $stmt = $db->prepare(
        "UPDATE lesson2cards JOIN lessons USING(lesson_id) " .
        "SET expires = NULL " .
        "WHERE user_id = ? " .
        "AND expires = created"
    );
    $stmt->bind_param("i", $_COOKIE['user_id']);
    $stmt->execute();
}

// 

function get_card($card_id)
{
    global $b, $c, $l;

    $db = get_db();
    $stmt = $db->prepare(
        "SELECT cardfront, cardback FROM cards WHERE card_id=?"
    );
    $stmt->bind_param("i", $card_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows != 1) {
        error_page(sprintf($l["Could not find card with ID %s"], $card_id));
        exit;
    }

    $row = $result->fetch_object();
    $result->close();

    return array($row->cardfront, $row->cardback);
}

function wellformed_xml($xmltext)
{
    global $b;
    $parser = xml_parser_create("UTF-8");
    $result = xml_parse($parser, $xmltext, true); // true = no more data
    if ($result) {
        $b["xml error code"] = "none";
        xml_parser_free($parser);
        return true;
    } else {
        $b["xml error code"] = xml_get_error_code($parser);
        xml_parser_free($parser);
        return false;
    }
}

function xml_error_message()
{
    global $b, $l;
    if ($b["xml error code"] === "none") {
        return "";
    } else {
        return sprintf(
            $l["error code %s: %s"],
            $b["xml error code"],
            xml_error_string($b["xml error code"])
        ) . "</p><p>" . $l["card not added"];
    }
}

function make_card($cardfront, $cardback, $repetition_algorithm = "lesson", $add_to_lesson = true)
{
    global $b;

    $db = get_db();
    $stmt =  $db->prepare(
        "INSERT INTO cards " .
        "(real_created, creator, cardfront, cardback) " .
        "VALUES (NOW(), ?, ?, ?)"
    );
    $stmt->bind_param("iss", $_COOKIE['user_id'], $cardfront, $cardback);
    $to_cards = $stmt->execute();

    if ($add_to_lesson) {
        $stmt = $db->prepare(
            "INSERT INTO lesson2cards (lesson_id, card_id, " .
            "expires, created, forgotten, remembered" .
            (($repetition_algorithm == "lesson") ? "" : ", repetition_algorithm") .
            ") VALUES (?, LAST_INSERT_ID(), NULL, NOW(), '0', '0'" .
            (($repetition_algorithm == "lesson") ? "" : ", ?") .
            ")"
        );
        if ($repetition_algorithm == "lesson") {
            $stmt->bind_param("i", $b["lesson"]);
        } else {
            $stmt->bind_param("ii", $b["lesson"], $repetition_algorithm);
        }

        $result = $stmt->execute();

        return $result && $to_cards;
    } else {
        return $to_cards;
    }
}

function make_short_term($card_id)
{
    global $b;

    $db = get_db();
    $stmt = $db->prepare(
        "UPDATE lesson2cards SET expires=created " .
        "WHERE lesson_id=? AND card_id=?"
    );
    $stmt->bind_param("ii", $b["lesson"], $card_id);
    $stmt->execute();

    read_card_directory();
}

function make_long_term($card_id)
{
    global $b;

    // Expires tomorrow.
    $db = get_db();
    $stmt = $db->prepare(
        "UPDATE lesson2cards " .
        "SET expires=(CURDATE() + INTERVAL 1 DAY), " .
        "created=CURDATE() " .
        "WHERE expires=created AND lesson_id=? " .
        "AND card_id=?"
    );
    $stmt->bind_param("ii", $b["lesson"], $card_id);
    $stmt->execute();

    read_card_directory();
}

function unexpire($card_id)
{
    global $b;

    $db = get_db();

    $dif = "(to_days(CURDATE()) - to_days(lesson2cards.created))";

    $repetition_algorithm = card_repetition_algorithm($card_id);

    srand(make_seed());
    $number = rand(0, 2);

    switch ($repetition_algorithm) {
        case 2:                         // 2^n
        case 3:                         // 3^n
        case 4:                         // 4^n
            if ($number < 0.5) {
                $number = "( " . ($repetition_algorithm - 1) . "*" . $dif . "-IF(($dif)>3, 1, 0))";
            } elseif ($number > 1.5) {
                $number = "( " . ($repetition_algorithm - 1) . "*" . $dif . "+IF(($dif)>3, 1, 0))";
            } else {
                $number = "( " . ($repetition_algorithm - 1) . "*" . $dif . ")";
            }
            break;
        default:
            mailerror("Unknown repetition algorithm: " . $repetition_algorithm);
            error_page("Internal server error");
            break;
    }

    $stmt = $db->prepare(
        "UPDATE lesson2cards " .
        "SET expires=adddate(CURDATE(), interval " . $number . " day)," .
        "remembered=remembered+1 " .
        "WHERE expires <= CURDATE() AND lesson_id=? " .
        "AND card_id=?"
    );
    $stmt->bind_param("ii", $b["lesson"], $card_id);
    $stmt->execute();

    read_card_directory();

    $stmt = $db->prepare(
        "UPDATE forget_stats_rem,lesson2cards " .
        "SET forget_stats_rem.num=forget_stats_rem.num+1 " .
        "WHERE forget_stats_rem.lesson_id=? " .
        "AND lesson2cards.lesson_id=? " .
        "AND lesson2cards.card_id=? " .
        "AND forget_stats_rem.dif=" . $dif
    );
    $stmt->bind_param("iii", $b["lesson"], $b["lesson"], $card_id);
    $stmt->execute();

    if ($stmt->affected_rows == 0) {
        $stmt = $db->prepare(
            "INSERT INTO forget_stats_rem (lesson_id, dif, num) " .
            "SELECT ?, " . $dif . ", 1 " .
            "FROM lesson2cards WHERE card_id=? " .
            "AND lesson_id=?"
        );
        $stmt->bind_param("iii", $b["lesson"], $card_id, $b["lesson"]);
        $stmt->execute();
    }
}

function remove_card($card_id)
{
    global $b;

    $db = get_db();
    $stmt = $db->prepare(
        "DELETE FROM lesson2cards WHERE lesson_id=? AND card_id=?"
    );
    $stmt->bind_param("ii", $b["lesson"], $card_id);
    $stmt->execute();

    $stmt = $db->prepare(
        "SELECT card_id FROM lesson2cards WHERE card_id=?"
    );
    $stmt->bind_param("i", $card_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $stmt->free_result();

        // OK, no one else uses this card, so we can safely remove it.
        $stmt = $db->prepare(
            "DELETE FROM cards WHERE card_id=?"
        );
        $stmt->bind_param("i", $card_id);
        $stmt->execute();
    } else {
        $stmt->free_result();
    }

    read_card_directory();
}

function make_new($card_id, $increment_forgotten = false)
{
    global $b;

    $db = get_db();

    if ($increment_forgotten) {
        $stmt = $db->prepare(
            "SELECT * FROM lesson2cards WHERE lesson_id=? AND card_id=?"
        );
        $stmt->bind_param("ii", $b["lesson"], $card_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_object();
        if (!is_object($row)) {
            return;
        }
        $result->close();

        $dif = difference(now(), $row->created);

        $stmt = $db->prepare(
            "UPDATE forget_stats_forg SET num=num+1 WHERE " .
            "lesson_id=? AND dif=?"
        );
        $stmt->bind_param("ii", $b["lesson"], $dif);
        $stmt->execute();

        if ($stmt->affected_rows == 0) {
            $stmt = $db->prepare(
                "INSERT INTO forget_stats_forg (lesson_id, dif, num) " .
                "SELECT ?, ?, 1 " .
                "FROM lesson2cards WHERE card_id=? " .
                "AND lesson_id=?"
            );
            $stmt->bind_param(
                "iiii",
                $b["lesson"],
                $dif,
                $card_id,
                $b["lesson"]
            );
            $stmt->execute();
        }
    }

    $stmt = $db->prepare(
        "UPDATE lesson2cards SET " .
        ($increment_forgotten ? "forgotten=forgotten+1, " : "") .
        "created=?, expires=NULL " .
         "WHERE card_id=? AND lesson_id=?"
    );
    $now = now();
    $stmt->bind_param("sii", $now, $card_id, $b["lesson"]);
    $stmt->execute();

    read_card_directory();
}

function cardback2regex($cardback)
{
    $ret = trim($cardback);

    // &something;
    $ret = str_replace("&nbsp;", " ", $ret);

    // Before replace of regex chars
    $ret = str_replace("(<em>", "(", $ret);

    // Escape regex special chars appearing in the string
    $ret = str_replace("\\", "\\\\", $ret);
    $ret = str_replace("[", "\\[", $ret);
    $ret = str_replace("]", "\\]", $ret);
    $ret = str_replace("{", "\\{", $ret);
    $ret = str_replace("(", "\\(", $ret);
    $ret = str_replace(")", "\\)", $ret);
    $ret = str_replace("|", "\\|", $ret);
    $ret = str_replace("*", "\\*", $ret);
    $ret = str_replace("+", "\\+", $ret);
    $ret = str_replace("?", "\\?", $ret);

    // Special handling of <em>
    $ret = str_replace(" <em>", " ", $ret);
    $ret = str_replace("<em>", "[^a-zA-Z0-9]", $ret);
    $ret = str_replace("</em>", ".?", $ret);

    // Special handling of spaces
    $ret = str_replace(" ", " +", $ret);
    $ret = str_replace("\n", " *", $ret);

    // Special handling of <br/>
    $ret = str_replace("<br/>", "[;,]? *", $ret);

    $ret = str_replace("</sub>", ".?", $ret);
    $ret = str_replace("'", "['´]", $ret);

    // Handling of other HTML
    $ret = preg_replace("%</?[^>]*>%", "", $ret);

    return "^ *" . $ret . " *$";
}

function make_date($timestamp)
{
    return date("Y-m-d", $timestamp);
}

function now()
{
    return date("Y-m-d");
}

function parse_date($date)
{
    if (strpos($date, "-")) {          // I'm fully aware that strpos = 0 will be false here.
        return explode("-", $date);
    } else {
        return array(substr($date, 0, 4), substr($date, 4, 2), substr($date, 6, 2));
    }
}

function make_timestamp($date)
{
    // gmmktime(hour, minute, second, month, day, year)
    //
    // We are using noon as the reference time. I previously tried
    // midnight, but strange bugs appeared on certain dates in combination
    // with strtotime().

    list($year, $month, $day) = parse_date($date);

    return gmmktime(12, 0, 0, $month, $day, $year);
}

function difference($date1, $date2)
{
    $timestamp1 = make_timestamp($date1);
    $timestamp2 = make_timestamp($date2);

    return abs($timestamp1 - $timestamp2) / (24 * 3600);
    // I'm taking the difference in seconds and change it into difference in days.
}

function date_plus($date, $number)
{
    $timestamp1 = make_timestamp($date);
    $timestamp2 = strtotime("+" . $number . " days", $timestamp1);
    return make_date($timestamp2);
}

function expire_sessions()
{
    global $c;

    $db = get_db();

    $stmt = $db->prepare(
        "DELETE FROM sessions " .
        "WHERE session_start + INTERVAL ? MINUTE < NOW() " .
        "OR session_last_active + INTERVAL ? MINUTE < NOW()"
    );
    $stmt->bind_param("ss", $c['max session length'], $c['max inactive time']);
    $stmt->execute();
}

function logout()
{
    global $c;

    $db = get_db();

    $stmt = $db->prepare("DELETE FROM sessions WHERE user_id=?");
    $stmt->bind_param("i", $_COOKIE['user_id']);

    return $stmt->execute();
}

function username_unique($username)
{
    global $l;

    $db = get_db();
    $stmt = $db->prepare(
        "SELECT username FROM users WHERE username=?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $numrows = $stmt->num_rows;
    $stmt->free_result();

    return $numrows == 0;
}

function mobile_phone_unique($number)
{
    global $l;

    $db = get_db();
    $stmt = $db->prepare(
        "SELECT mobile_phone FROM users WHERE mobile_phone=?"
    );
    $stmt->bind_param("s", $number);
    $stmt->execute();
    $stmt->store_result();
    $numrows = $stmt->num_rows;
    $stmt->free_result();

    return $numrows == 0;
}

function make_seed()
{
    list($usec, $sec) = explode(' ', microtime());
    $floatseed = (float) $sec + ((float) $usec * 100000);
    return (int) $floatseed;
}

function path_join_urls($path, $urls)
{
    $new_urls = array();

    foreach ($urls as $key => $value) {
        if (is_array($value)) {
            $new_urls[$key] = path_join_urls($path, $value);
        } elseif ($value == '.') {
            $new_urls[$key] = $path . '/';
        } else {
            $new_urls[$key] = $path . '/' . $value;
        }
    }

    return $new_urls;
}
