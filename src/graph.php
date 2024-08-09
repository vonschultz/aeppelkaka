<?php

//  Aeppelkaka, a program which can help a stundent learning facts.
//  Copyright (C) 2003, 2006, 2020, 2021, 2022, 2023, 2024 Christian von Schultz
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
unset($g); // the graph variable
require_once("config.php");
load_config();
require_once("backend.php");
require_once("html.php");
require_once("graph_" . $c["lang"] . ".php");
$lesson = $_REQUEST['lesson'];
$month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null;
$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null;
$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : false;

$l["Number of new cards: %s"] = mb_convert_encoding($l["Number of new cards: %s"], "ISO-8859-1", "UTF-8");
$l["Number of short term cards: %s"] = mb_convert_encoding($l["Number of short term cards: %s"], "ISO-8859-1", "UTF-8");

if (!is_null($year)) { // If $year is not null, it has to be 4 digits.
    if (!preg_match("/^[0-9]{4}$/", $year)) {
        $year = null;
    }
}

if (!is_null($month)) { // If $month is not null, it has to be 2 digits.
    if (!preg_match("/^[0-9]{2}$/", $month)) {
        $month = null;
    }
}

if (is_null($year) && !is_null($month)) {
    $month = null;
}


function twodigits($month)
{
    if ($month < 10) {
        return "0" . $month;
    } else {
        return $month;
    }
}

//* This creates the $g["cards"] array. For example
//  $g["cards"]["2003-12-11"]["1999-01-14"] = number of cards that expire on
//                                            2003-12-11 and are created on
//                                            1999-01-14.
//  $g["first"] = the earliest date of expiration
//  $g["last"] = the last date of expiration
// Do not forget to read_card_directory().
function make_gcards()
{
    global $g, $b, $year, $month, $debug;

    $g["cards"] = array();
    $g["first"] = now();
    $g["last"] = now();
    $g["max number of cards"] = 0;

    if (!empty($year)) {
        @sort($b["expired cards"]);
    }

    foreach ($b["expired cards"] as $key => $card) {
        if (make_timestamp($card->expires) < make_timestamp($g["first"])) {
            $g["first"] = $card->expires;
        } elseif (make_timestamp($card->expires) > make_timestamp($g["last"])) {
            $g["last"] = $card->expires;
        }
        if (!isset($g["cards"][$card->expires])) {
            $g["cards"][$card->expires] = array();
        }
        if (!isset($g["cards"][$card->expires][$card->created])) {
            $g["cards"][$card->expires][$card->created] = 0;
        }
        $g["cards"][$card->expires][$card->created] ++;
        eval("\$t = " . implode("+", $g["cards"][$card->expires]) . ";");
        if ($t > $g["max number of cards"]) {
            $g["max number of cards"] = $t;
        }
    }

    if (!empty($year)) {
        @sort($b["learned cards"]);
    }
    foreach ($b["learned cards"] as $key => $card) {
        if (make_timestamp($card->expires) < make_timestamp($g["first"])) {
            $g["first"] = $card->expires;
        } elseif (make_timestamp($card->expires) > make_timestamp($g["last"])) {
            $g["last"] = $card->expires;
        }
        if (!isset($g["cards"][$card->expires])) {
            $g["cards"][$card->expires] = array();
        }
        if (!isset($g["cards"][$card->expires][$card->created])) {
            $g["cards"][$card->expires][$card->created] = 0;
        }
        $g["cards"][$card->expires][$card->created] ++;
        eval("\$t = " . implode("+", $g["cards"][$card->expires]) . ";");
        if ($t > $g["max number of cards"]) {
            $g["max number of cards"] = $t;
        }
    }
    if (!is_null($year)) {
        if (
            make_timestamp($g["first"]) <
            make_timestamp($year . (is_null($month) ? "01" : $month) . "01")
        ) {
            $g["first"] = $year ."-". (is_null($month) ? "01" : $month) . "-01";

            if ($debug) {
                echo "<p>Setting \$g[\"first\"] to " . $g["first"] . "</p>\n";
            }
        }
        if (!is_null($month)) {
            if (
                make_timestamp($g["last"]) >
                make_timestamp(date_plus($year . twodigits($month + 1) . "01", -1))
            ) {
                $g["last"] = date_plus($year . twodigits($month + 1) . "01", -1);
                if ($debug) {
                    echo "<p>Setting \$g[\"last\"] to " . $g["last"] . "<br/>" .
                        "\$year = $year; \$month = $month; " .
                        "date_plus(" . $year . twodigits($month + 1) . "01" . ", -1) = " .
                        date_plus($year . twodigits($month + 1) . "01", -1) . "; </p>\n";
                }
            } elseif ($debug) {
                echo "<p>Leaving \$g[\"last\"] at " . $g["last"] . "</p>\n";
            }
        } elseif (make_timestamp($g["last"]) > make_timestamp($year . "1231")) {
            $g["last"] = $year . "1231";
            if ($debug) {
                echo "<p>Setting \$g[\"last\"] to " . $g["last"] . "</p>\n";
            }
        }
    }
    $g["number of days"] = difference($g["first"], $g["last"]) + 1;
}

