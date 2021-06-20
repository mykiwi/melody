<?php

<<<CONFIG
packages:
    - "twig/twig:3.3.2"
    - "php: >=5.3.0"
    - "ext-pdo: *"
CONFIG;

$twig = new Twig\Environment(new Twig\Loader\ArrayLoader(array(
    'foo' => 'Hello {{ include("bar") }}',
    'bar' => 'world',
)));

echo $twig->render('foo');
