<?php
require_once "resources/phpMQTT.php";

/**
 * Send a message via MQTT
 */
function sendMessage(string $topic, string $message, phpMQTT $mqtt): bool {
    if ($mqtt->connect()) {
        $mqtt->publish($topic, $message, 0, 1);
        $mqtt->close();
        return true;
    }
    return false;
}

/**
 * Standard JSON response
 */
function jsonResponse(string $status, string $topic = "", string $message = ""): void {
    header("Content-Type: application/json");
    echo json_encode([
        "status"  => $status,
        "topic"   => $topic,
        "message" => $message
    ]);
    exit;
}

// ✅ Config (ideally from env vars or config file)
$server     = "192.168.1.20";
$port       = 1883;
$clientId   = "Web PHP MQTT Client";
$username   = "ahmsNode";
$password   = "ahms2013";

// ✅ Collect inputs safely
$topic   = trim($_GET["topic"]   ?? "");
$message = trim($_GET["message"] ?? "");

// ✅ Validate input
if ($topic === "" || $message === "") {
    jsonResponse("invalid arguments", $topic, $message);
}

// ✅ Setup MQTT
$mqtt = new phpMQTT($server, $port, $clientId, $username, $password);

// ✅ Attempt publish
if (sendMessage($topic, $message, $mqtt)) {
    jsonResponse("ok", $topic, $message);
} else {
    jsonResponse("failed to send message", $topic, $message);
}