//* The estimated number of thimes that a card aged $age has been
//  seen. Normally this is repetition_algorithm()^x where x is the
//  number of times the card has been seen.

function repetition_count($age)
{
    return round(log10($age) / log10(lesson_repetition_algorithm()));
}

//* Creates the image variables: size, $g["im"], and so on.
function make_imvars()
{
    global $g, $c;

    $g["dayspace"] = 29; // The width of each day in pixels;
    $g["space between arrow and canvas"] = 10;
    $g["ysize"] = $c["diagram height"]; // The total height of the image
    $g["ymax"] = $g["ysize"] - 1;
    $g["yaxis"] = 30; // The width of the y axis
    $g["xaxis"] = 30; // The height of the x axis
    $g["margin top"] = 15; // The margin at the top of the image
    $g["margin top"] += (
        (
            $g["ysize"] - $g["space between arrow and canvas"] -
            $g["xaxis"] - $g["margin top"]
        ) % $g["max number of cards"]
    );
    $g["margin right"] = 40;
    $g["ycanvas"] = ($g["ysize"] - $g["xaxis"] - $g["margin top"] -
                     $g["space between arrow and canvas"]);
    $g["card height"] = $g["ycanvas"] / $g["max number of cards"];
    $g["xcanvas"] = $g["dayspace"] * $g["number of days"];
    $g["xsize"] = (
        $g["xcanvas"] + $g["yaxis"] + $g["margin right"] +
        $g["space between arrow and canvas"]
    );
    $g["xmax"] = $g["xsize"] - 1;

    //   ╷              ╷
    //  ─┼──┼──┼──┼──┼──┼──┼──┼──┼─>  ╷    ╷
    //   ╵              ╵             ╵\    \
    //                                  \    `-- half height of minor tick
    //                                   \
    //                                    `-- half height of major tick
    $g["half height of major tick"] = 5;
    $g["half height of minor tick"] = 3;

    // The arrow: (Picture in UTF-8, please use an UTF-8 aware editor)
    //       ╷      │
    //      /│\     ├ $g["arrow height"]
    //     / │ \    │
    //     ___
    //       `- $g["half width of arrow"]
    //
    // The width of the arrow is really 2*$g["half width of arrow"] - 1,
    // since the axis takes up one pixel.
    $g["arrow height"] = 15;
    $g["half width of arrow"] = 5;

    $g["font"] = 1; // The font system of PHP. Each built-in font has a number.

    $g["im"] = @ImageCreate($g["xsize"], $g["ysize"])
        or error_page("Cannot Initialize new GD image stream.");
    $g["background"] = ImageColorAllocate($g["im"], 255, 255, 255);
    $g["text"] = ImageColorAllocate($g["im"], 233, 14, 91);
    $g["black"] = ImageColorAllocate($g["im"], 0, 0, 0);
    $g["light gray"] = ImageColorAllocate($g["im"], 170, 170, 170);
    $g["Light gray"] = ImageColorAllocate($g["im"], 210, 210, 210);

    $g["colors"][] = ImageColorAllocate($g["im"], 255, 255, 255);  // white
    $g["text colors"][] = $g["text"];
    $g["colors"][] = ImageColorAllocate($g["im"], 255, 255, 153);  // light yellow
    $g["text colors"][] = $g["text"];
    $g["colors"][] = ImageColorAllocate($g["im"], 255, 255, 0);    // yellow
    $g["text colors"][] = $g["text"];
    $g["colors"][] = ImageColorAllocate($g["im"], 255, 153, 102);  // kind of pink
    $g["text colors"][] = $g["text"];
    $g["colors"][] = ImageColorAllocate($g["im"], 0, 204, 51);     // green
    $g["text colors"][] = $g["black"];
    $g["colors"][] = ImageColorAllocate($g["im"], 0, 153, 255);    // light blue
    $g["text colors"][] = $g["black"];
    $g["colors"][] = ImageColorAllocate($g["im"], 0, 0, 255);      // blue
    $g["text colors"][] = ImageColorAllocate($g["im"], 255, 127, 127);
    $g["colors"][] = ImageColorAllocate($g["im"], 0, 0, 127);      // darker blue with white text
    $g["text colors"][] = ImageColorAllocate($g["im"], 255, 255, 255);
    $g["colors"][] = ImageColorAllocate($g["im"], 0, 0, 0);        // black with wite text
    $g["text colors"][] = ImageColorAllocate($g["im"], 255, 255, 255);
}

