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

function exit_json($status, $body)
{
    header("HTTP/1.1 $status");
    header("Content-type: application/json");
    echo json_encode($body);
    exit;
}

class ApiRouter
{
    private $data = array();
    private $request_parts = array();

    public function __construct($request)
    {
        $this->request_parts = explode("/", $request);
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

    public function __invoke($endpoint)
    {
        $endpoint_parts = explode("/", $endpoint);
        $len = count($endpoint_parts);
        $this->data = array();

        if (count($this->request_parts) != $len) {
            return false;
        }
        for ($i = 0; $i < $len; $i++) {
            if ($this->request_parts[$i] != $endpoint_parts[$i]) {
                if (
                    str_starts_with($endpoint_parts[$i], "{") &&
                    str_ends_with($endpoint_parts[$i], "}")
                ) {
                    $this->data[substr($endpoint_parts[$i], 1, -1)] = (
                        $this->request_parts[$i]
                    );
                } else {
                    return false;
                }
            }
        }
        return true;
    }
}

$request = (
    $_SERVER["REQUEST_METHOD"] . " " .
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);
$router = new ApiRouter(request: $request);
$base_path = parse_url($c["webdir"], PHP_URL_PATH);

if ($router("GET $base_path/{lesson}/cards.json")) {
    assert_lesson($router->lesson);

    $db = get_db();
    $stmt = $db->prepare(
        "SELECT * FROM cards JOIN lesson2cards " .
        "ON lesson2cards.card_id = cards.card_id " .
        "WHERE lesson_id=?"
    );
    $stmt->bind_param("i", $b["lesson"]);
    $stmt->execute();
    $cards = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    exit_json(
        status: "200 OK",
        body: $cards
    );
} elseif ($router("PUT $base_path/{lesson}/pluginsettings.json")) {
    assert_lesson($router->lesson);

    $data = json_decode(file_get_contents("php://input"));

    $validator = new JsonSchema\Validator();
    $validator->validate($data, (object)[
        '$ref' => 'file://' . realpath('pluginsettings-schema.json')
    ]);

    if (!$validator->isValid()) {
        $errors = array();
        foreach ($validator->getErrors() as $error) {
            $errors[] = $error['message'];
        }
        exit_json(
            status: "400 Bad Request",
            body: array(
                "success" => false,
                "data" => $data,
                "error" => implode("\n", $errors),
                "errors" => $validator->getErrors()
            )
        );
    }
    $db = get_db();
    $stmt = $db->prepare("UPDATE lessons SET plugins=? WHERE lesson_id=?");
    $stmt->bind_param("si", json_encode($data), $b["lesson"]);
    $stmt->execute();
    exit_json(
        status: "200 OK",
        body: array(
            "success" => true,
            "valid" => $validator->isValid(),
        )
    );
} else {
    exit_json(
        status: "404 Not Found",
        body: array(
            "request" => $request,
            "base_path" => $base_path,
            "error" => "Not found"
        )
    );
}
