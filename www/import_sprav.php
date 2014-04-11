<?php

ini_set("max_execution_time", 0);
ini_set('memory_limit', '800M');
error_reporting(E_ALL);


$hostname = "localhost";
$username = "test_mission";
$password = "test_mission";
$dbName = "test_mission";


/* создать соединение */
MYSQL_CONNECT($hostname, $username, $password) or die("Не могу создать соединение");
@mysql_select_db("$dbName") or die("Не могу выбрать базу данных");
//mysql_query("SET NAMES cp1251");



$result_select = MYSQL_QUERY("SELECT * FROM `category` WHERE `id` = '85'");
$i = 0;
$cat_id2delete = array();
while ($row = mysql_fetch_assoc($result_select)) {
	echo 'parent category ' . ++$i . ' - ' . $row['id'] . '<br />';
	$cat_id2delete[] = $row['id'];
	
	$result_select2 = MYSQL_QUERY("SELECT * FROM `category` WHERE `parent_id` = '" . $row['id'] . "'");
	while ($row2 = mysql_fetch_assoc($result_select2)) {
		echo 'delete category #' . ++$i . ' - ' . $row['id'] . ' -> ' . $row2['id'] . '<br />';
		$cat_id2delete[] = $row2['id'];
		
		$result_select3 = MYSQL_QUERY("SELECT * FROM `category` WHERE `parent_id` = '" . $row2['id'] . "'");
		while ($row3 = mysql_fetch_assoc($result_select3)) {
			echo 'delete category #' . ++$i . ' - ' . $row2['id'] . ' -> ' . $row3['id'] . '<br />';
			$cat_id2delete[] = $row3['id'];
		}
	}
}

/* Очистка значений дополнительных полей */
echo '<br /><br />';
foreach ($cat_id2delete as $id) {
	echo $id . '<br />';
	if ($id != '85') {
		MYSQL_QUERY("DELETE FROM category WHERE `id` = " . $id);
		echo '<br />delete category by id=' . $id;
	}
	
	$id_fields_data = MYSQL_QUERY("SELECT * FROM content WHERE `category` = " . $id);
	MYSQL_QUERY("DELETE FROM content WHERE `category` = " . $id);
	echo '<br />delete content by category=' . $id;
	
	while ($row = mysql_fetch_assoc($id_fields_data)) {
		MYSQL_QUERY("DELETE FROM content_fields_data WHERE `item_id` = " . $row['id']);
		echo '<br />delete content_fields_data by item_id=' . $row['id'];
	}
	
	MYSQL_QUERY("DELETE FROM content_fields_data WHERE `item_id` = " . $id);
	echo '<br />delete content_fields_data by item_id=' . $id;
}




$count = 0;
$rss = simplexml_load_file('Aircraft category.xml');

foreach ($rss->XMLDictionaryElement as $item) {

	if ($item->Level == 1) {
		//$tru = iconv('UTF-8', 'WINDOWS-1251', $item->TitleRu);
		$tru = $item->TitleRu;
		if ($tru == ' ')
			$tru=$item->TitleEn;
		//echo '$tru=' . $tru . '|<br />';
		$ten = strtolower($item->TitleEn);   

		$it3 = MYSQL_QUERY("INSERT INTO `content` (
				`title`, `url`, `cat_url`, `prev_text`, `category`, `post_status`, `author`, `lang`, `publish_date`, `created`, `position`) 
		VALUES ('" . $tru . "', '" . $ten . "', 'spravochnik-tip-letatelnogo-apparata/', '" .$tru . "', 85, 'publish', 'Administrator', 3, ".time().", ".time().", ".$count.")");

		$idd = mysql_insert_id();
		echo $idd.'<br>';
		


		$it4=MYSQL_QUERY("INSERT INTO `content_fields_data` (
				`item_id`, `item_type`, `field_name`, `data`) 
		VALUES ('" . $idd . "', 'page', 'field_rus', '" . $tru ."')");


		$it4=MYSQL_QUERY("INSERT INTO `content_fields_data` (
				`item_id`, `item_type`, `field_name`, `data`) 
		VALUES ('" . $idd . "', 'page', 'field_eng', '" . $item->TitleEn ."')");


		$it4=MYSQL_QUERY("INSERT INTO `content_fields_data` (
				`item_id`, `item_type`, `field_name`, `data`) 
		VALUES ('" . $idd . "', 'page', 'field_adrep', '" . $item->ADREP ."')");


		$it4=MYSQL_QUERY("INSERT INTO `content_fields_data` (
				`item_id`, `item_type`, `field_name`, `data`) 
		VALUES ('" . $idd . "', 'page', 'field_guid', '" . $item->GUID ."')");


		$count++;
    }
}

