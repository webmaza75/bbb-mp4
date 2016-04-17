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
$hTopMenu = 40;
// Список участников вебинара (внешние данные)
$students = [
    ['name' => 'Воротилов Глеб', 'answer' => 'добрый вечер', 'time' => '20:00'],
    ['name' => 'Еропкин Виталий', 'answer' => 'Всем добрый вечер', 'time' => '20:00'],
    ['name' => 'Мамонов Виктор', 'answer' => 'Здравствуйте!', 'time' => '20:01'],
    ['name' => 'Фролов Максим', 'answer' => 'Добрый вечер', 'time' => '20:02'],
];
$master = 'Степанцев Альберт'; // внешние данные
$presentationName = 'P3P.pdf'; // Название презентации (возможно, внешние данные)
$lessonTitle = 'PHP-2: Профессиональное программирование: Обзор современных фреймворков';
$titlePadding = 35;

require __DIR__ . '/autoload.php';
require __DIR__ . '/functions.php';

$layout = new \ProfIT\Bbb\Layout($resFileName, 'defaultlayout');
$layout->setStyleSheet($cssFileName);
$image = new \ProfIT\Bbb\layout\Image([
    'w' => $w,
    'h' => $h
]);
$image->loadLayout($layout);
$image->generatePng('test.png');

$windows = $layout->getWindows();

// После генерации общего фона с блоками
$im = imagecreatefrompng('test.png');
// Наложение иконок "свернуть" на всех окнах
$src = __DIR__ . '/resources/style/css/assets/images/3_minimizeButton.png';
$imgSize = getimagesize($src); // получение размера изображения

foreach ($windows as $window) {
    $startX = ($window->relW + $window->relX) * $w - 50;
    if (!$window->relY) {
        $startY = 0;
    } else {
        $startY = ceil($window->relY * $h) + 3;
    }
    drawIcon($im, $startX, $startY + 3, $imgSize, $src, $filter = true);
}

// Наложение иконок "развернуть" на всех окнах
$src = __DIR__ . '/resources/style/css/assets/images/3_increaseButton.png';
$imgSize = getimagesize($src); // получение размера изображения

foreach ($windows as $window) {
    $startX = ceil(($window->relW + $window->relX) * $w) - 30;
    if (!$window->relY) {
        $startY = 0;
    } else {
        $startY = ceil($window->relY * $h) + 3;
    }
    drawIcon($im, $startX, $startY + 3, $imgSize, $src, $filter = true);
}

/**
 * Блок презентации
 */
// Наложение слайда презентации (помещается в папку с иконками)
$src = imagecreatefrompng(__DIR__ . '/resources/style/css/assets/images/slide.png');
// Данные для окна
$c[0] = ceil($windows['PresentationWindow']->relX * $w);
$c[1] = ceil($windows['PresentationWindow']->relY);
$c[2] = ceil($windows['PresentationWindow']->relW * $w);
$c[3] = ceil($windows['PresentationWindow']->relH * $h);

// 958 * 656 - исходный размер слайда
imagecopyresampled($im, $src, $c[0], $c[1] + $titlePadding + $hTopMenu, 0, 0, (958*2/3 + 17), (656*2/3 + 17), 958, 656);

/** Нижние иконки на кнопках под презентацией */

// кнопка под иконку (добавить)
$startYbtn = $c[1] + $titlePadding + ceil(656*2/3 + 17);
$startYbtn += 20; // + вертикальный отступ после слайда
$startXbtn = $c[0] + 10; // координата X начала кнопки
$src = __DIR__ . '/resources/style/css/assets/images/upload.png';
$imgSize = getimagesize($src); // получение размера изображения

drawButtonWithIcon($im, $startXbtn, $startYbtn + $hTopMenu, $imgSize,  $src);

// кнопка под иконку (стрелка влево - disabled)
$startXbtn += 100;
$src = __DIR__ . '/resources/style/css/assets/images/left-arrow.png';
$imgSize = getimagesize($src);

drawButtonWithIcon($im, $startXbtn, $startYbtn + $hTopMenu, $imgSize, $src, true);

// Название презентации
$fontSize = 11;
$font = __DIR__ . '/resources/fonts/arial.ttf';
$textBlack = imagecolorallocate($im, 00, 00, 00);
imagettftext ($im, $fontSize, 0, $startXbtn - 5, $titlePadding - 10 + $hTopMenu, $textColor, $font, $presentationName );

