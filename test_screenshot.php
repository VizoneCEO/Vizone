<?php
$url = 'https://rootsmarket.com.mx';

// Test Thum.io
$thumUrl = "https://image.thum.io/get/width/1200/crop/800/" . $url;
$headersThum = get_headers($thumUrl, 1);

// Test Microlink
$mlUrl = "https://api.microlink.io?url=" . urlencode($url) . "&screenshot=true&meta=false";
$mlContent = file_get_contents($mlUrl);

// Test WordPress MShots
$wpUrl = "https://s0.wordpress.com/mshots/v1/" . urlencode($url) . "?w=1200";
$headersWp = get_headers($wpUrl, 1);

echo "Thum.io status: " . ($headersThum[0] ?? 'Failed') . "\n";
echo "Microlink content: " . substr($mlContent, 0, 100) . "...\n";
echo "MShots status: " . ($headersWp[0] ?? 'Failed') . "\n";
