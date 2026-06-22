<?php
$html = file_get_contents('https://www.harti.gov.lk/daily-price.php');
preg_match_all('/href=["\']([^"\']+\.pdf)["\']/i', $html, $m);
print_r(array_slice($m[1], 0, 10));
