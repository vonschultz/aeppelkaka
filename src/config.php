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
require_once("early_functions.php");

// The manifest file contains paths to static assets
$c["manifest"] = json_decode(file_get_contents("manifest.json"), true);

// Use Smarty
require_once('../../vendor/autoload.php');
use Smarty\Smarty;

// Language selection
$c["default language"] = "sv";

// Edit the next lines when deploying:
$c["webdir"] = "https://${PUBLIC_HOSTNAME}/aeppelkaka";
$c["yuidir"] = "https://${PUBLIC_HOSTNAME}/yui";  // Tested with YUI 2.9.0

// The address that gets diagnostic messages from the system:
// Edit the next line when deploying:
$c["webmaster mail"] = "${WEBMASTER_MAIL}";

// The address that mail is sent from:
// Edit the next line when deploying:
$c["aeppelkaka mail"] = "${AEPPELKAKA_MAIL}";

// Information required to connect to the MySQL server.
// Edit the next lines when deploying:
$c["mysql host"] = "${MYSQL_HOST}";
$c["mysql user"] = "${MYSQL_USER}";
$c["mysql password"] = "${MYSQL_PASSWORD}";
$c["mysql database name"] = "${MYSQL_DATABASE_NAME}";

// Key used to decrypt encrypted database entries.  New keys can be generated
// with sodium_bin2hex(sodium_crypto_secretbox_keygen()).
// Edit the next line when deploying:
$c["sodium key"] = sodium_hex2bin("${SODIUM_KEY}");

// Admin users (IDs should match entries in the `users` database):
// Edit the next line when deploying
$c["admin user id"] = array("${ADMIN_USER_ID}");

// User handling:

$c["max session length"] = 24 * 60; // minutes
$c["max inactive time"] = 60; // minutes


// Default algorithm:
$c["default repetition algorithm"] = 3;   // age of cards goes like 3^t.
$c["default hand size"] = 8;  // number of new cards you can comfortly handle

// Do not edit:

// The session stuff uses cookies, and only cookies:
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);

// We only use UTF-8
ini_set('default_charset', 'UTF-8');

// The stuff in the URL array is relative $c['webdir']. You should use
// <base href="$c['webdir']"> if you use these, or prepend
// $c['webdir'] . "/".
$url['help']                = 'help';
$url['listoflessons']       = '.';
$url['login']               = 'login.php';
$url['logout']              = 'logout';
$url['newlesson']           = 'newlesson';
$url['setup']               = 'setup';
$url['userlist']            = 'users';

// Appart from the To and Subject e-mail headers, we may want some more.
$c['extra mail headers'] = ('From: ' . $c["aeppelkaka mail"] . "\r\n".
                            'Content-type: text/plain; charset="UTF-8"');

// Now we define the following functions:
//  bool setup();
//                 True if we are ready to set up the user account.
//  void load_config();
//                 Reads user preferences into the global $c variable.
//                 If the user is not logged in, (s)he will be sent to
//                 the login page.
//  bool save_config();
//                 Saves the user preferences. true = success.
//  void set_language($lang)
//                 Sets the language. Usually called from load_config().
//  void set_timezone($tz)
//                 Sets the timezone. If PHP5 >= 5.1.0RC1 it uses the
//                 appropriate PHP functions, otherwise it tries with putenv();
//  book mailerror($error_message)
//                 Sends error messages by mail to webmaster.

function set_language($lang)
{
    global $c;

    if (empty($lang)) {
        $c["lang"] = $c["default language"];
    }

    $c["lang"] = $lang;
    putenv("LANG=" . $c["lang"]);
    putenv("LC_ALL=" . $c["lang"]);
    setlocale(LC_ALL, $c["lang"]);

    global $l;
    require_once("config_" . $lang . ".php");
    set_lang_array();

    $smarty = get_smarty();
    $smarty->assign("lang", $c["lang"]);
}

