<?php

use Cake\Core\Plugin;
use Cake\Database\TypeFactory;
use Cake\I18n\Date;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;

Time::setDefaultLocale('pt-BR');
FrozenTime::setDefaultLocale('pt-BR');
Date::setDefaultLocale('pt-BR');
FrozenDate::setDefaultLocale('pt-BR');

TypeFactory::build('time')
    ->useImmutable()
    ->useLocaleParser();
TypeFactory::build('date')
    ->useImmutable()
    ->useLocaleParser();
TypeFactory::build('datetime')
    ->useImmutable()
    ->useLocaleParser();
TypeFactory::build('timestamp')
    ->useImmutable()
    ->useLocaleParser();
TypeFactory::build('datetimefractional')
    ->useImmutable()
    ->useLocaleParser();
TypeFactory::build('timestampfractional')
    ->useImmutable()
    ->useLocaleParser();
TypeFactory::build('timestamptimezone')
    ->useImmutable()
    ->useLocaleParser();

Date::setToStringFormat('dd/MM/yyyy');
FrozenDate::setToStringFormat('dd/MM/yyyy');
Time::setToStringFormat('dd/MM/yyyy HH:mm:ss');
FrozenTime::setToStringFormat('dd/MM/yyyy HH:mm:ss');

TypeFactory::map('cpf', 'Winsite\Database\Type\CpfType');
TypeFactory::map('cnpj', 'Winsite\Database\Type\CnpjType');

include Plugin::path('Winsite') . 'config' . DS . 'inflections.php';
