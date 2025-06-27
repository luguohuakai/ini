<?php

use luguohuakai\ini\Ini;

include __DIR__ . '/../src/Ini.php';

$ini = Ini::load(__DIR__ . '/test.ini');

//echo $ini->get('test.test1');
//echo $ini->get('name');
//echo $ini->get('srun.name');
//var_export($ini->all);

$ini->set('sex', 'man');
$ini->set('srun.sex', 'woman');
$ini->set('srun.name', 'srun');
$ini->set('test.name', 'test');
$ini->set('test.age', '16');

$ini->setAll([
    'test.test2' => 'test2',
    'test.test3' => 'test3',
    'srun.name' => 'srun',
    'age' => '19',
]);
//$ini->set('test.test2', 'test2');