// кнопка с количеством слайдов
$startXbtn += 50;
// Подключаемый шрифт
$fontSize = 9;
$textColor = imagecolorallocate($im, 47, 47, 47); // цвет текста
drawButtonWithIcon($im, $startXbtn, $startYbtn + $hTopMenu);
imagettftext ($im, $fontSize, 0, $startXbtn + 6, $startYbtn + 20 + $hTopMenu, $textColor, $font, '1 / 25' );

// кнопка под иконку (стрелка вправо)
$startXbtn += 50;
$src = __DIR__ . '/resources/style/css/assets/images/right-arrow.png';
$imgSize = getimagesize($src);

drawButtonWithIcon($im, $startXbtn, $startYbtn + $hTopMenu, $imgSize, $src);

// кнопка под иконку (двунаправленная стрелка / увеличить по ширине)
$startXbtn = $c[2] + $c[0] - 100;
$src = __DIR__ . '/resources/style/css/assets/images/fit-to-width.png';
$imgSize = getimagesize($src);

drawButtonWithIcon($im, $startXbtn, $startYbtn + $hTopMenu, $imgSize, $src);

// кнопка под иконку (fit-to-screen)
$startXbtn += 40;
$src = __DIR__ . '/resources/style/css/assets/images/fit-to-screen.png';
$imgSize = getimagesize($src);

drawButtonWithIcon($im, $startXbtn, $startYbtn + $hTopMenu, $imgSize, $src);

/**
 * Блок для списка студентов (слушателей)
 */
$c[0] = ceil($windows['ListenersWindow']->relX * $w);
$c[1] = ceil($windows['ListenersWindow']->relY);
$c[2] = ceil($windows['ListenersWindow']->relW * $w);
$c[3] = ceil($windows['ListenersWindow']->relH * $h);
$pad = 4;
$bottomPadding = 46;
$hStudentMenu = 30;
$hStudentBlock = $c[3] - $bottomPadding - $titlePadding;
$wStudentBlock = $c[2] - $pad * 2;

// Название презентации
$fontSize = 11;
$textBlack = imagecolorallocate($im, 00, 00, 00);
imagettftext ($im, $fontSize, 0, 115, $titlePadding - 12 + $hTopMenu, $textColor, $font, count($students) );

$studentImage = imagecreate($wStudentBlock, $hStudentBlock);
// Цвета полос и имени преподавателя

$white = imagecolorallocate($studentImage, 255, 255, 255); // белый фон
$grey = imagecolorallocate($studentImage, 247, 247, 247); // чередование серых полос под именами
$lightGrey = imagecolorallocate($studentImage, 243, 243, 243); // фон для заголовоков в этом блоке (статус, имя, медиа)
$colorMasterName = imagecolorallocate($studentImage, 70, 100, 158); // цвет имени мастера (преподавателя) в списке

// Белый фон
imagefill($studentImage, 0, 0, $white);

// Заголовки (Статус, Имя, Медиа)
imagefilledrectangle($studentImage, 0, $c[1], $c[2], $hStudentMenu, $lightGrey);
$offsetHLine = 20;
$textColor = imagecolorallocate($studentImage, 47, 47, 47); // цвет текста
imagettftext ($studentImage, 10, 0, 5, $offsetHLine, $textColor, $font, 'Статус' );
imagettftext ($studentImage, 10, 0, 5 + 50, $offsetHLine, $textColor, $font, 'Имя' );
imagettftext ($studentImage, 10, 0, 5 + 160, $offsetHLine, $textColor, $font, 'Медиа' );

// Серые полосы
$offsetHLine += 11; // Отступ сверху
$hLine = 20; // Высота полос
$countLines = ($hStudentBlock - $offsetHLine) / $hLine; // Количество полос в блоке

for ($i = 1; $i < $countLines; $i++) {
    imagefilledrectangle($studentImage, 0, $offsetHLine + ($hLine * $i++), $c[2], $offsetHLine + ($hLine * $i), $grey);
}

$startXList = 40;
$hStudentName = 15;
$fontSize = 8;
// Имя преподавателя
imagettftext($studentImage, $fontSize, 0, 53, $offsetHLine + $hStudentName, $colorMasterName, $font, $master );


