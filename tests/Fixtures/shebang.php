#!/usr/bin/env -S melody run
<?php
<<<CONFIG
packages:
    - "twig/twig:3.3.2"
CONFIG;

$twig = new Twig\Environment(new Twig\Loader\ArrayLoader(array(
    'foo' => 'Hello {{ include("bar") }}',
    'bar' => 'world',
)));

echo $twig->render('foo');
