<?php

require_once('vendor/autoload.php');

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('logger');
$log->pushHandler(new StreamHandler('log/my.log', Logger::INFO));

$base_usage = memory_get_usage();

$log->info('Начало');
$log->info(memoryUsage(memory_get_usage(), $base_usage));

$log->info('Инициируем массив');
$array = range(0, 1000000);
$log->info(memoryUsage(memory_get_usage(), $base_usage));

$count = 0;
foreach ($array as $value)
{
    $log->info('В цикле');
    $value -= 1;
    $log->info(memoryUsage(memory_get_usage(), $base_usage));

    if ($count > 5) break;
    $count++;
}

$log->info('Удалим массив');
unset($array);
$log->info(memoryUsage(memory_get_usage(), $base_usage));

$log->info('Факториал');
Factorial(10000);
$log->info(memory_get_usage());
$log->info(memoryUsage(memory_get_usage(), $base_usage));


function memoryUsage($usage, $base_usage) {
    return "Разница использования памяти: " . ($usage - $base_usage);
}

function Factorial($number) {
    if($number <= 1) {
        return 1;
    } else return ($number * Factorial($number - 1));
}