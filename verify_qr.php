<?php
try {
    $qr = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(100)->generate('test');
    if (strlen($qr) > 0) {
        echo "QR Library OK. Length: " . strlen($qr);
    } else {
        echo "QR Library Failed (Empty)";
    }
} catch (\Exception $e) {
    echo "QR Error: " . $e->getMessage();
}
