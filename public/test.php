<?php

require __DIR__ . '/../vendor/autoload.php';

$container = new Slim\Container(require  __DIR__ . '/../app/config/config.php');

$storage = $container->get('storage');
$values = $storage->read('2016032208374720149');

$randomData = [];

foreach ($values as $asset => $value) {
    if (mt_rand(1, 100) > 80) {
        continue;
    }
    
    $randomData[$asset] = $value;
}

unset($values);

$equipment = new Bridge\Model\Equipment($container);
$hashed = $equipment->getHashedEquipments();
$bench = new Ubench();

$bench->run(function () use ($randomData, $hashed) {
    echo count($randomData);
    echo '<br>';
    echo count($hashed);
    echo '<br>';
    echo '<br><pre>';
    print_r(Bridge\Service\Differ\ArrayDiffer::diff($hashed, $randomData));
});

echo '<br>', PHP_EOL, $bench->getTime(), '<br>', PHP_EOL, $bench->getMemoryPeak();