function make_canvas()
{
    global $g, $b, $l;
    $x1 = $g["yaxis"];
    $x2 = $x1 + $g["xcanvas"];
    for ($i = 1; $i <= $g["max number of cards"]; $i++) {
        $y = $g["ycanvas"] + $g["margin top"] - 1 +
            $g["space between arrow and canvas"] - $i * $g["card height"];
        ImageLine(
            $g["im"],
            $x1,
            $y,
            $x2,
            $y,
            // Darker line every 10th card
            (($i % 10) != 0) ? $g["Light gray"] : $g["light gray"]
        );
    }

    if (
        strlen(sprintf(
            $l["Number of new cards: %s"],
            $b["number of new cards"]
        )) * ImageFontWidth($g["font"]) + $g["yaxis"] + 50 < $g["xsize"]
    ) {
        ImageString(
            $g["im"],
            $g["font"],
            $g["yaxis"] + 50,
            2,
            sprintf(
                $l["Number of new cards: %s"],
                $b["number of new cards"]
            ),
            $g["text"]
        );
    }

    if (
        (
            strlen(sprintf(
                $l["Number of short term cards: %s"],
                $b["number of short term cards"]
            )) * ImageFontWidth($g["font"]) + $g["yaxis"] + 50 < $g["xsize"]
        )
        && $b["number of short term cards"] != 0
    ) {
        ImageString(
            $g["im"],
            $g["font"],
            $g["yaxis"] + 50,
            10,
            sprintf(
                $l["Number of short term cards: %s"],
                $b["number of short term cards"]
            ),
            $g["text"]
        );
    }
}

function draw_xaxis()
{
    global $g;

    $im =& $g["im"];
    $x1 = $g["yaxis"]; // The x axis starts at 0 + width of y axis
    $x2 = $g["xmax"] - $g["margin right"] + $g["arrow height"];
    $y = $g["ymax"] - $g["xaxis"];

    ImageLine($im, $x1, $y, $x2, $y, $g["black"]);


    // The arrow:
    $x1 = $x2;
    $x2 = $g["xmax"] - $g["margin right"];
    $y1 = $y;
    $y2 = $y - $g["half width of arrow"];

    ImageLine($im, $x1, $y1, $x2, $y2, $g["black"]);

    $y2 = $y + $g["half width of arrow"];

    ImageLine($im, $x1, $y1, $x2, $y2, $g["black"]);

    // The dates:

    $x = $g["yaxis"];
    $y1 = $y - $g["half height of major tick"];
    $y2 = $y + $g["half height of major tick"];
    $current = $g["first"];
    for ($i = 0; $i < $g["number of days"]; $i++) {
        ImageLine($im, $x, $y1, $x, $y2, $g["black"]);

        ImageString(
            $im,
            $g["font"],
            $x + $g["dayspace"] / 2 - ImageFontWidth($g["font"]) * 5 / 2, //centered
            $y + ImageFontHeight($g["font"]),
            substr($current, 0, 4) . "-",
            $g["text"]
        );
        ImageString(
            $im,
            $g["font"],
            $x + $g["dayspace"] / 2 - ImageFontWidth($g["font"]) * 5 / 2, //centered
            $y + 2 * ImageFontHeight($g["font"]),
            substr($current, 5, 2) . "-" . substr($current, 8, 2),
            $g["text"]
        );

        $current = date_plus($current, 1);
        $x += $g["dayspace"];
    }
    ImageLine($im, $x, $y1, $x, $y2, $g["black"]); // and the last line
}

