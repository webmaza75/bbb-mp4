<?php
/**
 * @use php test.php --src=./resources/layout.xml --css=./resources/style/css/BBBDefault.css --width=1280 --height=720
 * php test.php --src=./resources/layout.xml --css=./resources/style/css/BBBDefault.css
 */
//
$options = getopt('', ['src:', 'css:', 'width::', 'height::']);
// Источник src и css
$resFileName = realpath($options['src']);
$cssFileName = realpath($options['css']);
$w = $options['width'] ? : 1280;
$h = $options['height'] ? : 720;
// Список участников вебинара (внешние данные)
$students = [
    ['name' => 'Воротилов Глеб', 'answer' => 'добрый вечер', 'time' => '20:00'],
    ['name' => 'Еропкин Виталий', 'answer' => 'Всем добрый вечер', 'time' => '20:00'],
    ['name' => 'Мамонов Виктор', 'answer' => 'Здравствуйте!', 'time' => '20:01'],
    ['name' => 'Фролов Максим', 'answer' => 'Добрый вечер', 'time' => '20:02'],
];
$master = 'Степанцев Альберт'; // внешние данные


require __DIR__ . '/autoload.php';

$layout = new \ProfIT\Bbb\Layout($resFileName, 'defaultlayout');
$layout->setStyleSheet($cssFileName);
$image = new \ProfIT\Bbb\layout\Image([
    'w' => $w,
    'h' => $h
]);

$image->loadLayout($layout);
$image->generatePng('test.png');

// После генерации общего фона с блоками
$im = imagecreatefrompng('test.png');

// Подключаемый шрифт
$font = __DIR__ . '/resources/fonts/arial.ttf';
// цвет текста
$text_color = imagecolorallocate($im, 47, 47, 47);
// Цвет background под текст
$bgcolor = imagecolorallocate($im, 204, 204, 204);

// Заплатки на существующий текст
$startX = 3;
$startY = 3;
$endX = 200;
$endY = 30;
imagefilledrectangle($im, $startX, $startY, $endX, $endY, $bgcolor);
imagefilledrectangle($im, $startX, $startY + 247, $endX, $endY + 247, $bgcolor);
imagefilledrectangle($im, $startX, $startY + 495, $endX, $endY + 495, $bgcolor);
imagefilledrectangle($im, $startX + 233, $startY, $endX + 233, $endY, $bgcolor);
imagefilledrectangle($im, $startX + 893, $startY, $endX + 893, $endY, $bgcolor);

$presentationName = 'P3P.pdf'; // Название презентации (возможно, внешние данные)
$fontSize = 11; // Размер шрифта

// Подписи к блокам (встроенные окна)
imagettftext ($im , $fontSize , 0 , 5 , 23 , $text_color , $font , 'Не трогать!' );
imagettftext ($im , $fontSize , 0 , 237 , 23 , $text_color , $font , 'Презентация: ' . $presentationName );
imagettftext ($im , $fontSize , 0 , 898 , 23 , $text_color , $font , 'Чат' );
imagettftext ($im , $fontSize , 0 , 5 , 270 , $text_color , $font , 'Пользователи: ' . count($students) );
imagettftext ($im , $fontSize , 0 , 5 , 518 , $text_color , $font , 'Веб-камеры' );

// Наложение иконок "свернуть"
$src = imagecreatefrompng(__DIR__ . '/resources/style/css/assets/images/3_minimizeButton.png');
imagefilter($src, IMG_FILTER_NEGATE);
imagefilter($src, IMG_FILTER_BRIGHTNESS, 70);
// Копирование откуда - что - отX - отY - доX - доY
$startXicon = 190;
$startYicon = 15;
imagecopy($im, $src, $startXicon, $startYicon, 0, 0, 10, 10);
imagecopy($im, $src, $startXicon + 660, $startYicon, 0, 0, 10, 10);
imagecopy($im, $src, $startXicon + 1050, $startYicon, 0, 0, 10, 10);
imagecopy($im, $src, $startXicon, $startYicon + 245, 0, 0, 10, 10);
imagecopy($im, $src, $startXicon, $startYicon + 495, 0, 0, 10, 10);

