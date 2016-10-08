<?php
    include("wp-config.php");
    if (!mysql_connect('127.0.0.1', 'root', 'abyss4529')) {  die('Could not connect: ' . mysql_error());  }
    if (!mysql_select_db('wp_cutv')) {  die('Could not connect: ' . mysql_error());  }

    $result = mysql_query("SELECT term_taxonomy_id FROM ".$table_prefix."term_taxonomy");
    while ($row = mysql_fetch_array($result)) {
        $term_taxonomy_id = $row['term_taxonomy_id'];
        echo "term_taxonomy_id: ".$term_taxonomy_id." count = ";
        $countresult = mysql_query("SELECT count(*) FROM ".$table_prefix."term_relationships WHERE term_taxonomy_id = '$term_taxonomy_id'");
        $countarray = mysql_fetch_array($countresult);
        $count = $countarray[0];
        echo $count."<br />";
        mysql_query("UPDATE ".$table_prefix."term_taxonomy SET count = '$count' WHERE term_taxonomy_id = '$term_taxonomy_id'");
    }

    $result = mysql_query("SELECT ID FROM ".$table_prefix."posts");
    while ($row = mysql_fetch_array($result)) {
        $post_id = $row['ID'];
        echo "post_id: ".$post_id." count = ";
        $countresult = mysql_query("SELECT count(*) FROM ".$table_prefix."comments WHERE comment_post_ID = '$post_id' AND comment_approved = 1");
        $countarray = mysql_fetch_array($countresult);
        $count = $countarray[0];
        echo $count."<br />";
        mysql_query("UPDATE ".$table_prefix."posts SET comment_count = '$count' WHERE ID = '$post_id'");
    }
