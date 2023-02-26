# Manual de instalação

## Instalando cakephp

```sh
composer create-project --prefer-dist cakephp/app [nome do projeto]
```

##### Configurando .htaccess

```php
RewriteCond %{HTTPS} off [OR]
RewriteCond %{HTTP_HOST} ^www\. [NC]
RewriteCond %{HTTP_HOST} ^(?:www\.)?(.+)$ [NC]
RewriteRule ^ https://%1%{REQUEST_URI} [L,NE,R=301]
```


## Instalando o plugin Winsite

Copie o pasta Winsite

```sh
\\WINSITE-SERVER\Repositorio\Cake 3\Winsite
```

para

```sh
[nome do projeto]\plugins\
```

### Configurando o plugin Winsite

#### composer.json

Acrescte o plugin search no require

```php
"friendsofcake/search": "^2.3"
```

Subistitua o código abaixo 

```sh 
"App\\": "src
```
por

```sh 
"App\\": "src, 
"Winsite\\": "./plugins/Winsite/src"
```

Atualize as dependências do projeto

```sh 
composer update
```

#### config/boostrap.php

Carregue o plugin 

```php
Plugin::load('Winsite');
```

Importe as classes abaixo

```php
use Cake\I18n\Date;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
```

Acrescente o código abaixo no final do arquivo

```php
Configure::write('Theme', [
    'title' => 'nome do projeto',
    'logo' => [
        'mini' => '<img src="/winsite/img/ logo do projeto" alt="Logo nome do projeto" title="Nome do projeto">',
        'large' => '<img src="/winsite/img/ logo do projeto" alt="Logo nome do projetob" title="Nome do projeto">',
    ],
    'folder' => ROOT,
 ]);
 ```

Substitua codigo abaixo 

```php
Type::build('time')
    ->useImmutable();
Type::build('date')
    ->useImmutable();
Type::build('datetime')
    ->useImmutable();
Type::build('timestamp')
    ->useImmutable();
```

por 

```php
Type::build('time')
    ->useImmutable()
    ->useLocaleParser();
Type::build('date')
    ->useImmutable()
    ->useLocaleParser();
Type::build('datetime')
    ->useImmutable()
    ->useLocaleParser();
Type::build('timestamp')
    ->useImmutable()
    ->useLocaleParser();

Time::setDefaultLocale('pt-BR');
FrozenTime::setDefaultLocale('pt-BR');
Date::setDefaultLocale('pt-BR');
FrozenDate::setDefaultLocale('pt-BR');

Time::setToStringFormat([IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM]);
FrozenTime::setToStringFormat([IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM]);
Date::setToStringFormat([IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE]);
FrozenDate::setToStringFormat([IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE]);

```

#### src/controller/AppController.php

Importe class Configure

```php
use Cake\Core\Configure;
```

Coloque o código abaixo dentro de initialize

```php
		$this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'userFields' => [
                        'username' => 'email',
                        'password' => 'passwd'
                    ],
                    'finder' => 'auth'
                ]
            ],
            'authError' => 'Você não está autorizado a acessar esse local',
            'loginAction' => [
                'controller' => 'users',
                'action' => 'login'
            ],
            'loginRedirect' => [
                'controller' => 'users',
                'action' => 'dashboard'
            ],
            'logoutRedirect' => [
                'controller' => 'users',
                'action' => 'login'
            ],
        ]);
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see http://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        $this->loadComponent('Security');
        $this->loadComponent('Csrf');
```

Acrescente código abaixo no final de beforeRender

```php
$this->viewBuilder()->setTheme('Winsite');
$this->set('theme', Configure::read('Theme'));
```

#### src/view/AppView.php

Acrescente o código abaixo dentro de initialize

```php
$this->loadHelper('Form', ['className' => 'Winsite.Form']);
```

##### config/app.php

Configure o banco de dados

```php
'Datasources' => [
        'default' => [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\[Mysql/Postgree]',
            'persistent' => false,
            'host' => '[host]',
            //'port' => 'non_standard_port_number',
            'username' => '[username]',
            'password' => '[password]',
            'database' => '[database]',
            'encoding' => 'utf8',
            'timezone' => 'UTC',
            'flags' => [],
            'cacheMetadata' => true,
            'log' => false,
            'quoteIdentifiers' => false,
            //'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],
            'url' => env('DATABASE_URL', null),
        ],
```

#### Gerando o bake

Gere o bake dos models individualmente para configurar displayFields, validações e relacimentos

##### Model

```php
bin/cake bake model [nome do model] -f [sempre sobrescreve o arquivo] -t Winsite [theme winsite]
```

##### Controller

```php
bin/cake bake controller all -f -t Winsite
```

##### Template

```php
bin/cake bake template all -f -t Winsite
```