function draw_yaxis()
{
    global $g;

    $im =& $g["im"];
    $x = $g["yaxis"]; // The y axis starts at 0 + width of y axis

    $y1 = $g["ymax"] - $g["xaxis"];
    $y2 = $g["margin top"] - $g["arrow height"];

    ImageLine($im, $x, $y1, $x, $y2, $g["black"]);


    // The arrow:
    $y1 = $y2;
    $y2 = $g["margin top"];
    $x1 = $x;
    $x2 = $x - $g["half width of arrow"];

    ImageLine($im, $x1, $y1, $x2, $y2, $g["black"]);

    $x2 = $x + $g["half width of arrow"];

    ImageLine($im, $x1, $y1, $x2, $y2, $g["black"]);

    // The numbers
    $x1 = $x - $g["half height of major tick"];
    $x2 = $x + $g["half height of major tick"];
    $y = $g["ymax"] - $g["xaxis"];

    $draw = 1; // FIXME: Better name of variable
    while ($g["card height"] * $draw < ImageFontHeight($g["font"])) {
        $draw *= 10;
    }
    for ($i = 0; $i <= $g["max number of cards"]; $i++) {
        if (($i % $draw) == 0) {
            ImageLine($im, $x1, $y, $x2, $y, $g["black"]);

            // Here comes some very complicated code. The third argument means
            // "right centered, 5 pixels to the left of the horizontal line".
            ImageString(
                $im,
                $g["font"],
                $x - ImageFontWidth($g["font"]) * (floor(log10(($i == 0) ? 1 : $i)) + 1)
                - $g["half height of major tick"] - 5,
                $y - ImageFontHeight($g["font"]) / 2,
                $i,
                $g["text"]
            );
        } else {
            ImageLine(
                $im,
                $x - $g["half height of minor tick"],
                $y,
                $x + $g["half height of minor tick"],
                $y,
                $g["black"]
            );
        }
        $y -= $g["card height"];
    }
}


function draw_image()
{
    global $g;
    header("Content-type: image/png");
    // Try to disable caching...
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    // always modified
    header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");                          // HTTP/1.0

    ImagePng($g["im"]);
}

//* Draws the cards that expire on $date
function draw_day($date, $x)
{
    global $g, $debug;

    if ($debug) {
        echo "<p>In draw_day(\"" . htmlentities($date) . "\", $x)...</p>\n";
    }

    $im =& $g["im"];
    $y = $g["ymax"] - $g["xaxis"];
    $total = 0;

    if (!isset($g["cards"][$date])) {
        $g["cards"][$date] = array();
    }
    ksort($g["cards"][$date]);
    foreach ($g["cards"][$date] as $created => $number) {
        if ($debug) {
            echo "<p>Drawing day $date, cards created on $created: $number. ";
            echo "</p>\n";
        } else {
            $total += $number;
            ImageRectangle(
                $im,
                $x,
                $y,
                $x + $g["dayspace"],
                $y - $number * $g["card height"],
                $g["black"]
            );
            ImageFilledRectangle(
                $im,
                $x + 1,
                $y - $number * $g["card height"] + 1,
                $x + $g["dayspace"] - 1,
                $y - 1,
                $g["colors"][repetition_count(difference($date, $created))]
            );
            // If there is enough space, we tell how old the cards will be when they expire
            if (
                $number * $g["card height"] >= ImageFontHeight($g["font"])
            ) {
                if ($number * $g["card height"] > (5 / 3) * ImageFontHeight($g["font"])) {
                    $padding = round(ImageFontHeight($g["font"]) / 3);
                } else {
                    $padding = floor(($number * $g["card height"] - ImageFontHeight($g["font"])) / 2);
                }
                ImageString(
                    $im,
                    $g["font"],
                    $x + 5,
                    $y - $number * $g["card height"] + $padding,
                    difference($date, $created),
                    $g["text colors"][repetition_count(difference($date, $created))]
                );
            }
            $y -= $number * $g["card height"];
        }
    }
    if ($total != 0) {
        ImageString(
            $im,
            $g["font"],
            $x + 5,
            $y - ImageFontHeight($g["font"]),
            $total,
            $g["text"]
        );
    }
}

