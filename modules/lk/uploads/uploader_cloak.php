<?php
require_once ($_SERVER['DOCUMENT_ROOT']. '/modules/config/cfg_lib.php');
require_once ($_SERVER['DOCUMENT_ROOT']. '/modules/libs/ints.php');
require_once ($_SERVER['DOCUMENT_ROOT']. '/modules/libs/mysql.php');

foreach ($servers as $server) {
$don_1 = "donate_".$server['title'];
$donate_1=$mysqli->query("SELECT `donate_".$server['title']."` FROM `dle_users` WHERE `name` LIKE '$login'")->fetch_object()->$don_1;
if ($donates[$donate_1]['cloak']=="true") {
	$hd_cloak = "cloak_good";
	if ($donates[$donate_1]['hd_cloak']=="true" ) {
	    $hd_cloak = "hd_cloak_good";
    }
  }
}


/**
 * Загрузка картинки из формы
 * @see http://denisyuk.by/all/polnoe-rukovodstvo-po-zagruzke-izobrazheniy-na-php/
 */
// Перезапишем переменные для удобства
$filePath  = $_FILES['setskin']['tmp_name'];
$errorCode = $_FILES['setskin']['error'];
// Проверим на ошибки
if ($errorCode !== UPLOAD_ERR_OK || !is_uploaded_file($filePath)) {
    // Массив с названиями ошибок
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE   => 'max_file',
        UPLOAD_ERR_FORM_SIZE  => 'max_file',
        UPLOAD_ERR_PARTIAL    => 'huyna',
        UPLOAD_ERR_NO_FILE    => 'no_upload',
        UPLOAD_ERR_NO_TMP_DIR => 'hz',
        UPLOAD_ERR_CANT_WRITE => 'hz',
        UPLOAD_ERR_EXTENSION  => 'hz',
    ];
    // Зададим неизвестную ошибку
    $unknownMessage = 'hz';
    // Если в массиве нет кода ошибки, скажем, что ошибка неизвестна
    $outputMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : $unknownMessage;
    // Выведем название ошибки
	header('Location: http://six.u-studio.su/cabinet?'.$outputMessage);
    die($outputMessage);
}
// Создадим ресурс FileInfo
$fi = finfo_open(FILEINFO_MIME_TYPE);
// Получим MIME-тип
$mime = (string) finfo_file($fi, $filePath);
// Закроем ресурс
finfo_close($fi);
// Проверим ключевое слово image (image/jpeg, image/png и т. д.)
if (strpos($mime, 'image/png') === false) {
	header('Location: http://six.u-studio.su/cabinet?no_image');
	die();
}
// Результат функции запишем в переменную
$image = getimagesize($filePath);
// Зададим ограничения для картинок
if ($hd_cloak=="cloak_good" || $hd_cloak=="hd_cloak_good") {
	$limit_ok = $perms['cloak'];
    if ($hd_cloak=="hd_cloak_good") {
        $limit_ok = $perms['hd_cloak'];
    }
} else {
	header('Location: http://six.u-studio.su/cabinet?cloak_error');
	die();
}
$limitBytes  = $limit_ok * $limit_ok * 5;
$limitWidth  = $limit_ok;
$limitHeight = $limit_ok;
// Проверим нужные параметры
if (filesize($filePath) > $limitBytes) {
	header('Location: http://six.u-studio.su/cabinet?max');
	die('max');
} if ($image[1] > $limitHeight) {
	header('Location: http://six.u-studio.su/cabinet?max');
	die('max');
} if ($image[0] > $limitWidth)  {
    header('Location: http://six.u-studio.su/cabinet?max');
    die('max');
}
// Сгенерируем новое имя файла на основе MD5-хеша
$name = $_FILES['setskin']['name'];
// Сгенерируем расширение файла на основе типа картинки
$extension = image_type_to_extension($image[2]);
// Сократим .jpeg до .jpg
$format = str_replace('jpeg', 'jpg', $extension);
// Переместим картинку с новым именем и расширением в папку /pics
if (!move_uploaded_file($filePath, __DIR__ . '/cloaks/'  .$login . '.png')) {
    die('При записи изображения на диск произошла ошибка.');
} else {
	header('Location: http://six.u-studio.su/cabinet?cloak_success');
}