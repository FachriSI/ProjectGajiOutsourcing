<?php
$content = file_get_contents('unit_import_debug_3.txt');
if (mb_detect_encoding($content, 'UTF-16LE', true)) {
    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
}
file_put_contents('unit_import_debug_pure_3.txt', $content);