$hMicro = $offsetHLine + $hStudentName + 20;
// Имена студентов
foreach ($students as $v) {
    imagettftext($studentImage, $fontSize, 0, 53, $offsetHLine + $hStudentName + 20, $textColor, $font, $v['name'] );
    $hStudentName += $hLine;
}
$startXList = $wStudentBlock / 5;
// Вертикальные линии в блоке списка студентов
$greyVLine = imagecolorallocate($studentImage, 197, 197, 197);
imageline ($studentImage, $startXList + 5 , $offsetHLine - 32, $startXList + 5, $hStudentBlock, $greyVLine);
imageline ($studentImage, $startXList *4 - 15, $offsetHLine - 32, $startXList *4 - 15, $hStudentBlock, $greyVLine);
// Горизонтальная линия (под меню) в блоке списка студентов
imageline ($studentImage, 0, $offsetHLine, $wStudentBlock, $offsetHLine, $greyVLine);
// Горизонтальная линия под списком студентов
imageline ($studentImage, 0, $hStudentBlock, $wStudentBlock, $hStudentBlock, $greyVLine);

// кнопка под иконку (поднятая рука)
$startXbtn = $c[0] + 10;
$startYbtn = $c[3] - $bottomPadding / 2 - $imgSize[1];
$src = __DIR__ . '/resources/style/css/assets/images/hand.png';
$imgSize = getimagesize($src);

drawButtonWithIcon($im, $startXbtn, $startYbtn + $hTopMenu, $imgSize, $src);

// кнопка под иконку (колесо - настройка?) - иконка не найдена, заменена на эллипс (рисование)
$startXbtn +=40;
$src = __DIR__ . '/resources/style/css/assets/images/ellipse.png';
$imgSize = getimagesize($src);

drawButtonWithIcon($im, $startXbtn, $startYbtn + $hTopMenu, $imgSize, $src);

// Соединение блока списка студентов с общим окном (картинкой)
imagecopy($im, $studentImage, $pad, $c[1] + $titlePadding + $hTopMenu, 0, 0, $wStudentBlock, $hStudentBlock);

// Медиа преподавателя (микрофон) - исходник белый и крупный
$src = imagecreatefrompng(__DIR__ . '/resources/style/css/assets/images/audio_20_white.png');
imagefilter($src, IMG_FILTER_NEGATE); // иконка стала черной
imagefilter($src, IMG_FILTER_BRIGHTNESS, 50); // осветление
// Обрезка имеющейся иконки (срезаем круглую рамку)
$src = imagecrop($src, ['x' => 9, 'y' => 9, 'width' => 14, 'height' => 14]);

// вставка с ресайзем до 12*12
imagecopyresampled($im, $src, $startXList * 4, $hMicro + 5 + $hTopMenu, 0, 0, 12, 12, 14, 14);

/**
 * Блок чата
 */
$c[0] = ceil($windows['ChatWindow']->relX * $w);
$c[1] = ceil($windows['ChatWindow']->relY);
$c[2] = ceil($windows['ChatWindow']->relW * $w);
$c[3] = ceil($windows['ChatWindow']->relH * $h);

$wChat = $c[2] - 5; // Ширина блока чата
$hChat = $c[3] - $titlePadding - 4; // Высота блока чата
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
$textColor = imagecolorallocate($imageChat, 47, 47, 47);
imagettftext ($imageChat , $fontSize , 0 , 15 , 21 , $textColor , $font , 'Все');

// Сообщения в чате (настройка)
$hOffsetChat = 70;
$hOffsetBetweenChat = 25;
$nameChatColor = imagecolorallocate($imageChat, 100, 100, 100);
$textChatColor = imagecolorallocate($imageChat, 47, 47, 47);

// Заполнение чата сообщениями
foreach ($students as $v) {
    imagettftext ($imageChat , $fontSize , 0 , 15 , $hOffsetChat , $nameChatColor , $font , $v['name']);
    imagettftext ($imageChat , $fontSize , 0 , $wChat - 50 , $hOffsetChat, $nameChatColor , $font , $v['time']);
    $hOffsetChat +=$hOffsetBetweenChat;
    imagettftext ($imageChat , $fontSize , 0 , 15 , $hOffsetChat, $textChatColor , $font , $v['answer']);
    $hOffsetChat +=($hOffsetBetweenChat + 20);
}

