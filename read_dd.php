<?php
$content = file_get_contents('dd_output_3.txt');
if (mb_detect_encoding($content, 'UTF-16LE', true)) {
    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
}
file_put_contents('dd_utf8_pure_3.txt', $content);
