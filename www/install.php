<?php
/**
 * Created by PhpStorm.
 * User: traktor
 * Date: 14.03.14
 * Time: 22:19
 */


require_once('config.php');

$action = $_POST['action'];                                     // действие: проверка соединения с БД, проверка наличия дампа для импорта или собственно импорт

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
    connect_db();
    echo true;
}

function install() {
    $dump = file(DUMP_PATH);
    foreach ($dump as $num => $sql_str) {
        if (substr($sql_str, 0, 2) == '--') unset($dump[$num]);
        if (substr($sql_str, 0, 2) == '/*') unset($dump[$num]);
    }
    $dump = explode(";\n", implode("\n", $dump));
    unset($dump[count($dump) - 1]);

    connect_db();
    foreach ($dump as $query)
        if ($query)
            if (!mysql_query($query))
                die("Fail on '$query'");

    echo true;
}

function connect_db() {
    $link = mysql_connect(HOST, USER, PASS);												// коннект с MySQL
    if (!$link) die('<br />Не могу соединиться с MySQL:<br />'.mysql_error());
    else {
        mysql_select_db(DB, $link) or die("<br />Не могу подключиться к базе " . DB . ".<br />".mysql_errno()." - ".mysql_error()."<br />");
        mysql_set_charset('utf8', $link); 													// установка кодировки соединения с БД
    }
}

?>