// Горизонтальная линия разделитель в блоке чата
$greyHorizLine = imagecolorallocate($imageChat, 197, 197, 197);
// Горизонтальная линия (под меню) сверху в блоке чата
imageline ($imageChat, 0, 35, $wChat, 35, $greyHorizLine);
// Горизонтальная линия под сообщениями снизу в блоке чата
imageline ($imageChat, 0, $hChat - 110, $wChat, $hChat - 110, $greyHorizLine);
// Поле ввода сообщения
imagerectangle($imageChat, 5, $hChat - 100, $wChat - 115, $hChat - 10, $greyHorizLine);


// кнопка Отправить
$buttonFilledColor = imagecolorallocate($imageChat, 220, 220, 220);
// фон кнопки
drawRoundRectangle($imageChat, $wChat - 100, $hChat - 70, $wChat - 10, $hChat - 36, 3, $buttonFilledColor);
// бордер кнопки
drawRoundRectangleNotFilled($imageChat, $wChat - 100, $hChat - 70, 375, $hChat - 36, 3, $greyHorizLine);
imagettftext ($imageChat , $fontSize , 0, $wChat - 91 , $hChat - 48, $textColor , $font , 'Отправить');

// Меню чата вкладка Настройки
drawRoundRectangle($imageChat, 60, 0, 145, 29, 2, $buttonFilledColor);
drawRoundRectangleNotFilled($imageChat, 60, 0, 145, 29, 2, $greyHorizLine);
imagettftext ($imageChat , $fontSize , 0, 65 , 21, $textColor , $font , 'Настройки');

imagecopy($im, $imageChat, $c[0]+2, $c[1] + $hTopMenu + $titlePadding, 0, 0, $wChat, $hChat);

/**
 * Формирование верхнего меню на черном фоне
 */

// кнопка "экран"
$startXbtn = 10;
$startYbtn = 6;
$src = __DIR__ . '/resources/style/css/assets/images/deskshare_icon.png';
$imgSize = getimagesize($src);

drawButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize, $src, false, 2);

// кнопка "подключенные наушники"
$startXbtn += 50;
$src = __DIR__ . '/resources/style/css/assets/images/headset_open.png';
$imgSize = getimagesize($src);

drawButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize, $src, false, 2);

// кнопка "видеокамера"
$startXbtn += 50;
$src = __DIR__ . '/resources/style/css/assets/images/webcam.png';
$imgSize = getimagesize($src);

drawButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize, $src, false, 2);

// кнопка "контроль записи"
$startXbtn += 50;
$src = __DIR__ . '/resources/style/css/assets/images/control-record.png';
$imgSize = getimagesize($src);

drawButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize, $src, false, 2);

// кнопка "микрофон"
$startXbtn += 65;
$src = __DIR__ . '/resources/style/css/assets/images/control-record.png';
$imgSize = getimagesize($src);

drawButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize, $src, false, 3);

// Название урока
$startXbtn +=110;
$fontSize = 11;
$textColor = imagecolorallocate($im, 255, 255, 255); // белый цвет для текста меню
imagettftext ($im, $fontSize, 0, $startXbtn, $startYbtn + 17, $textColor, $font, $lessonTitle );


// кнопка "клавиши быстрого доступа"
$startXbtn = $w - 270;
$fontSize = 10;
$textColor = imagecolorallocate($im, 0, 0, 0);
$buttonFilledColor = imagecolorallocate($im, 240, 240, 240);
$buttonBorderColor = imagecolorallocate($im, 190, 190, 190);
drawRoundRectangle($im, $startXbtn, $startYbtn, $startXbtn + 170, $startYbtn + 24, 3, $buttonFilledColor);
drawRoundRectangleNotFilled($im, $startXbtn, $startYbtn, $startXbtn + 170, $startYbtn + 24, 3, $buttonBorderColor);
imagettftext ($im, $fontSize, 0, $startXbtn + 5, $startYbtn+17, $textColor, $font, 'клавиши быстрого доступа' );

// Знак вопроса
$fontSize = 11;
$startXbtn += 190;
$textColor = imagecolorallocate($im, 255, 255, 255); // белый цвет для текста меню
imagettftext ($im, $fontSize, 0, $startXbtn, $startYbtn + 17, $textColor, $font, '?' );

// кнопка "logout"
$startXbtn += 30;
$src = __DIR__ . '/resources/style/css/assets/images/logout.png';
$imgSize = getimagesize($src);

drawButtonWithIcon($im, $startXbtn, $startYbtn, $imgSize, $src, false, 2);

imagepng($im, 'test.png');
imagedestroy($im);