// Наложение иконок "развернуть"
$src = imagecreatefrompng(__DIR__ . '/resources/style/css/assets/images/3_increaseButton.png');
imagefilter($src, IMG_FILTER_NEGATE);
imagefilter($src, IMG_FILTER_BRIGHTNESS, 70);

$delta = 20; // смещение иконок по горизонтали относительно предыдущих
$startXicon += $delta; // Стартовая позиция для картинки по X
imagecopy($im, $src, $startXicon, $startYicon, 0, 0, 10, 10);
imagecopy($im, $src, $startXicon + 660, $startYicon, 0, 0, 10, 10);
imagecopy($im, $src, $startXicon + 1050, $startYicon, 0, 0, 10, 10);
imagecopy($im, $src, $startXicon, $startYicon + 245, 0, 0, 10, 10);
imagecopy($im, $src, $startXicon, $startYicon + 495, 0, 0, 10, 10);

/**
 * Блок презентации
 */
// Наложение слайда презентации (помещается в папку с иконками)
$src = imagecreatefrompng(__DIR__ . '/resources/style/css/assets/images/slide.png');
// 958 * 656 - исходный размер слайда
imagecopyresampled($im, $src, 234, 34, 0, 0, (958*2/3 + 17), (656*2/3 + 17), 958, 656);

/**
 * @param $img - картинка - основа
 * @param $src - указатель на ресурс (вставляемую картинку)
 * @param $startXbtn - начало кнопки по X
 * @param $startYbtn - начало кнопки по Y
 * @param $imgSize - размер картинки-ресурса
 * @param bool|false $disabled - доступность кнопки (disabled - осветление иконки)
 */
function doButtonWithIcon($img, $startXbtn, $startYbtn, $imgSize = [25, 0], $src = '', $disabled = false) {
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

// кнопка под иконку (добавить)
$startXbtn = 235; // координата X начала кнопки
$startYbtn = 495; // координата Y начала кнопки
$src = __DIR__ . '/resources/style/css/assets/images/upload.png';
$imgSize = getimagesize($src); // получение размера изображения

doButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize,  $src);

// кнопка под иконку (стрелка влево - disabled)
$startXbtn = 335;
$startYbtn = 495;
$src = __DIR__ . '/resources/style/css/assets/images/left-arrow.png';
$imgSize = getimagesize($src);

doButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize, $src, true);

// кнопка с количеством слайдов
$startXbtn = 394;
$startYbtn = 495;
doButtonWithIcon($im, $startXbtn, $startYbtn);
imagettftext ($im, $fontSize, 0, $startXbtn + 2, $startYbtn + 20, $text_color, $font, '1 / 25' );

// кнопка под иконку (стрелка вправо)
$startXbtn = 450;
$startYbtn = 495;
$src = __DIR__ . '/resources/style/css/assets/images/right-arrow.png';
$imgSize = getimagesize($src);

doButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize, $src);

// кнопка под иконку (двунаправленная стрелка / увеличить по ширине)
$startXbtn = 750;
$startYbtn = 495;
$src = __DIR__ . '/resources/style/css/assets/images/fit-to-width.png';
$imgSize = getimagesize($src);

doButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize, $src);

// кнопка под иконку (fit-to-screen)
$startXbtn = 800;
$startYbtn = 495;
$src = __DIR__ . '/resources/style/css/assets/images/fit-to-screen.png';
$imgSize = getimagesize($src);

doButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize, $src);

/**
 * Блок для списка студентов (слушателей)
 */
$studentBlock = 140; // Высота блока для студентов
$studentImage = imagecreate(223, $studentBlock);

// Цвета полос и имени преподавателя
$white = imagecolorallocate($studentImage, 255, 255, 255); // белый фон
$grey = imagecolorallocate($studentImage, 247, 247, 247); // чередование серых полос под именами
$lightGrey = imagecolorallocate($im, 243, 243, 243); // фон для заголовоков в этом блоке (статус, имя, медиа)
$colorMasterName = imagecolorallocate($im, 70, 100, 158); // цвет имени мастера (преподавателя) в списке

