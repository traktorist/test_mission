<?php
ini_set("max_execution_time", 0);
ini_set('memory_limit', '800M');
mb_internal_encoding("UTF-8");


define('HOST', 'localhost'); 												    // имя хоста
define('DB', 'test_mission'); 												    // имя базы
define('USER', 'test_mission'); 											    // имя пользователя
define('PASS', 'test_mission'); 										        // пароль

$link = mysql_connect(HOST, USER, PASS);										// коннект с MySQL
if (!$link) die('<br />Не могу соединиться с MySQL:<br />'.mysql_error());
else {
    mysql_select_db(DB, $link) or die("<br />Не могу подключиться к базе " . DB . ".<br />".mysql_errno()." - ".mysql_error()."<br />");
    mysql_set_charset('utf8', $link); 											// установка кодировки соединения с БД
}


$channels_array = array();

$res_channels = mysql_query("SELECT * FROM `mod_rss_channels`");
while ($channel = mysql_fetch_array($res_channels)) {
    $all_attributes_array = array();
    $res_attributes_for_channel = mysql_query("SELECT * FROM `mod_rss_attributes` WHERE `id_channel` = '" . $channel['id_channel'] . "'");
    while ($attribute = mysql_fetch_array($res_attributes_for_channel)) {
        $words = explode(",", $attribute['words']);
        $attr_array = array();
        foreach ($words as $word)
            $attr_array[$word] = array('field' => $attribute['field'], 'category' => $attribute['category']);

        $all_attributes_array[] = $attr_array;
    }
    $channels_array[] = array('rss_obj' => simplexml_load_string(file_get_contents($channel['url'])), 'attributes' => $all_attributes_array);
}


$count = 0;
foreach ($channels_array as $channel) {
    foreach ($channel['rss_obj']->channel->item as $item) {
        $match = 0;
        $continue = false;
        $field_or_category_array = array();
        foreach ($channel['attributes'] as $attribute) {
            foreach ($attribute as $word => $field_or_category) {
                $pos = mb_stripos($item->title, $word) || mb_stripos($item->description, $word);
                if ($pos !== false) {
                    echo 'Подстрока <strong>' . $word . '</strong> найдена в позиции: ' . $pos . '. Категория <strong>' . $field_or_category['category'] . '</strong>. Поле <strong>' . $field_or_category['field'] . '</strong><br />';
                    $match++;
                    $field_or_category_array[] = $field_or_category;
                }
            }
        }
        if ($match) {
            echo '<div><b><a href="' . $item->link . '">' . $item->title . "</a></b><br/>";
//            echo '$match = ' . $match . '<br />';

            $i = 0;
            if ($match > 1) {
                $where = 'WHERE ';
                while ($match != $i) {
//                    echo $field_or_category_array[$i]['field'] . ' - ' . $field_or_category_array[$i]['category'] . '<br />';
                    $where .= "(`field_name` = '" . protect($field_or_category_array[$i]['field']) . "' AND `data` = '" . protect($field_or_category_array[$i]['category']) . "') OR ";
                    $i++;
                }
                $where = substr($where, 0, -4);
//                echo $where . '<br />';

                $item_id_for_match = array();
                $res_fields_data_for_match = mysql_query("SELECT `item_id` FROM `content_fields_data` $where");
                while ($item_id = mysql_fetch_array($res_fields_data_for_match)) {
                    $item_id_for_match[] = $item_id['item_id'];
                }
                $count_item_array = array_count_values($item_id_for_match);
//                var_dump($count_item_array);
                foreach ($count_item_array as $count_item) {
                    if ($count_item > 1) {
                        echo 'Уже есть такая статья в БД!';
                        $continue = true;
                    }
                }
            }

            $res_content_titles = mysql_query("SELECT `title` FROM `content`");
            while ($content = mysql_fetch_array($res_content_titles)) {
                $content_titles[] = $content['title'];
            }
            if (in_array($item->title, $content_titles)) {
                echo 'Уже есть такая статья в БД!';
                $continue = true;
            }


            if (!$continue) {
                echo '<br />';
                $translit_url = translitIt($item->title);
                $strtotime = strtotime($item->pubDate);
//                echo '<br /><br />content:';
//                echo '<br />title -> ' . $item->title . '<br />url -> ' . $translit_url . '<br />cat_url -> ' . "aviatsionnye-sobytiia/" . '<br />prev_text -> ' . $item->description . '<br />full_text -> ' . " " . '<br />category -> ' . "62" . '<br />post_status -> ' . "draft" . '<br />author -> ' . "Administrator" . '<br />publish_date_UNIX -> ' . $strtotime;
                $sql = "INSERT INTO `content`
                    (`id`, `title`, `meta_title`, `url`, `cat_url`, `keywords`, `description`, `prev_text`, `full_text`, `category`, `full_tpl`, `main_tpl`, `position`, `comments_status`, `comments_count`, `post_status`, `author`, `publish_date`, `created`, `updated`, `showed`, `lang`, `lang_alias`)
                    VALUES (NULL, '$item->title', NULL, '$translit_url', 'aviatsionnye-sobytiia/', NULL, NULL, '$item->description', ' ', '62', NULL, '', '', '', '0', 'draft', 'Administrator', '$strtotime', '$strtotime', '$strtotime', '', '0', '0');";
                mysql_query($sql);
                $item_id = mysql_insert_id();

                $i = 0;
                while ($match != $i) {
                    $field_name = $field_or_category_array[$i]['field'];
                    $data = $field_or_category_array[$i]['category'];
//                    echo '<br /><br />conten_fields_data:';
//                    echo '<br />item_id -> ' . $item_id . '<br />item_type -> ' . "page" . '<br />field_name -> ' . $field_or_category_array[$i]['field'] . '<br />data -> ' . $field_or_category_array[$i]['category'];
                    $sql = "INSERT INTO `content_fields_data`
                        (`id`, `item_id`, `item_type`, `field_name`, `data`)
                        VALUES (NULL, '$item_id', 'page', '$field_name', '$data');";
                    mysql_query($sql);
                    $i++;
                }

                $count++;
            }

            echo "<br/></div><hr>";
        }
    }
}

echo "<br/><br/>Добавлено новых статей: $count<hr>";

// экранирует одинарные кавычки, html-теги и лишние пробелы по краям
function protect($str) {
    return trim(htmlspecialchars(mysql_real_escape_string($str)));
}

// формирует транслит из темы новости
function translitIt($str) {
    $tr = array(
        "А"=>"a","Б"=>"b","В"=>"v","Г"=>"g",
        "Д"=>"d","Е"=>"e","Ж"=>"j","З"=>"z","И"=>"i",
        "Й"=>"y","К"=>"k","Л"=>"l","М"=>"m","Н"=>"n",
        "О"=>"o","П"=>"p","Р"=>"r","С"=>"s","Т"=>"t",
        "У"=>"u","Ф"=>"f","Х"=>"h","Ц"=>"ts","Ч"=>"ch",
        "Ш"=>"sh","Щ"=>"sch","Ъ"=>"","Ы"=>"yi","Ь"=>"",
        "Э"=>"e","Ю"=>"yu","Я"=>"ya","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
        " "=> "_", "."=> "", "/"=> "_"
    );

    $urlstr = strtr($str, $tr);
    $urlstr = preg_replace('/[^A-Za-z0-9_\-]/', '', $urlstr);

    return $urlstr;
}

?>
