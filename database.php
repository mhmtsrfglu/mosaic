<?php
/**
 * Created by PhpStorm.
 * User: mehmetserefoglu
 * Date: 17.10.2017
 * Time: 14:55
 * github: github.com/mhmtsrfglu
 */

try {
    ob_start();
    $db = new PDO("mysql:host=eu-cdbr-west-01.cleardb.com;dbname=heroku_4cf1778fdb58598", "b51450aeed3fc6", "bfc2c232");
} catch ( PDOException $e ){
    ob_clean();
    print $e->getMessage();
}

