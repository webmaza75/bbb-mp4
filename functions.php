<?php
// Разместить иконку с кнопкой (с закругленными углами)
/**
 * @param $img - картинка - основа
 * @param $src - указатель на ресурс (вставляемую картинку)
 * @param $startXbtn - начало кнопки по X
 * @param $startYbtn - начало кнопки по Y
 * @param $imgSize - размер картинки-ресурса
 * @param bool|false $disabled - доступность кнопки (disabled - осветление иконки)
 */
function drawButtonWithIcon($img, $startXbtn, $startYbtn, $imgSize = [25, 0], $src = '', $disabled = false)
{
    $buttonFilledColor = imagecolorallocate($img, 240, 240, 240);
    $buttonBorderColor = imagecolorallocate($img, 190, 190, 190);
    $endXbtn = 8 + $startXbtn + $imgSize[0] + 8;
    $endYbtn = $startYbtn + 30;

    drawRoundRectangle($img, $startXbtn, $startYbtn, $endXbtn, $endYbtn, 3, $buttonFilledColor); // фон
    drawRoundRectangleNotFilled($img, $startXbtn, $startYbtn, $endXbtn, $endYbtn, 3, $buttonBorderColor); // бордер
    if ('' !== $src) {
        $source = imagecreatefrompng($src);
        if ($disabled) {
            imagefilter($source, IMG_FILTER_BRIGHTNESS, 100);
        }
        imagecopy($img, $source, $startXbtn + 8, $startYbtn + (30 - $imgSize[1]) / 2, 0, 0, $imgSize[0], $imgSize[1]);
    }
}
// Разместить только иконку
function drawIcon($img, $startX, $startY, $imgSize = [0, 0], $src, $filter = false, $disabled = false)
{
    $source = imagecreatefrompng($src);
    if (true == $filter) {
        imagefilter($source, IMG_FILTER_NEGATE);
        imagefilter($source, IMG_FILTER_BRIGHTNESS, 70);
    }
    if ($disabled) {
        imagefilter($source, IMG_FILTER_BRIGHTNESS, 100);
    }
    imagecopy($img, $source, $startX + 8, $startY + (30 - $imgSize[1]) / 2, 0, 0, $imgSize[0], $imgSize[1]);
}

// Для кнопок с закругленными углами (фон)
function drawRoundRectangle($img, $x1, $y1, $x2, $y2, $radius, $color)
{
    imagefilledrectangle($img, $x1+$radius, $y1, $x2-$radius, $y2, $color);
    imagefilledrectangle($img, $x1, $y1+$radius, $x1+$radius-1, $y2-$radius, $color);
    imagefilledrectangle($img, $x2-$radius+1, $y1+$radius, $x2, $y2-$radius, $color);

    imagefilledarc($img,$x1+$radius, $y1+$radius, $radius*2, $radius*2, 180 , 270, $color, IMG_ARC_PIE);
    imagefilledarc($img,$x2-$radius, $y1+$radius, $radius*2, $radius*2, 270 , 360, $color, IMG_ARC_PIE);
    imagefilledarc($img,$x1+$radius, $y2-$radius, $radius*2, $radius*2, 90 , 180, $color, IMG_ARC_PIE);
    imagefilledarc($img,$x2-$radius, $y2-$radius, $radius*2, $radius*2, 360 , 90, $color, IMG_ARC_PIE);
}
// Для кнопок с закругленными углами (border)
function drawRoundRectangleNotFilled($img, $x1, $y1, $x2, $y2, $radius, $color) {
    imageline($img, $x1+$radius, $y1, $x2-$radius, $y1, $color);
    imageline($img, $x1+$radius, $y2, $x2-$radius, $y2, $color);
    imageline($img, $x1, $y1+$radius, $x1, $y2-$radius, $color);
    imageline($img, $x2, $y1+$radius, $x2, $y2-$radius, $color);

    imagearc($img,$x1+$radius, $y1+$radius, $radius*2, $radius*2, 180 , 270, $color);
    imagearc($img,$x2-$radius, $y1+$radius, $radius*2, $radius*2, 270 , 360, $color);
    imagearc($img,$x1+$radius, $y2-$radius, $radius*2, $radius*2, 90 , 180, $color);
    imagearc($img,$x2-$radius, $y2-$radius, $radius*2, $radius*2, 360 , 90, $color);
}