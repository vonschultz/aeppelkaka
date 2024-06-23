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
unset($html);
unset($setup);
require_once("config.php");
load_config();
require_once("html.php");
require_once("backend.php");
require_once("setup_" . $c["lang"] . ".php");

$username = isset($_POST['username']) ? $_POST['username'] : null;

$lang = isset($_POST['lang']) ? $_POST['lang'] : null;
$tz = isset($_POST['tz']) ? $_POST['tz'] : null;
$prefers = isset($_POST['prefers']) ? $_POST['prefers'] : null;
$diaheight = isset($_POST['diaheight']) ? $_POST['diaheight'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$city = isset($_POST['city']) ? $_POST['city'] : null;
$country = isset($_POST['country']) ? $_POST['country'] : null;

$old_password = isset($_POST['old_password']) ? $_POST['old_password'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$password2 = isset($_POST['password2']) ? $_POST['password2'] : null;

function setup_form_page()
{
    global $l, $c, $html, $error, $message;

    if (!$html["has_begun"]) {
        begin_html();
        add_stylesheet($c["webdir"] . "/" . $c["manifest"]["main.css"], "");
        menu_item($l["Lessons"], "./", $l["Main page with lessons"]);
        menu_item($l["Help"], "help", $l["The Aeppelkaka manual"]);
        menu_item($l["Logout"], "logout", $l["Logout of Aeppelkaka"]);
        head($l["Setup"], "setup.php");
        body();
    }
    echo "<h1>" . $l["Setup"] . "</h1>\n";

    if (!empty($message)) {
        paragraph($message);
    }

    if (!empty($_POST['lang']) || !empty($_POST['username'])) {
        paragraph($l["This was saved:"]);
    }

    if (!empty($error)) {
        error($error, false);
    }

    begin_form("setup.php");
    echo "<table class=\"inset cyan\">\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["Username"] . "</td>\n";
    echo "    <td><input type=\"text\" name=\"username\" value=\"" .
        $c["name"] . "\"/></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td><input type=\"submit\" value=\"" . $l["Change username"] . "\"/></td>\n";
    echo "    <td><input type=\"reset\" value=\"" . $l["Reset"] . "\"/></td>\n";
    echo "  </tr>\n</table>\n";
    end_form();

    begin_form("setup.php");
    echo "<table class=\"inset yellow\">\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["Language"] . "</td>\n";
    echo "    <td><select name=\"lang\">\n";

    foreach ($c["languages"] as $language => $description) {
        echo "\t<option value=\"" . htmlspecialchars($language) . "\"";
        if ($c["lang"] == $language) {
            echo " selected=\"selected\" ";
        }
        echo ">" . htmlspecialchars($description) . "</option>\n";
    }
    echo "    </select></td>\n";
    echo "  </tr>\n";

    echo "  <tr>\n";
    echo "    <td>" . $l["Timezone"] . "</td>\n";
    echo "    <td>\n";
    echo "      <select name=\"tz\">\n";
    $oldcontinent = false;
    foreach ($c["timezones"] as $key => $timezone) {
        if (str_contains($timezone, "/")) {
            list($continent, $identifier) = explode("/", $timezone);
        } else {
            $identifier = $timezone;
            $continent = false;
        }
        if ($continent != $oldcontinent) {
            if ($oldcontinent !== false) {
                echo "        </optgroup>\n";
            }
            echo "        <optgroup label=\"" . htmlspecialchars($continent) . "\">\n";
            $oldcontinent = $continent;
        }
        if ($timezone == $c["timezone"]) {
            echo "          <option selected=\"selected\">";
        } else {
            echo "          <option>";
        }
        echo htmlspecialchars($timezone) . "</option>\n";
    }
    if ($oldcontinent !== false) {
        echo "        </optgroup>\n";
    }
    echo "      </select>\n";
    echo "    </td>\n";
    echo "  </tr>\n";

    echo "  <tr>\n";
    echo "    <td>" . $l["prefers"] . "</td>\n";
    echo "    <td><input type=\"radio\" name=\"prefers\" value=\"text/plain\"";
    // Plain text is default when the user prefers "text/plain" or when we
    // are not sure what (s)he prefers.
    if ($c["prefers"] != "application/xhtml+xml") {
        echo " checked=\"checked\"";
    }
    echo "/> " . $l["Plain text"] . "<br/>\n";
    echo "        <input type=\"radio\" name=\"prefers\" value=\"application/xhtml+xml\"";
    if ($c["prefers"] == "application/xhtml+xml") {
        echo " checked=\"checked\"";
    }
    echo "/>" . $l["XHTML 1.1"] . "</td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["Diagram height"] . "</td>\n";
    echo "    <td><input type=\"text\" name=\"diaheight\" value=\"" . $c["diagram height"] . "\"/></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["E-mail"] . "</td>\n";
    echo "    <td><input type=\"text\" name=\"email\" value=\"" . $c["user email"] . "\"/></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["City"] . "</td>\n";
    echo "    <td><input type=\"text\" name=\"city\" value=\"" . $c["user city"] . "\"/></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["Country"] . "</td>\n";
    echo "    <td><input type=\"text\" name=\"country\" value=\"" . $c["user country"] . "\"/></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td><input type=\"submit\" value=\"" . $l["Submit"] . "\"/></td>\n";
    echo "    <td><input type=\"reset\" value=\"" . $l["Reset"] . "\"/></td>\n";
    echo "  </tr>\n</table>\n";
    end_form();

    begin_form("setup.php");
    echo "<table class=\"inset pink\">\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["Old password"] . "</td>\n";
    echo "    <td><input type=\"password\" name=\"old_password\" /></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["Password"] . "</td>\n";
    echo "    <td><input type=\"password\" name=\"password\" /></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td>" . $l["Verify password"] . "</td>\n";
    echo "    <td><input type=\"password\" name=\"password2\" /></td>\n";
    echo "  </tr>\n";
    echo "  <tr>\n";
    echo "    <td><input type=\"submit\" value=\"" . $l["Change password"] . "\"/></td>\n";
    echo "    <td><input type=\"reset\" value=\"" . $l["Reset"] . "\"/></td>\n";
    echo "  </tr>\n</table>\n";
    end_form();

    end_body();
    end_html();
}

if (!empty($username)) {
    if (!username_unique($username) && $username != $c["name"]) {
        $error = sprintf($l["Username %s not unique"], $username);
    } elseif ($username != $c["name"]) {
        $db = get_db();
        $stmt = $db->prepare(
            "UPDATE users SET username=? WHERE user_id=?"
        );
        $stmt->bind_param("si", $username, $_COOKIE['user_id']);
        $stmt->execute();

        $c["name"] = $username;
    }
} elseif (!empty($lang)) {
    if (!array_key_exists($lang, $c["languages"])) {
        error($l["Impossible error: wrong language selected"], false);
        $lang = $c["lang"];
    }
    $c["lang"] = $lang;

    if (!in_array($tz, $c["timezones"])) {
        error($l["Impossible error: wrong timezone selected"], false);
    }
    $c["timezone"] = $tz;

    $c["prefers"] = $prefers;

    if (preg_match("/.*[^0-9].*/", $diaheight)) {
        error($l["Only digits in diaheight"], false);
        $diaheight = $c["diagram height"];
    }

    $c["diagram height"] = $diaheight;

    $c["user email"] = $email;
    $c["user city"] = $city;
    $c["user country"] = $country;

    save_config();
    load_config();
    require_once("setup_" . $c["lang"] . ".php");
} elseif (!empty($old_password)) {
    if (empty($password)) {
        $error = $l["Empty passwords won't do."];
    } elseif ($password != $password2) {
        $error = $l["Passwords don't match"];
    } else {
        $db = get_db();
        $stmt = $db->prepare(
            "SELECT password_hash, password_inner_hash_algo " .
            "FROM users WHERE user_id=?"
        );
        $stmt->bind_param("i", $_COOKIE['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_object();
        $result->close();

        $password_hash = sodium_decode($row->password_hash);

        if (!empty($row->password_inner_hash_algo)) {
            $old_password = hash(
                $row->password_inner_hash_algo,
                $old_password
            );
        }

        if (!password_verify($old_password, $password_hash)) {
            $error = $l["Old password incorrect"];
        } else {
            $stmt = $db->prepare(
                "UPDATE users " .
                "SET password_hash=?, password_inner_hash_algo=\"\" " .
                "WHERE user_id=?"
            );
            $new_password_hash = sodium_encode(
                password_hash($password, PASSWORD_DEFAULT)
            );
            $stmt->bind_param("si", $new_password_hash, $_COOKIE['user_id']);
            $stmt->execute();
            $message = $l["Password updated"];
        }
    }
}

setup_form_page();
