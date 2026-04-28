<?php
    session_start();

    // Generate random string (lebih kuat dari angka saja)
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha = substr(str_shuffle($chars), 0, 5);
    $_SESSION['captcha'] = $captcha;

    // Ukuran gambar
    $width = 140;
    $height = 50;

    $image = imagecreatetruecolor($width, $height);

    // Warna background random
    $bgColor = imagecolorallocate($image, rand(200,255), rand(200,255), rand(200,255));
    imagefill($image, 0, 0, $bgColor);

    // Tambahkan noise (titik)
    for ($i = 0; $i < 100; $i++) {
        $noiseColor = imagecolorallocate($image, rand(100,200), rand(100,200), rand(100,200));
        imagesetpixel($image, rand(0,$width), rand(0,$height), $noiseColor);
    }

    // Tambahkan garis acak
    for ($i = 0; $i < 5; $i++) {
        $lineColor = imagecolorallocate($image, rand(100,200), rand(100,200), rand(100,200));
        imageline($image, rand(0,$width), rand(0,$height), rand(0,$width), rand(0,$height), $lineColor);
    }

    // Path font (gunakan font TTF)
    $fontPath = '../../assets/font/ClassicalDiary.ttf';

    // Tulis teks dengan rotasi random
    for ($i = 0; $i < strlen($captcha); $i++) {
        $textColor = imagecolorallocate($image, rand(0,100), rand(0,100), rand(0,100));
        $angle = rand(-25, 25);
        imagettftext(
            $image,
            20,
            $angle,
            20 + ($i * 20),
            rand(30,40),
            $textColor,
            $fontPath,
            $captcha[$i]
        );
    }

    // Output
    header("Content-type: image/png");
    imagepng($image);
    imagedestroy($image);
?>