<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RoboRecipes MQTT on <?= gethostname(); ?></title>
    <link rel="stylesheet" href="resources/style.css" type="text/css">
    <script src="resources/mqtts.js" defer></script>
    <script>
        document.addEventListener("DOMContentLoaded", initPage);
    </script>
</head>
<body>
    <main>
        <table id="messageTable">
            <caption>RoboRecipes MQTT on <?= gethostname(); ?></caption>
            <thead>
                <tr>
                    <th scope="col">Topic</th>
                    <th scope="col">Message</th>
                </tr>
            </thead>
            <tbody id="messages">
                <!-- JavaScript will auto-populate here -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">RoboRecipes.com - Arduino Home Monitoring System</td>
                </tr>
