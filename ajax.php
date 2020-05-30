<?php

require_once "lesson_5.php";

$request = $_GET;
$id = intval($request["id"]);

$result = [];

$redisService = new RedisService();

$start = microtime();
if ($cache = $redisService->get('product_' . $id)) {
    $result = $cache;
    $result['data']['from'] = 'cache';
} else {
    $product = MySqlDB::getInstance()
        ->query("SELECT * FROM goods WHERE id = :id", [":id" => $id]);

    if ($product) {
        $result = reset($product);
        $redisService->set('product_' . $id, $result);
        $result['data']['from'] = 'db';
    }
}
$result['data']['time'] = microtime() - $start;

echo json_encode($result, JSON_UNESCAPED_UNICODE);