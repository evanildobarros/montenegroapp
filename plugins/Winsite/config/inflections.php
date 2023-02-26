<?php

use Cake\Utility\Inflector;

Inflector::rules('singular', [
    '/^(.*)(oes|aes|aos)$/i' => '\1ao',
    '/^(.*)(a|e|o|u)is$/i' => '\1\2l',
    '/^(.*)e?is$/i' => '\1il',
    '/^(.*)(r|s|z)es$/i' => '\1\2',
    '/^(.*)ns$/i' => '\1m',
    '/^(.*)s$/i' => '\1',
]);

Inflector::rules('plural', [
    '/^(.*)ao$/i' => '\1oes',
    '/^(.*)(r|s|z)$/i' => '\1\2es',
    '/^(.*)(a|e|o|u)l$/i' => '\1\2is',
    '/^(.*)il$/i' => '\1is',
    '/^(.*)(m|n)$/i' => '\1ns',
    '/^(.*)$/i' => '\1s',
]);

Inflector::rules('uninflected', [
    'atlas',
    'lapis',
    'onibus',
    'pires',
    'virus',
    '.*x',
    'status',
]);

Inflector::rules('irregular', [
    'abdomen' => 'abdomens',
    'alcool' => 'alcoois',
    'alemao' => 'alemaes',
    'artesa' => 'artesaos',
    'as' => 'ases',
    'bencao' => 'bencaos',
    'campus' => 'campi',
    'cao' => 'caes',
    'capelao' => 'capelaes',
    'capitao' => 'capitaes',
    'chao' => 'chaos',
    'charlatao' => 'charlataes',
    'cidadao' => 'cidadaos',
    'consul' => 'consules',
    'cristao' => 'cristaos',
    'dificil' => 'dificeis',
    'email' => 'emails',
    'escrivao' => 'escrivaes',
    'fossel' => 'fosseis',
    'gas' => 'gases',
    'germens' => 'germen',
    'grao' => 'graos',
    'hifens' => 'hifen',
    'irmao' => 'irmaos',
    'lei' => 'leis',
    'liquens' => 'liquen',
    'mal' => 'males',
    'mao' => 'maos',
    'orfao' => 'orfaos',
    'pai' => 'pais',
    'pais' => 'paises',
    'pao' => 'paes',
    'projetil' => 'projeteis',
    'reptil' => 'repteis',
    'sacristao' => 'sacristaes',
    'sotao' => 'sotaos',
    'tabeliao' => 'tabeliaes',
]);
