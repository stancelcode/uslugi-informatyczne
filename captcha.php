<?php
session_start();
header('Content-Type: image/png');

$width = 80;
$height = 30;

$image = imagecreate($width, $height);
$bg = imagecolorallocate($image, 245, 245, 245);
$textColor = imagecolorallocate($image, 0, 0, 0);

$a = rand(1, 9);
$b = rand(1, 9);
$_SESSION['captcha_result'] = $a + $b;

imagestring($image, 5, 10, 8, "$a + $b = ?", $textColor);
imagepng($image);
imagedestroy($image);
?>