function set_timezone($tz)
{
    global $c;

    if (in_array($tz, $c["timezones"])) {
        putenv("TZ=" . $tz);
        if (function_exists("date_default_timezone_set")) {
            date_default_timezone_set($tz);
        }
        $c["timezone"] = $tz;
        $timezone_offset = date('P');

        $db = get_db();
        $stmt = $db->prepare("SET time_zone = ?");
        $stmt->bind_param("s", $timezone_offset);
        $stmt->execute();
    } else {
        mailerror("Invalid timezone selected in config.php: set_timezone(\"" . $tz . "\")");
    }
}

function mailerror($error_message)
{
    global $c;
    return error_log($error_message, 1, $c["webmaster mail"], "From: " . $c["aeppelkaka mail"]);
}

function my_error_handler($errno, $errstr, $errfile, $errline)
{
    static $email_sent = false;
    if (!$email_sent) {
        $email_sent = true;
        mailerror("$errfile:$errline: [$errno] $errstr");
    }
    return false;
}

set_error_handler('my_error_handler');

function set_lang_array()
{
    global $c;
    $c["languages"] = array(
        "en" => lang("English"),
        "sv" => lang("Swedish")
    );
}

set_lang_array();

// I got these from http://www.php.net/manual/en/timezones.php:
$c["timezones"] = array(
    "UTC",
    "Africa/Abidjan",
    "Africa/Accra",
    "Africa/Addis_Ababa",
    "Africa/Algiers",
    "Africa/Asmera",
    "Africa/Bamako",
    "Africa/Bangui",
    "Africa/Banjul",
    "Africa/Bissau",
    "Africa/Blantyre",
    "Africa/Brazzaville",
    "Africa/Bujumbura",
    "Africa/Cairo",
    "Africa/Casablanca",
    "Africa/Ceuta",
    "Africa/Conakry",
    "Africa/Dakar",
    "Africa/Dar_es_Salaam",
    "Africa/Djibouti",
    "Africa/Douala",
    "Africa/El_Aaiun",
    "Africa/Freetown",
    "Africa/Gaborone",
    "Africa/Harare",
    "Africa/Johannesburg",
    "Africa/Kampala",
    "Africa/Khartoum",
    "Africa/Kigali",
    "Africa/Kinshasa",
    "Africa/Lagos",
    "Africa/Libreville",
    "Africa/Lome",
    "Africa/Luanda",
    "Africa/Lubumbashi",
    "Africa/Lusaka",
    "Africa/Malabo",
    "Africa/Maputo",
    "Africa/Maseru",
    "Africa/Mbabane",
    "Africa/Mogadishu",
    "Africa/Monrovia",
    "Africa/Nairobi",
    "Africa/Ndjamena",
    "Africa/Niamey",
    "Africa/Nouakchott",
    "Africa/Ouagadougou",
    "Africa/Porto-Novo",
    "Africa/Sao_Tome",
    "Africa/Timbuktu",
    "Africa/Tripoli",
    "Africa/Tunis",
    "Africa/Windhoek",
    "America/Adak",
    "America/Anchorage",
    "America/Anguilla",
    "America/Antigua",
    "America/Araguaina",
    "America/Argentina/Buenos_Aires",
    "America/Argentina/Catamarca",
    "America/Argentina/ComodRivadavia",
    "America/Argentina/Cordoba",
    "America/Argentina/Jujuy",
    "America/Argentina/La_Rioja",
    "America/Argentina/Mendoza",
    "America/Argentina/Rio_Gallegos",
    "America/Argentina/San_Juan",
    "America/Argentina/Tucuman",
    "America/Argentina/Ushuaia",
    "America/Aruba",
    "America/Asuncion",
    "America/Atka",
    "America/Bahia",
    "America/Barbados",
    "America/Belem",
    "America/Belize",
    "America/Boa_Vista",
    "America/Bogota",
    "America/Boise",
    "America/Buenos_Aires",
    "America/Cambridge_Bay",
    "America/Campo_Grande",
    "America/Cancun",
    "America/Caracas",
    "America/Catamarca",
    "America/Cayenne",
    "America/Cayman",
    "America/Chicago",
    "America/Chihuahua",
    "America/Coral_Harbour",
    "America/Cordoba",
    "America/Costa_Rica",
    "America/Cuiaba",
    "America/Curacao",
    "America/Danmarkshavn",
    "America/Dawson",
    "America/Dawson_Creek",
    "America/Denver",
    "America/Detroit",
    "America/Dominica",
    "America/Edmonton",
    "America/Eirunepe",
    "America/El_Salvador",
    "America/Ensenada",
    "America/Fort_Wayne",
    "America/Fortaleza",
    "America/Glace_Bay",
    "America/Godthab",
    "America/Goose_Bay",
    "America/Grand_Turk",
    "America/Grenada",
    "America/Guadeloupe",
    "America/Guatemala",
    "America/Guayaquil",
    "America/Guyana",
    "America/Halifax",
    "America/Havana",
    "America/Hermosillo",
    "America/Indiana/Indianapolis",
    "America/Indiana/Knox",
    "America/Indiana/Marengo",
    "America/Indiana/Vevay",
    "America/Indianapolis",
    "America/Inuvik",
    "America/Iqaluit",
    "America/Jamaica",
    "America/Jujuy",
    "America/Juneau",
    "America/Kentucky/Louisville",
    "America/Kentucky/Monticello",
    "America/Knox_IN",
    "America/La_Paz",
    "America/Lima",
    "America/Los_Angeles",
    "America/Louisville",
    "America/Maceio",
    "America/Managua",
    "America/Manaus",
    "America/Martinique",
    "America/Mazatlan",
    "America/Mendoza",
    "America/Menominee",
    "America/Merida",
    "America/Mexico_City",
    "America/Miquelon",
    "America/Monterrey",
    "America/Montevideo",
    "America/Montreal",
    "America/Montserrat",
    "America/Nassau",
    "America/New_York",
    "America/Nipigon",
    "America/Nome",
    "America/Noronha",
    "America/North_Dakota/Center",
    "America/Panama",
    "America/Pangnirtung",
    "America/Paramaribo",
    "America/Phoenix",
    "America/Port-au-Prince",
    "America/Port_of_Spain",
    "America/Porto_Acre",
    "America/Porto_Velho",
    "America/Puerto_Rico",
    "America/Rainy_River",
    "America/Rankin_Inlet",
    "America/Recife",
    "America/Regina",
    "America/Rio_Branco",
    "America/Rosario",
    "America/Santiago",
    "America/Santo_Domingo",
    "America/Sao_Paulo",
    "America/Scoresbysund",
    "America/Shiprock",
    "America/St_Johns",
    "America/St_Kitts",
    "America/St_Lucia",
    "America/St_Thomas",
    "America/St_Vincent",
    "America/Swift_Current",
    "America/Tegucigalpa",
    "America/Thule",
    "America/Thunder_Bay",
    "America/Tijuana",
    "America/Toronto",
    "America/Tortola",
    "America/Vancouver",
    "America/Virgin",
    "America/Whitehorse",
    "America/Winnipeg",
    "America/Yakutat",
    "America/Yellowknife",
    "Brazil/Acre",
    "Brazil/DeNoronha",
    "Brazil/East",
    "Brazil/West",
    "Canada/Atlantic",
    "Canada/Central",
    "Canada/East-Saskatchewan",
    "Canada/Eastern",
    "Canada/Mountain",
    "Canada/Newfoundland",
    "Canada/Pacific",
    "Canada/Saskatchewan",
    "Canada/Yukon",
    "Chile/Continental",
    "Chile/EasterIsland",
    "Mexico/BajaNorte",
    "Mexico/BajaSur",
    "Mexico/General",
    "US/Alaska",
    "US/Aleutian",
    "US/Arizona",
    "US/Central",
    "US/East-Indiana",
    "US/Eastern",
    "US/Hawaii",
    "US/Indiana-Starke",
    "US/Michigan",
    "US/Mountain",
    "US/Pacific",
    "US/Pacific-New",
    "US/Samoa",
    "Antarctica/Casey",
    "Antarctica/Davis",
    "Antarctica/DumontDUrville",
    "Antarctica/Mawson",
    "Antarctica/McMurdo",
    "Antarctica/Palmer",
    "Antarctica/Rothera",
    "Antarctica/South_Pole",
    "Antarctica/Syowa",
    "Antarctica/Vostok",
    "Arctic/Longyearbyen",
    "Asia/Aden",
    "Asia/Almaty",
    "Asia/Amman",
    "Asia/Anadyr",
    "Asia/Aqtau",
    "Asia/Aqtobe",
    "Asia/Ashgabat",
    "Asia/Ashkhabad",
    "Asia/Baghdad",
    "Asia/Bahrain",
    "Asia/Baku",
    "Asia/Bangkok",
    "Asia/Beirut",
    "Asia/Bishkek",
    "Asia/Brunei",
    "Asia/Calcutta",
    "Asia/Choibalsan",
    "Asia/Chongqing",
    "Asia/Chungking",
    "Asia/Colombo",
    "Asia/Dacca",
    "Asia/Damascus",
    "Asia/Dhaka",
    "Asia/Dili",
    "Asia/Dubai",
    "Asia/Dushanbe",
    "Asia/Gaza",
    "Asia/Harbin",
    "Asia/Hong_Kong",
    "Asia/Hovd",
    "Asia/Irkutsk",
    "Asia/Istanbul",
    "Asia/Jakarta",
    "Asia/Jayapura",
    "Asia/Jerusalem",
    "Asia/Kabul",
    "Asia/Kamchatka",
    "Asia/Karachi",
    "Asia/Kashgar",
    "Asia/Katmandu",
    "Asia/Krasnoyarsk",
    "Asia/Kuala_Lumpur",
    "Asia/Kuching",
    "Asia/Kuwait",
    "Asia/Macao",
    "Asia/Macau",
    "Asia/Magadan",
    "Asia/Makassar",
    "Asia/Manila",
    "Asia/Muscat",
    "Asia/Nicosia",
    "Asia/Novosibirsk",
    "Asia/Omsk",
    "Asia/Oral",
    "Asia/Phnom_Penh",
    "Asia/Pontianak",
    "Asia/Pyongyang",
    "Asia/Qatar",
    "Asia/Qyzylorda",
    "Asia/Rangoon",
    "Asia/Riyadh",
    "Asia/Saigon",
    "Asia/Sakhalin",
    "Asia/Samarkand",
    "Asia/Seoul",
    "Asia/Shanghai",
    "Asia/Singapore",
    "Asia/Taipei",
    "Asia/Tashkent",
    "Asia/Tbilisi",
    "Asia/Tehran",
    "Asia/Tel_Aviv",
    "Asia/Thimbu",
    "Asia/Thimphu",
    "Asia/Tokyo",
    "Asia/Ujung_Pandang",
    "Asia/Ulaanbaatar",
    "Asia/Ulan_Bator",
    "Asia/Urumqi",
    "Asia/Vientiane",
    "Asia/Vladivostok",
    "Asia/Yakutsk",
    "Asia/Yekaterinburg",
    "Asia/Yerevan",
    "Indian/Antananarivo",
    "Indian/Chagos",
    "Indian/Christmas",
    "Indian/Cocos",
    "Indian/Comoro",
    "Indian/Kerguelen",
    "Indian/Mahe",
    "Indian/Maldives",
    "Indian/Mauritius",
    "Indian/Mayotte",
    "Indian/Reunion",
    "Atlantic/Azores",
    "Atlantic/Bermuda",
    "Atlantic/Canary",
    "Atlantic/Cape_Verde",
    "Atlantic/Faeroe",
    "Atlantic/Jan_Mayen",
    "Atlantic/Madeira",
    "Atlantic/Reykjavik",
    "Atlantic/South_Georgia",
    "Atlantic/St_Helena",
    "Atlantic/Stanley",
    "Australia/ACT",
    "Australia/Adelaide",
    "Australia/Brisbane",
    "Australia/Broken_Hill",
    "Australia/Canberra",
    "Australia/Currie",
    "Australia/Darwin",
    "Australia/Hobart",
    "Australia/LHI",
    "Australia/Lindeman",
    "Australia/Lord_Howe",
    "Australia/Melbourne",
    "Australia/North",
    "Australia/NSW",
    "Australia/Perth",
    "Australia/Queensland",
    "Australia/South",
    "Australia/Sydney",
    "Australia/Tasmania",
    "Australia/Victoria",
    "Australia/West",
    "Australia/Yancowinna",
    "Europe/Amsterdam",
    "Europe/Andorra",
    "Europe/Athens",
    "Europe/Belfast",
    "Europe/Belgrade",
    "Europe/Berlin",
    "Europe/Bratislava",
    "Europe/Brussels",
    "Europe/Bucharest",
    "Europe/Budapest",
    "Europe/Chisinau",
    "Europe/Copenhagen",
    "Europe/Dublin",
    "Europe/Gibraltar",
    "Europe/Helsinki",
    "Europe/Istanbul",
    "Europe/Kaliningrad",
    "Europe/Kiev",
    "Europe/Lisbon",
    "Europe/Ljubljana",
    "Europe/London",
    "Europe/Luxembourg",
    "Europe/Madrid",
    "Europe/Malta",
    "Europe/Mariehamn",
    "Europe/Minsk",
    "Europe/Monaco",
    "Europe/Moscow",
    "Europe/Nicosia",
    "Europe/Oslo",
    "Europe/Paris",
    "Europe/Prague",
    "Europe/Riga",
    "Europe/Rome",
    "Europe/Samara",
    "Europe/San_Marino",
    "Europe/Sarajevo",
    "Europe/Simferopol",
    "Europe/Skopje",
    "Europe/Sofia",
    "Europe/Stockholm",
    "Europe/Tallinn",
    "Europe/Tirane",
    "Europe/Tiraspol",
    "Europe/Uzhgorod",
    "Europe/Vaduz",
    "Europe/Vatican",
    "Europe/Vienna",
    "Europe/Vilnius",
    "Europe/Warsaw",
    "Europe/Zagreb",
    "Europe/Zaporozhye",
    "Europe/Zurich",
    "Pacific/Apia",
    "Pacific/Auckland",
    "Pacific/Chatham",
    "Pacific/Easter",
    "Pacific/Efate",
    "Pacific/Enderbury",
    "Pacific/Fakaofo",
    "Pacific/Fiji",
    "Pacific/Funafuti",
    "Pacific/Galapagos",
    "Pacific/Gambier",
    "Pacific/Guadalcanal",
    "Pacific/Guam",
    "Pacific/Honolulu",
    "Pacific/Johnston",
    "Pacific/Kiritimati",
    "Pacific/Kosrae",
    "Pacific/Kwajalein",
    "Pacific/Majuro",
    "Pacific/Marquesas",
    "Pacific/Midway",
    "Pacific/Nauru",
    "Pacific/Niue",
    "Pacific/Norfolk",
    "Pacific/Noumea",
    "Pacific/Pago_Pago",
    "Pacific/Palau",
    "Pacific/Pitcairn",
    "Pacific/Ponape",
    "Pacific/Port_Moresby",
    "Pacific/Rarotonga",
    "Pacific/Saipan",
    "Pacific/Samoa",
    "Pacific/Tahiti",
    "Pacific/Tarawa",
    "Pacific/Tongatapu",
    "Pacific/Truk",
    "Pacific/Wake",
    "Pacific/Wallis",
    "Pacific/Yap");

