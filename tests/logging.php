<?php
$logFile = 'D:/xampp/htdocs/recommend_movie/logs/debug_log.txt'; // Local file path
$rawPostData = "Hello How are you";
file_put_contents($logFile, "Raw POST Data: " . $rawPostData . PHP_EOL, FILE_APPEND);
echo "Logging test complete.";
?>