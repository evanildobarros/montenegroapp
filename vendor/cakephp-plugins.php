<?php
$baseDir = dirname(dirname(__FILE__));

return [
    'plugins' => [
        'AssetMix' => $baseDir . '/vendor/ishanvyas22/asset-mix/',
        'AuditLog' => $baseDir . '/vendor/hevertonfreitas/cakephp-audit-log/',
        'Authentication' => $baseDir . '/vendor/cakephp/authentication/',
        'Authorization' => $baseDir . '/vendor/cakephp/authorization/',
        'Bake' => $baseDir . '/vendor/cakephp/bake/',
        'Cake/Localized' => $baseDir . '/vendor/cakephp/localized/',
        'Cake/TwigView' => $baseDir . '/vendor/cakephp/twig-view/',
        'Correios' => $baseDir . '/plugins/Correios/',
        'Cors' => $baseDir . '/vendor/ozee31/cakephp-cors/',
        'Crud' => $baseDir . '/vendor/friendsofcake/crud/',
        'DebugKit' => $baseDir . '/vendor/cakephp/debug_kit/',
        'IdeHelper' => $baseDir . '/vendor/dereuromark/cakephp-ide-helper/',
        'Josegonzalez/Upload' => $baseDir . '/vendor/josegonzalez/cakephp-upload/',
        'Migrations' => $baseDir . '/vendor/cakephp/migrations/',
        'Queue' => $baseDir . '/vendor/dereuromark/cakephp-queue/',
        'Search' => $baseDir . '/vendor/friendsofcake/search/',
        'Winsite' => $baseDir . '/plugins/Winsite/',
    ],
];
