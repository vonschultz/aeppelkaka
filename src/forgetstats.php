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
unset($g); // the graph variable
require_once("config.php");
load_config();
require_once("backend.php");
require_once("html.php");
require_once("graph_" . $c["lang"] . ".php");
$debug = isset($_REQUEST['debug']) ? $_REQUEST['debug'] : false;
$lesson = $_REQUEST['lesson'];

load_config();


function make_gforg()
{
    global $g, $b, $debug;

    $g["number of entries"] = 0;

    $db = get_db();
    $stmt = $db->prepare(
        "SELECT dif, num as forg FROM `forget_stats_forg` " .
        "WHERE lesson_id=? AND dif > 0"
    );
    $stmt->bind_param("i", $b["lesson"]);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $magnitude = round(log3($row['dif']));
        $g['forg'][$magnitude][$row['dif']] = $row['forg'];
    }
    $result->close();
    ksort($g["forg"]);

    $stmt = $db->prepare(
        "SELECT dif, num as rem FROM `forget_stats_rem` " .
        "WHERE lesson_id=? AND dif > 0"
    );
    $stmt->bind_param("i", $b["lesson"]);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $magnitude = round(log3($row['dif']));
        $g['rem'][$magnitude][$row['dif']] = $row['rem'];
    }
    $result->close();

    ksort($g["rem"]);

    foreach ($g["forg"] as $mag => $array) {
        ksort($array);
        $g["forg"][$mag] = $array;
    }

    foreach ($g["rem"] as $mag => $array) {
        ksort($array);
        $g["rem"][$mag] = $array;
    }

    $g["number of entries"] = sizeof($g["rem"]);
}

//* The third logarithm of $number. The age of a card that
//  has been seen x times is 3^x, measured from the day
//  the card was created. Thus, this function can be used
//  to get the number of times the card has been seen.
//
//  In reality, when the user tests himself, the card is
//  moved 2*(current age) days into the future. If the user
//  does not visit aeppelkaka each day, the new age might
//  not equal 3^x.
function log3($number)
{
    return log10($number) / log10(3);
}

//* Creates the image variables: size, $g["im"], and so on.
function make_imvars()
{
    global $g, $c;

    $g["dayspace"] = 40; // The width of each day in pixels;
    $g["space between arrow and canvas"] = 10;
    $g["ysize"] = $c["diagram height"]; // The total height of the image
    $g["ymax"] = $g["ysize"] - 1;
    $g["yaxis"] = 30; // The width of the y axis
    $g["xaxis"] = 30; // The height of the x axis
    $g["margin top"] = 10; // The margin at the top of the image
    $g["margin top"] += (
        ($g["ysize"] - $g["space between arrow and canvas"] -
         $g["xaxis"] - $g["margin top"]) % 100
    );
    $g["margin right"] = 40;
    $g["ycanvas"] = (
        $g["ysize"] - $g["xaxis"] - $g["margin top"] -
        $g["space between arrow and canvas"]
    );
    $g["card height"] = $g["ycanvas"] / 100;
    $g["xcanvas"] = $g["dayspace"] * $g["number of entries"];
    $g["xsize"] = ($g["xcanvas"] + $g["yaxis"] + $g["margin right"] +
                   $g["space between arrow and canvas"]);
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
    $g["arrow height"] = 10;
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
}

