<?php
/**
 * Created by PhpStorm.
 * User: traktor
 * Date: 14.03.14
 * Time: 22:19
 */


$action = $_POST['action'];                                     // действие: проверка соединения с БД или наличия дампа для импорта
$dump_path = $_SERVER['DOCUMENT_ROOT'].'/dump.sql';             // путь до файла дампа БД, который нужно импортировать

switch ($action) {
    case 'check_connection': {
        check_connection();
        return;
    }
    case 'check_dump': {
        check_dump($dump_path);
        return;
    }
    default: {
        echo 'vrotmnenogi!';
        return;
    }
}


function check_connection() {
    sleep(2);
    echo true;
}

function check_dump($dump_path) {
    sleep(1);
    echo file_exists($dump_path);
}



?>
