<?php
$file = 'C:\\xampp\\htdocs\\p_edunote\\public\\assets\\css\\app.css';
$text = file_get_contents($file);

$start = strpos($text, '/* === SIDEBAR COLLAPSE === */');
$end = strpos($text, '/* === RESPONSIVE === */');
if ($end === false) {
    $end = strlen($text);
}

$new = file_get_contents('C:\\xampp\\htdocs\\p_edunote\\_tmp_css_new_section.txt');

$new_text = substr($text, 0, $start) . $new . substr($text, $end);
file_put_contents($file, $new_text);
echo 'CSS sidebar section replaced';