function make_canvas()
{
    global $g, $b, $l;
    $x1 = $g["yaxis"];
    $x2 = $x1 + $g["xcanvas"];
    for ($i = 1; $i <= 100; $i++) {
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
    reset($g["forg"]);
    foreach ($g["forg"] as $mag => $value) {
        ImageLine($im, $x, $y1, $x, $y2, $g["black"]);

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
    for ($i = 0; $i <= 100; $i++) {
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

function draw_rectangle($im, $left, $top, $right, $bottom, $fill_color)
{
    global $g;
    if ($left < $right and $top < $bottom) {
        ImageRectangle(
            $im,
            $left,
            $top,
            $right,
            $bottom,
            $g["black"]
        );
    }
    if ($left + 1 < $right - 1 and $top + 1 < $bottom - 1) {
        ImageFilledRectangle(
            $im,
            $left + 1,
            $top + 1,
            $right - 1,
            $bottom - 1,
            $fill_color
        );
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
function draw_day($mag, $x)
{
    global $g, $debug;

    if ($debug) {
        echo "<p>In draw_day(\"" . htmlentities($mag) . "\", $x)...</p>\n";
    }

    $im =& $g["im"];
    $y = $g["ymax"] - $g["xaxis"];

    $total = 0;
    $youngest = pow(3, $mag + 1);
    $oldest = 0;

    if (!isset($g["rem"][$mag])) {
        $g["rem"][$mag] = array();
    }
    reset($g["rem"][$mag]);
    foreach ($g["rem"][$mag] as $age => $num) {
        if ($age < $youngest) {
            $youngest = $age;
        }

        if ($age > $oldest) {
            $oldest = $age;
        }

        if (!isset($g["forg"][$mag])) {
            $g["forg"][$mag] = array();
        }
        if (!isset($g["forg"][$mag][$age])) {
            $g["forg"][$mag][$age] = 0;
        }
        $total += $num + $g["forg"][$mag][$age];
    }


    if ($youngest == $oldest) {
        $current = $youngest;
    } else {
        $current = $youngest . "-" . $oldest;
    }
    ImageString(
        $im,
        $g["font"],
        round(
            $x + $g["dayspace"] / 2
            - ImageFontWidth($g["font"]) * strlen($current) / 2 //centered
        ),
        round($y + 0.7 * ImageFontHeight($g["font"])),
        $current,
        $g["text"]
    );
    $current = "(" . $total . ")";
    ImageString(
        $im,
        $g["font"],
        round(
            $x + $g["dayspace"] / 2
            - ImageFontWidth($g["font"]) * strlen($current) / 2 //centered
        ),
        round($y + 2.5 * ImageFontHeight($g["font"])),
        $current,
        $g["text"]
    );

    $percentage = 0;
    reset($g["rem"][$mag]);
    foreach ($g["rem"][$mag] as $age => $num) {
        $rem = $num * 100 / $total;
        $percentage += $rem;
        draw_rectangle(
            $im,
            left: $x,
            top: round($y - $rem * $g["card height"]),
            right: $x + $g["dayspace"],
            bottom: $y,
            fill_color: $g["colors"][$mag]
        );

        // If there is enough space, we tell how old the cards will be when they expire
        if ($rem * $g["card height"] >= ImageFontHeight($g["font"])) {
            if ($rem * $g["card height"] > (5 / 3) * ImageFontHeight($g["font"])) {
                $padding = round(ImageFontHeight($g["font"]) / 3);
            } else {
                $padding = ($rem * $g["card height"] - ImageFontHeight($g["font"])) / 2;
            }

            ImageString(
                $im,
                $g["font"],
                $x + 5,
                round($y - $rem * $g["card height"] + $padding),
                $age,
                $g["text colors"][$mag]
            );
            if ($debug) {
                echo "<p>Printing \$g[\"rem\"][$mag][$age] = " . $g["rem"][$mag][$age] . "</p>";
            }
        }


        $y -= round($rem * $g["card height"]);
    }

    if (round($percentage, 1) == 100) {
        $perstring = "100%";
    } else {
        $perstring = sprintf("%.1f%%", $percentage);
    }

    $xpadding = $g["dayspace"] * .47 - ImageFontWidth($g["font"]) * strlen($perstring) / 2;

    ImageString(
        $im,
        $g["font"],
        round($x + $xpadding),
        round($y - ImageFontHeight($g["font"])),
        $perstring,
        $g["black"]
    );
}

function draw_days()
{
    global $g, $debug;
    if ($debug) {
        echo "<p>In draw_days()...</p>\n";
    }
    $x = $g["yaxis"];
    reset($g["forg"]);
    foreach ($g["forg"] as $current => $value) {
        draw_day($current, $x);

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

//* void main(void), so to speak

if ($debug) {
    ob_start();
}

assert_lesson($lesson);

if ($debug) {
    echo "<h1>" . sprintf($l["lesson %s"], lesson_user($lesson)) . "</h1>\n\n";
}

//* Find out which cards we have.
$n = read_card_directory();
make_gforg();

//* Prepare image drawing
make_imvars();

if ($debug) {
    draw_days();
    debug();

    $body = ob_get_clean();

    $url = path_join_urls('..', $url);
    $url['this'] = 'forgetstats';
    $url['thislesson'] = './';

    $smarty = get_smarty();
    $smarty->assign(
        'title',
        sprintf($l["forgetstats debugpage title %s"], lesson_user())
    );
    $smarty->assign('relative_url', urlencode(lesson_filename()) . "/");
    $smarty->assign('lesson_name', lesson_user());
    $smarty->assign('body', $body);

    $smarty->assign('l', $l);
    $smarty->assign('url', $url);
    do_http_headers();
    $smarty->display('layout.tpl');
} else {
    make_canvas();
    draw_xaxis();
    draw_yaxis();
    draw_days();
    draw_image();
}
