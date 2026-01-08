# Server usage

## Basic setup

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use divengine\ajaxmap;

function sum($x, $y) {
    return $x + $y;
}

class Enterprise {
    public static function getEmployees() {
        return [
            ["name" => "Thomas Hardy", "salary" => 1500],
            ["name" => "Christina Berglund", "salary" => 1200],
        ];
    }
}

$server = new ajaxmap();
$server->addMethod('sum');
$server->addClass('Enterprise');
$server->addData('Server Description', 'Ajaxmap demo');
$server->go();
```

## Registering methods

```php
$server->addMethod(
    name: 'MyClass::staticMethod',
    params_complex: false,
    security: true,
    hosts: [
        ['from' => '192.168.1.1', 'to' => '192.168.1.255']
    ],
    include: __DIR__ . '/bootstrap.php',
    namespace: 'aliasName'
);
```

- `name` can be a function name or `Class::method`.
- If `name` is a method on the server instance, it will call that method.
- `params_complex = true` generates a single `params` argument on the client.
- `security = true` requires `checkMethodAccess()` to allow the call.
- `hosts` restricts access by IP range.
- `include` is a string or array of files to include before execution.
- `namespace` registers a custom alias for the method on the client.

## Registering classes

```php
$server->addClass('MyStaticClass');
```

All public static methods become available on the client.

## Exposing data

```php
$server->addData('Server Name', 'My API');
```

Spaces are converted to underscores for property names.

## Request flow

`go()` handles these cases:

- `?lib` serves the client library (`ajaxmap.js`).
- `?execute=MethodName` runs a registered method.
- `?login` / `?logout` call hooks you can override.
- Any other request returns the mapping.
