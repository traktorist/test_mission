<?php

// коннект с БД
function connect_db() {
    $link = mysql_connect(HOST, USER, PASS);												// коннект с MySQL
    if (!$link) die('<br />Не могу соединиться с MySQL:<br />' . mysql_error());
    else {
        mysql_select_db(DB, $link) or die("<br />Не могу подключиться к базе.<br />" . mysql_errno() . " - " . mysql_error() . "<br />");
        mysql_set_charset('utf8', $link); 													// установка кодировки соединения с БД
    }
}

// поиск победителя
function drumroll($user_id) {
    $html = '';

    $result = mysql_query("SELECT `id_room`, COUNT(`id_room`) AS count_hands FROM `rooms` GROUP BY `id_room` HAVING count_hands = '2'");
    while ($row = mysql_fetch_array($result)) {
        $match = false;                                                         // флаг совпадения орудий(выброшенных рук) - нужно удалить результат игры
        $html .= 'room:' . $row['id_room'] . ' - ';

        $weapons = array();
        $res = mysql_query("SELECT * FROM `rooms` WHERE `id_room` = '" . $row['id_room'] . "';");
        while ($r = mysql_fetch_array($res)) {
            $weapons[] = $r;
        }

        /* определяем выйгравшего игрока */
        // если орудия совпадают, играем заново - результат нужно удалить
        if ($weapons[0]['weapon'] == $weapons[1]['weapon']) {
            $match = true;
            $html .= 'Победила ДРУЖБА';
        }
        // если орудия разные, то ищем опбедителя исходя из орудия первого игрока
        else {
            $html .= 'Победитель: ';

            if ($weapons[0]['weapon'] == 'k')
                $winner_id = ($weapons[1]['weapon'] == 'n') ? $weapons[0]['id_user'] : $weapons[1]['id_user'];
            else if ($weapons[0]['weapon'] == 'n')
                $winner_id = ($weapons[1]['weapon'] == 'b') ? $weapons[0]['id_user'] : $weapons[1]['id_user'];
            else if ($weapons[0]['weapon'] == 'b')
                $winner_id = ($weapons[1]['weapon'] == 'k') ? $weapons[0]['id_user'] : $weapons[1]['id_user'];

            $winner = mysql_fetch_array(mysql_query("SELECT `login` FROM `users` WHERE `id_user` = '" . $winner_id . "'"));
            $html .= $winner['login'];

            // отмечаем одну из выброшенных рук (руку текущего игрока), как просмотренную им
            mysql_query("UPDATE `rooms` SET `checked` = '1' WHERE `id_room` = '" . $row['id_room'] . "' AND `id_user` = '" . $user_id . "';");
        }

        // если результат игры просмотрен обоими игроками, то удалем результат
        $checked = mysql_fetch_array(mysql_query("SELECT COUNT(*) AS checked FROM `rooms` WHERE `id_room` = '" . $row['id_room'] . "' AND `checked` = '1';"));
        if (($checked['checked'] == 2) || ($match == true))
            mysql_query("DELETE FROM `rooms` WHERE `id_room` = '" . $row['id_room'] . "';");
    }

    return $html;
}


?>
