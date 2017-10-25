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
    $db = new PDO("mysql:host=mosaic.unelabs.com;dbname=mosaic_unelabs", "mosaic_unelabs", "Bilkent11");
} catch ( PDOException $e ){
    ob_clean();
    print $e->getMessage();
}