foreach ($rss->XMLDictionaryElement as $item) {

    if ($item->Level == 2) {
        $child = $item->XML_Link;
        $temp = "SELECT * FROM `content_fields_data` WHERE `field_name` = 'field_adrep' AND `data`=" . $child;
        $sql = mysql_query($temp);					// получаем данные
		echo $temp.'<hr>';


        while ($r2 = mysql_fetch_assoc($sql)) {
            $it1 = MYSQL_QUERY("SELECT * FROM `content` WHERE `id` =" . $r2['item_id']);
			
            while ($r2 = mysql_fetch_assoc($it1)) {
				echo $r2['title'];
                $it11 = MYSQL_QUERY("INSERT INTO `category` (
						`parent_id`, `name`, `url`, `category_field_group`, `field_group`, `per_page` ) 
				VALUES (" . $r2['category'] . ", '" . $r2['title'] . "', '" . $r2['url'] . "', 11, 11, 25)");
                $iddd = mysql_insert_id();
				
				
                $q = "DELETE FROM content WHERE `id` = " . $r2['id'];
                echo $q . '<br>';
                $it1 = MYSQL_QUERY($q);


                $itx = MYSQL_QUERY("SELECT * FROM `content_fields_data` WHERE `item_id` =" . $r2['id']);
				
                while ($rx = mysql_fetch_assoc($itx)) {
                    $sql = "UPDATE `content_fields_data` SET `item_type`='category',`item_id`=" . $iddd . " WHERE `id`=" . $rx['id'];
                    echo $sql;
                    $ity = MYSQL_QUERY($sql);
                }
            }
        }


        //$tru = iconv('UTF-8', 'WINDOWS-1251', $item->TitleRu);
        $tru = $item->TitleRu;
		if ($tru == ' ')
			$tru=$item->TitleEn;
        $ten = strtolower($item->TitleEn);


        $it3 = MYSQL_QUERY("INSERT INTO `content` (
				`title`, `url`, `cat_url`, `prev_text`,`category`,`post_status`, `author`, `lang`, `publish_date`, `created`, `position`) 
		VALUES ('" . $tru . "', '" . $ten . "', 'spravochnik-tip-letatelnogo-apparata/', '" . $tru . "', " . $iddd . ", 'publish', 'Administrator', 3, " . time() . ", " . time() . ", " . $count . ")");
        
		$idd = mysql_insert_id();

		

        $it4 = MYSQL_QUERY("INSERT INTO `content_fields_data` (
				`item_id`, `item_type`, `field_name`, `data`) 
		VALUES ('" . $idd . "', 'page', 'field_rus', '" . $tru . "')");


        $it4 = MYSQL_QUERY("INSERT INTO `content_fields_data` (
				`item_id`, `item_type`, `field_name`, `data`) 
		VALUES ('" . $idd . "', 'page', 'field_eng', '" . $item->TitleEn . "')");


        $it4 = MYSQL_QUERY("INSERT INTO `content_fields_data` (
				`item_id`, `item_type`, `field_name`, `data`) 
		VALUES ('" . $idd . "', 'page', 'field_adrep', '" . $item->ADREP . "')");


        $it4 = MYSQL_QUERY("INSERT INTO `content_fields_data` (
				`item_id`, `item_type`, `field_name`, `data`) 
		VALUES ('" . $idd . "', 'page', 'field_guid', '" . $item->GUID . "')");


        $count++;
    }
}

foreach ($rss->XMLDictionaryElement as $item) {

    if ($item->Level == 3) {
        $child = $item->XML_Link;
        $temp = "SELECT * FROM `content_fields_data` WHERE `field_name` = 'field_adrep' AND `data`=" . $child;
        $sql = mysql_query($temp);					// получаем данные
		echo $temp.'<hr>';


        while ($r2 = mysql_fetch_assoc($sql)) {
            $it1 = MYSQL_QUERY("SELECT * FROM `content` WHERE `id` =" . $r2['item_id']);
			
            while ($r2 = mysql_fetch_assoc($it1)) {
				echo $r2['title'];
                $it11 = MYSQL_QUERY("INSERT INTO `category` (
						`parent_id`, `name`, `url`, `category_field_group`, `field_group`, `per_page` ) 
				VALUES (" . $r2['category'] . ", '" . $r2['title'] . "', '" . $r2['url'] . "', 11, 11, 25)");
                $iddd = mysql_insert_id();
				
				
                $q = "DELETE FROM content WHERE `id` = " . $r2['id'];
                echo $q . '<br>';
                $it1 = MYSQL_QUERY($q);


                $itx = MYSQL_QUERY("SELECT * FROM `content_fields_data` WHERE `item_id` =" . $r2['id']);
				
                while ($rx = mysql_fetch_assoc($itx)) {
                    $sql = "UPDATE `content_fields_data` SET `item_type`='category',`item_id`=" . $iddd . " WHERE `id`=" . $rx['id'];
                    echo $sql;
                    $ity = MYSQL_QUERY($sql);
                }
            }
        }


        //$tru = iconv('UTF-8', 'WINDOWS-1251', $item->TitleRu);
        $tru = $item->TitleRu;
		if ($tru == ' ')
			$tru=$item->TitleEn;
        $ten = strtolower($item->TitleEn);


        $it3 = MYSQL_QUERY("INSERT INTO `content` (
				`title`, `url`, `cat_url`, `prev_text`,`category`,`post_status`, `author`, `lang`, `publish_date`, `created`, `position`) 
		VALUES ('" . $tru . "', '" . $ten . "', 'spravochnik-tip-letatelnogo-apparata/', '" . $tru . "', " . $iddd . ", 'publish', 'Administrator', 3, " . time() . ", " . time() . ", " . $count . ")");
        
		$idd = mysql_insert_id();

		

        $it4 = MYSQL_QUERY("INSERT INTO `content_fields_data` (
				`item_id`, `item_type`, `field_name`, `data`) 
		VALUES ('" . $idd . "', 'page', 'field_rus', '" . $tru . "')");


        $it4 = MYSQL_QUERY("INSERT INTO `content_fields_data` (
				`item_id`, `item_type`, `field_name`, `data`) 
		VALUES ('" . $idd . "', 'page', 'field_eng', '" . $item->TitleEn . "')");


        $it4 = MYSQL_QUERY("INSERT INTO `content_fields_data` (
				`item_id`, `item_type`, `field_name`, `data`) 
		VALUES ('" . $idd . "', 'page', 'field_adrep', '" . $item->ADREP . "')");


        $it4 = MYSQL_QUERY("INSERT INTO `content_fields_data` (
				`item_id`, `item_type`, `field_name`, `data`) 
		VALUES ('" . $idd . "', 'page', 'field_guid', '" . $item->GUID . "')");


        $count++;
    }
}

MYSQL_CLOSE();

?>
