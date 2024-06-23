<?php

//  Aeppelkaka, a program which can help a stundent learning facts.
//  Copyright (C) 2021, 2022, 2023, 2024 Christian von Schultz
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

unset($B);
unset($b);
unset($c);
require_once("config.php");

// Set the default language and timezone for this document.
set_language("en");
set_timezone("Europe/Stockholm");

require_once("html.php");
require_once("backend.php");

if (is_logged_in()) {
    if (array_key_exists('logout', $_GET)) {
        logout();
    } elseif (array_key_exists('hijack', $_REQUEST) && is_admin()) {
        $session_id = session_id();
        $db = get_db();
        $stmt = $db->prepare("DELETE FROM sessions WHERE session_id=?");
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $db->prepare(
            "INSERT INTO sessions " .
            "(session_id, user_id, ip, session_start, session_last_active)" .
            "values (?, ?, ?, NOW(), NOW())"
        );
        $stmt->bind_param(
            "sis",
            $session_id,
            $_REQUEST['hijack'],
            $_SERVER['REMOTE_ADDR']
        );
        $stmt->execute()
            or error("Databasfel: du loggades inte in: " . $db->error());

        setcookie("user_id", $_REQUEST['hijack']);
        $logged_in = true;
    }

    header("Location: " . $c["webdir"]);
    exit;
}

expire_sessions();

$logged_in = false;
$error = "";

if (!empty($_POST["username"]) && !empty($_POST["password"])) {
    $db = get_db();

    $stmt = $db->prepare(
        "SELECT user_id, password_hash, password_inner_hash_algo " .
        "FROM users WHERE username=?"
    );
    $stmt->bind_param("s", $_POST["username"]);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 1) {
        mailerror(
            "It appears the username \"" . $_POST["username"] .
            "\" is not unique."
        );
        $error .= "There is a problem with your account. Contact the webmaster. ";
        $stmt->close();
    } elseif ($stmt->num_rows == 0) {
        $error .= "Incorrect username or password. ";
        $stmt->close();
    } else {
        $stmt->bind_result($user_id, $password_hash, $password_inner_hash_algo);
        $stmt->fetch();
        $stmt->close();
        if (empty($password_inner_hash_algo)) {
            $password = $_POST["password"];
        } elseif (in_array($password_inner_hash_algo, hash_algos())) {
            // The password inner hash algo is primarily intended to enable
            // importing users and passwords from legacy systems, that don't
            // already use a format supported by password_verify().
            // If the password hash is supported by hash(), you can run
            // password_hash() on your legacy hashes and store the result.
            // Make a note in the password_inner_hash_algo column what algorithm
            // was used originally.
            $password = hash($password_inner_hash_algo, $_POST["password"]);
        } else {
            mailerror(
                "It appears the user \"" . $_POST["username"] .
                "\" is using a weird inner hash algorithm: " .
                "$password_inner_hash_algo."
            );
            $error .= "There is a problem with your account. Contact the webmaster. ";
            $password = false;
        }
        if (!empty($password) && password_verify($password, sodium_decode($password_hash))) {
            // User OK.

            // Delete any old sessions: the latest successful login must be used.
            $del_stmt = $db->prepare("DELETE FROM sessions WHERE user_id=?");
            $del_stmt->bind_param("i", $user_id);
            $del_stmt->execute();
            $del_stmt->close();

            // is_logged_in() has already created a session, but it is not
            // yet registred with the database.
            $ins_stmt = $db->prepare(
                "INSERT INTO sessions " .
                "(session_id, user_id, ip, session_start, session_last_active)" .
                "values (?, ?, ?, now(), now())"
            );
            $session_id = session_id();
            $ins_stmt->bind_param(
                "sis",
                $session_id,
                $user_id,
                $_SERVER['REMOTE_ADDR']
            );
            $ins_stmt->execute()
                or $error .= "Database trouble: Login failed. ";

            setcookie("user_id", $user_id);
            $logged_in = true;
        } else {
            // User not OK.
            $error .= "Incorrect username or password. ";
        }
    }
}

if (($logged_in || is_logged_in()) && !headers_sent()) {
    header("Location: " . $c["webdir"]);
    exit;
}


add_stylesheet($c["webdir"] . "/" . $c["manifest"]["main.css"], "Main style");

begin_html();

head("Aeppelkaka: Log in", ".");

echo "<body>\n";
$html["has_body"] = true;

?>

<h2>Log in</h2>

<?php

if (!empty($error)) {
    error($error, false);
}

begin_form("login.php");

?>
<table align="center">
  <tr>
    <td>Username:</td>
    <td><input type="text" name="username" /></td>
  </tr>
  <tr>
    <td>Password:</td>
    <td><input type="password" name="password" /></td>
  </tr>
  <tr>
    <td colspan="2"><input type="submit" value="Logga in" /></td>
  </tr>
</table>

<?php
end_form();

echo "</body>\n";

end_html();
