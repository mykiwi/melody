# Melody - One-file composer scripts


## Demo

Create a file named `script.php`:

```php
<?php
<<<CONFIG
packages:
    - "symfony/finder: ^5"
CONFIG;

$finder = Symfony\Component\Finder\Finder::create()
    ->in(__DIR__)
    ->files()
    ->name('*.php')
;

foreach ($finder as $file) {
    echo $file, "\n";
}
```

And simply run it:

```bash
$ melody run script.php
```