function draw_days()
{
    global $g, $debug;
    if ($debug) {
        echo "<p>In draw_days()...</p>\n";
    }
    $x = $g["yaxis"];
    $current = $g["first"];
    while (make_timestamp($current) <= make_timestamp($g["last"])) {
        draw_day($current, $x);
        $current = date_plus($current, 1);
        $x += $g["dayspace"];
    }
}

function debug()
{
    global $l, $b, $c, $g, $html;
    echo "<h1>\$b</h1>\n";
    echo "<pre>\n";
    print_r($b);
    echo "</pre>\n";

    echo "<h1>\$g</h1>\n";
    echo "<pre>\n";
    print_r($g);
    echo "</pre>\n\n";

    echo "<h1>\$c</h1>\n<pre>\n";
    print_r($c);
    echo "</pre>\n";

    echo "<h1>\$html</h1>\n<pre>\n";
    print_r($html);
    echo "</pre>\n";
}

function nextmonth($year, $month)
{
    if ($month == 12) {
        return array($year + 1, 1);
    } else {
        return array($year, $month + 1);
    }
}

//* void main(void), so to speak

assert_lesson($lesson);

ob_start();

if ($debug) {
    echo "<h1>" . sprintf($l["lesson %s"], lesson_user()) . "</h1>\n\n";
}

//* Find out which cards we have.
$n = read_card_directory();
make_gcards();

if (is_null($year)) {
    list($firstyear, $firstmonth, $firstday) = parse_date($g["first"]);
    list($lastyear, $lastmonth, $lastday) = parse_date($g["last"]);

//   $y = $firstyear;
//   $m = $firstmonth;
//   for(;
//       $y <= $lastyear && $m <= (($y == $lastyear)?$lastmonth:12);
//       list($y, $m) = nextmonth($y, $m))
//   {
//     echo "$y-$m:<br/><img src=\"graph/year=$y&amp;month=". twodigits($m) . "\"/><br/>\n";
//   }
    for ($y = $firstyear; $y <= $lastyear; $y++) {
        if ($y >= $firstyear + (($firstmonth >= 12) ? 2 : 1) && $y <= $lastyear - 1) {
            echo "<a href=\"graph/year=$y\">$y</a><br/>\n";
        } else {
            echo "$y:<br/><img src=\"graph/year=$y\"/><br/>\n";
        }
    }
} else {
    //* Prepare image drawing
    make_imvars();

    if ($debug) {
        draw_days();
        debug();
    } else {
        make_canvas();
        draw_xaxis();
        draw_yaxis();
        draw_days();
        draw_image();
        exit;
    }
}

$body = ob_get_clean();

$url = path_join_urls('..', $url);
$url['this'] = 'graph';
$url['thislesson'] = './';

$smarty = get_smarty();
$smarty->assign('title', $l["graph page title %s"], lesson_user());
$smarty->assign('relative_url', urlencode(lesson_filename()) . "/");
$smarty->assign('lesson_name', lesson_user());
$smarty->assign('body', $body);

$smarty->assign('l', $l);
$smarty->assign('url', $url);
do_http_headers();
$smarty->display('layout.tpl');
