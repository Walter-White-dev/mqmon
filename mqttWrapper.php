<?php
require_once "resources/phpMQTT.php";

// Show errors in development (remove or adjust in production)
ini_set("display_errors", 1);
error_reporting(E_ALL);

// Default connection config
$config = [
    "server"     => $_GET["server"]    ?? null,
    "port"       => $_GET["port"]      ?? null,
    "userName"   => $_GET["userName"]  ?? null,
    "password"   => $_GET["password"]  ?? null,
    "identifier" => "PHP MQTT Client"
];

// Dispatcher
$action = $_GET["action"] ?? null;
switch ($action) {
    case "getTopic":
        getTopic($config);
        break;
    case "pubMess":
        pubMess($config);
        break;
    default:
        displayJson("invalid action", "", "", $config);
        break;
}

/**
 * Create an MQTT connection
 */
function getMQTT(array $config): ?phpMQTT {
    if (empty($config["server"]) || empty($config["port"])) {
        return null; // invalid
    }
    return new phpMQTT(
        $config["server"],
        (int) $config["port"],
        $config["identifier"],
        $config["userName"] ?? "",
        $config["password"] ?? ""
    );
}

/**
 * Get messages from a topic
 */
function getTopic(array $config): void {
    $topic = trim($_GET["topic"] ?? "");

    if ($topic === "") {
        displayJson("invalid topic", "", "", $config);
        return;
    }

    $mqtt = getMQTT($config);
    if (!$mqtt || !$mqtt->connect()) {
        displayJson("failed to connect", $topic, "", $config);
        return;
    }

    $topics[$topic] = ["qos" => 0, "function" => "procmsg"];
    $mqtt->subscribe($topics);

    if ($mqtt->proc() === 0) {
        displayJson("no message", $topic, "", $config);
    }
    $mqtt->close();
}

/**
 * Publish a message
 */
function pubMess(array $config): void {
    $topic   = trim($_GET["topic"]   ?? "");
    $message = trim($_GET["message"] ?? "");
    $retain  = ($_GET["retain"] ?? "false") === "true";
    $qos     = 0;

    if ($topic === "" && $message === "") {
        displayJson("invalid message & topic", "", "", $config);
        return;
    }
    if ($topic === "") {
        displayJson("invalid topic", "", $message, $config);
        return;
    }
    if ($message === "") {
        displayJson("invalid message", $topic, "", $config);
        return;
    }

    $mqtt = getMQTT($config);
    if (!$mqtt || !$mqtt->connect()) {
        displayJson("failed to connect", $topic, $message, $config);
        return;
    }

    $mqtt->publish($topic, $message, $qos, $retain);
    $mqtt->close();
    displayJson("ok", $topic, $message, $config);
}

/**
 * Message callback
 */
function procmsg($topic, $message): void {
    displayJson("ok", $topic, $message, []);
}

/**
 * Return JSON response
 */
function displayJson(string $status, string $topic, string $message, array $config): void {
    // Do NOT expose password in output
    $response = [
        "status"   => $status,
        "topic"    => $topic,
        "message"  => $message,
        "server"   => $config["server"],
        "port"     => $config["port"],
        "userName" => $config["userName"],
        //"password" => $config["password"] // security risk!
    ];

    header("Content-Type: application/json");
    echo json_encode($response);
}
