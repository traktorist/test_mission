<?php
/**
 * Created by PhpStorm.
 * User: traktor
 * Date: 14.03.14
 * Time: 22:19
 */


require_once('config.php');

$action = $_POST['action'];                                     // действие: проверка соединения с БД или наличия дампа для импорта

switch ($action) {
    case 'check_dump': {
        check_dump(DUMP_PATH);
        return;
    }
    case 'check_connection': {
        check_connection();
        return;
    }
    case 'install': {
        install();
        return;
    }
    default: {
        echo 'vrotmnenogi!';
        return;
    }
}


function check_dump($dump_path) {
    echo file_exists($dump_path);
}

function check_connection() {
    $link = mysql_connect(HOST, USER, PASS);												// коннект с MySQL
    if (!$link) die('<br />Не могу соединиться с MySQL:<br />'.mysql_error());
    else {
        mysql_select_db(DB, $link) or die("<br />Не могу подключиться к базе " . DB . ".<br />".mysql_errno()." - ".mysql_error()."<br />");
        mysql_set_charset('utf8', $link); 													// установка кодировки соединения с БД
        echo true;
    }
}

function install() {
    echo true;
}

?>