$offsetHLine = 280; // Отступ сверху
// Заголовки (Статус, Имя, Медиа)
imagefilledrectangle($im, 3, $offsetHLine, 225, $offsetHLine + 30, $lightGrey);
$offsetHLine += 20;
imagettftext ($im, 10, 0, 5, $offsetHLine, $text_color, $font, 'Статус' );
imagettftext ($im, 10, 0, 5 + 50, $offsetHLine, $text_color, $font, 'Имя' );
imagettftext ($im, 10, 0, 5 + 160, $offsetHLine, $text_color, $font, 'Медиа' );

// Белый фон
imagefill($studentImage, 0, 0, $white);

// Серые полосы
$offsetHLine = 311; // Отступ сверху
$hLine = 19; // Высота полос
$countLines = $studentBlock / 19; // Количество полос в блоке
for ($i = 1; $i < $countLines; $i++) {
    imagefilledrectangle($studentImage, 0, $hLine * $i++, $offsetHLine, $hLine * $i, $grey);
}

// Соединение блока списка студентов с общим окном (картинкой)
imagecopy($im, $studentImage, 3, $offsetHLine, 0, 0, 223, $studentBlock);

$startXList = 40;
$hStudentName = 13;
$fontSize = 8;
// Имя преподавателя
imagettftext($im, $fontSize, 0, 53, $offsetHLine + $hStudentName, $colorMasterName, $font, $master );
// Медиа преподавателя (микрофон) - исходник белый и крупный
$src = imagecreatefrompng(__DIR__ . '/resources/style/css/assets/images/audio_20_white.png');
imagefilter($src, IMG_FILTER_NEGATE); // иконка стала черной
imagefilter($src, IMG_FILTER_BRIGHTNESS, 50); // осветление
// Обрезка имеющейся иконки (срезаем круглую рамку)
$src = imagecrop($src, ['x' => 9, 'y' => 9, 'width' => 14, 'height' => 14]);

// вставка с ресайзем до 12*12
imagecopyresampled($im, $src, 180, 315, 0, 0, 12, 12, 14, 14);

// Имена студентов
foreach ($students as $v) {
    imagettftext($im, $fontSize, 0, 53, $offsetHLine + $hStudentName + 20, $text_color, $font, $v['name'] );
    $hStudentName += $hLine;
}

// Вертикальные линии в блоке списка студентов
$greyVLine = imagecolorallocate($im, 197, 197, 197);
imageline ($im, $startXList + 10, $offsetHLine - 32, $startXList + 10, $offsetHLine + 209 - 32 - 35, $greyVLine);
imageline ($im, $startXList + 120, $offsetHLine - 32, $startXList + 120, $offsetHLine + 209 - 32 - 35, $greyVLine);
// Горизонтальная линия (под меню) в блоке списка студентов
imageline ($im, 3, $offsetHLine, $startXList + 186, $offsetHLine, $greyVLine);
// Горизонтальная линия под списком студентов
$offsetHLine += 209 - 32 - 38;
imageline ($im, 3, $offsetHLine, $startXList + 186, $offsetHLine, $greyVLine);

// кнопка под иконку (поднятая рука)
$startXbtn = 10;
$startYbtn = 455;
$src = __DIR__ . '/resources/style/css/assets/images/hand.png';
$imgSize = getimagesize($src);

doButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize, $src);

// кнопка под иконку (колесо - настройка?) - иконка не найдена, заменена на эллипс (рисование)
$startXbtn = 55;
$startYbtn = 455;
$src = __DIR__ . '/resources/style/css/assets/images/ellipse.png';
$imgSize = getimagesize($src);

doButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize, $src);


/**
 * Блок чата
 */
$wChat = 383; // Ширина блока чата
$hChat = 683; // Высота блока чата
$imageChat = imagecreate($wChat, $hChat);
$white = imagecolorallocate($imageChat, 255, 255, 255); // белый фон

$wTitleChat =  $wChat - 60;
$hTitleChat = 30;
$imageTitleChat = imagecreate($wTitleChat, $hTitleChat);
$grey = imagecolorallocate($imageTitleChat, 204, 204, 204); // серый фон