function is_admin()
{
    global $c;

    return (
        is_logged_in() &&
        in_array($_COOKIE['user_id'], $c['admin user id'])
    );
}

function assert_admin()
{
    global $l;

    if (!is_admin()) {
        error($l["Permission denied"]);
    }
}

function sodium_encode($plaintext)
{
    global $c;

    $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
    $ciphertext = sodium_crypto_secretbox(
        $plaintext,
        $nonce,
        $c['sodium key']
    );
    return $nonce . $ciphertext;
}

function sodium_decode($data)
{
    global $c;

    $noncebytes = SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;
    $nonce = mb_substr($data, 0, $noncebytes, '8bit');
    $ciphertext = mb_substr($data, $noncebytes, null, '8bit');
    return sodium_crypto_secretbox_open($ciphertext, $nonce, $c['sodium key']);
}

function load_config()
{
    global $c, $url;

    if (!is_logged_in()) {
        header("Location: " . $c['webdir'] . "/" . $url['login']);
        exit;
    }

    // First we update the last_active part of 'sessions'.
    $db = get_db();
    $stmt = $db->prepare(
        "UPDATE sessions SET session_last_active=NOW() " .
        "WHERE user_id=? AND session_id=? AND ip=?"
    );
    $session_id = session_id();
    $stmt->bind_param(
        "iss",
        $_COOKIE["user_id"],
        $session_id,
        $_SERVER['REMOTE_ADDR']
    );
    $stmt->execute();

    // Then we do what we're here for.
    $db = get_db();
    $stmt = $db->prepare(
        "SELECT " .
        "username, " .
        "lang, " .
        "timezone, " .
        "cardformat, " .
        "graphheight, " .
        "email, " .
        "city, " .
        "country, " .
        "joinedus " .
        "FROM users WHERE user_id=?"
    );
    $stmt->bind_param("i", $_COOKIE['user_id']);
    $stmt->execute();
    $stmt->store_result()
        or error(lang("Unable to load config from database."));

    if ($stmt->num_rows != 1) {
        error(lang("Unable to load config from database: numrows = " . $stmt->num_rows));
    }

    $stmt->bind_result(
        $username,
        $lang,
        $timezone,
        $cardformat,
        $graphheight,
        $email,
        $city,
        $country,
        $joinedus
    );

    $stmt->fetch();
    $stmt->close();

    $c["name"] = $username;
    set_language($lang);
    set_timezone($timezone);
    $c["prefers"] = $cardformat;
    $c["diagram height"] = $graphheight;
    $c["user email"] = $email;
    $c["user city"] = $city;
    $c["user country"] = $country;
    $c["user joined us on"] = $joinedus;
}

