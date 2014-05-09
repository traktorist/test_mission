<?php

include_once ("config.php");
include_once ("functions.php");


connect_db();

$action = $_POST['action'];                                                         // действие: выбор оружия

switch ($action) {
    case 'get_users': {
        $users = array();

        $result = mysql_query("SELECT * FROM `users_knb`");
        while ($row = mysql_fetch_array($result))
            $users[$row['id_user']] = $row['login'];

        echo json_encode($users);
        break;
    }
    case 'get_score': {
        $score_html = '<table border="1"><tr><td>user</td><td>score</td></tr>';

        $result = mysql_query("SELECT * FROM `users_knb` ORDER BY `score` DESC LIMIT 0, 10");
        while ($row = mysql_fetch_array($result)) {
            $score_html .= '<tr><td>' . $row['login'] . '</td><td>' . $row['score'] . '</td></tr>';
        }

        $score_html .= '</table>';

        echo json_encode($score_html);
        break;
    }
    case 'throw_hand': {
        $id_user = $_POST['id_user'];
        $weapon = $_POST['weapon'];
        $html = '';

        $room = mysql_fetch_array(mysql_query("SELECT *, COUNT(`id_user`) AS count FROM `rooms` GROUP BY `id_room`"));
        // если никто не играет, бросим первую руку
        if (!$room) {
            $timestamp = time();
            mysql_query("INSERT INTO `rooms` (`id_room`, `id_user`, `weapon`) VALUES ('" . $timestamp . "', '" . $id_user . "', '" . $weapon . "');");
            $html .= 'Ждём второго игрока...';
        }
        // если есть комната - кто-то играет
        else {
            // если брошена одна рука, бросим вторую
            if ($room['count'] == 1)
                mysql_query("INSERT INTO `rooms` (`id_room`, `id_user`, `weapon`) VALUES ('" . $room['id_room'] . "', '$id_user', '$weapon');");
        }

        echo json_encode($html);
        break;
    }
    case 'wait_opponent': {
        $user_id = $_POST['user_id'];                                               // id игрока - используется для отметки просмотра результата - результат игры удаляется, только если оба игрока просмотрели его

        // определяем победителя
        $html = drumroll($user_id);
        echo json_encode($html);
        break;
    }
    default: {
        return false;
    }
}

?>