// Фон чата
imagefilledrectangle($imageChat, 0, 0, $wChat, $hChat, $white);
// Фон для верхнего меню (правее меню вкладок)
imagefilledrectangle($imageTitleChat, 0, 0, $wChat, $hTitleChat, $white);
imagecopy($imageChat, $imageTitleChat, $wChat - $wTitleChat, 0, 0, 0, $wTitleChat, $hTitleChat);

$fontSize = 11;
// Основной цвет для текста меню вкладок чата
$text_color = imagecolorallocate($imageChat, 47, 47, 47);
imagettftext ($imageChat , $fontSize , 0 , 15 , 21 , $text_color , $font , 'Все');

// Сообщения в чате (настройка)
$hOffsetChat = 70;
$hOffsetBetweenChat = 25;
$nameChatColor = imagecolorallocate($imageChat, 100, 100, 100);
$textChatColor = imagecolorallocate($imageChat, 47, 47, 47);

// Заполнение чата сообщениями
foreach ($students as $v) {
    imagettftext ($imageChat , $fontSize , 0 , 15 , $hOffsetChat , $nameChatColor , $font , $v['name']);
    imagettftext ($imageChat , $fontSize , 0 , 335 , $hOffsetChat, $nameChatColor , $font , $v['time']);
    $hOffsetChat +=$hOffsetBetweenChat;
    imagettftext ($imageChat , $fontSize , 0 , 15 , $hOffsetChat, $textChatColor , $font , $v['answer']);
    $hOffsetChat +=($hOffsetBetweenChat + 20);
}

// Горизонтальная линия разделитель в блоке чата
$greyHorizLine = imagecolorallocate($imageChat, 197, 197, 197);
// Горизонтальная линия (под меню) сверху в блоке чата
imageline ($imageChat, 0, 35, 383, 35, $greyHorizLine);
// Горизонтальная линия под сообщениями снизу в блоке чата
imageline ($imageChat, 0, 600, 383, 600, $greyHorizLine);
// Поле ввода сообщения
imagerectangle($imageChat, 5, 607, 270, 678, $greyHorizLine);

// Для кнопок с закругленными углами
function drawRoundRectangle($img, $x1, $y1, $x2, $y2, $radius, $color) {
    imagefilledrectangle($img, $x1+$radius, $y1, $x2-$radius, $y2, $color);
    imagefilledrectangle($img, $x1, $y1+$radius, $x1+$radius-1, $y2-$radius, $color);
    imagefilledrectangle($img, $x2-$radius+1, $y1+$radius, $x2, $y2-$radius, $color);

    imagefilledarc($img,$x1+$radius, $y1+$radius, $radius*2, $radius*2, 180 , 270, $color, IMG_ARC_PIE);
    imagefilledarc($img,$x2-$radius, $y1+$radius, $radius*2, $radius*2, 270 , 360, $color, IMG_ARC_PIE);
    imagefilledarc($img,$x1+$radius, $y2-$radius, $radius*2, $radius*2, 90 , 180, $color, IMG_ARC_PIE);
    imagefilledarc($img,$x2-$radius, $y2-$radius, $radius*2, $radius*2, 360 , 90, $color, IMG_ARC_PIE);
}
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
// кнопка Отправить
$buttonFilledColor = imagecolorallocate($imageChat, 220, 220, 220);
// фон кнопки
drawRoundRectangle($imageChat, 277, 625, 375, 655, 3, $buttonFilledColor);
// бордер кнопки
drawRoundRectangleNotFilled($imageChat, 277, 625, 375, 655, 3, $greyHorizLine);
imagettftext ($imageChat , $fontSize , 0, 287 , 645, $text_color , $font , 'Отправить');

// Меню чата вкладка Настройки
drawRoundRectangle($imageChat, 60, 0, 145, 29, 2, $buttonFilledColor);
drawRoundRectangleNotFilled($imageChat, 60, 0, 145, 29, 2, $greyHorizLine);
imagettftext ($imageChat , $fontSize , 0, 65 , 21, $text_color , $font , 'Настройки');

imagecopy($im, $imageChat, 895, 33, 0, 0, $wChat, $hChat);

imagepng($im, 'test.png');
imagedestroy($im);
