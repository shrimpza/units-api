<?php

require_once(dirname(__FILE__) . '/unitsapi.php');

$api = new UnitsAPI();

try {
    $result = $api->convert($_GET['value'], $_GET['from'], $_GET['to']);

    // remove factors from output
    unset($result['from']['unit']['factors']);
    unset($result['to']['unit']['factors']);

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(array("error" => $e->getMessage()));
}

?>