function save_config()
{
    global $c;

    $db = get_db();
    $stmt = $db->prepare(
        "UPDATE users SET lang=?, timezone=?, " .
        "cardformat=?, graphheight=?, email=?, " .
        "city=?, country=? WHERE user_id=? " .
        "LIMIT 1"
    );
    $stmt->bind_param(
        "sssisssi",
        $c["lang"],
        $c["timezone"],
        $c["prefers"],
        $c["diagram height"],
        $c["user email"],
        $c["user city"],
        $c["user country"],
        $_COOKIE['user_id']
    );

    return $stmt->execute();
}

function smarty_modifier_sprintf($string, ...$args)
{
    return sprintf($string, ...$args);
}

function get_smarty()
{
    global $B, $c;
    if (!isset($B['smarty'])) {
        $B['smarty'] = new Smarty();

        $B['smarty']->setTemplateDir('smarty/templates');
        $B['smarty']->setCompileDir('smarty/templates_c');
        $B['smarty']->setCacheDir('smarty/cache');
        $B['smarty']->setConfigDir('smarty/configs');

        $B['smarty']->escape_html = true;

        $B['smarty']->assign('webdir', $c['webdir']);
        $B['smarty']->assign('yuidir', $c['yuidir']);
        $B['smarty']->assign('isadmin', is_admin());
        $B['smarty']->assign('manifest', $c['manifest']);

        $B['smarty']->registerPlugin(
            Smarty::PLUGIN_MODIFIER,
            'sprintf',
            'smarty_modifier_sprintf'
        );
    }
    return $B['smarty'];
}
