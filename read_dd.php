<?php
$content = file_get_contents('contract_debug_4.txt');
if (mb_detect_encoding($content, 'UTF-16LE', true)) {
    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
}
file_put_contents('contract_debug_pure_4.txt', $content);
