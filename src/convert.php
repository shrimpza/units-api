<?php

require_once(dirname(__FILE__) . '/unitsapi.php');

$api = new UnitsAPI();

try {
    $result = $api->convert($_GET['value'], $_GET['from'], $_GET['to']);
    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(array("error" => $e->getMessage()));
}

?>
