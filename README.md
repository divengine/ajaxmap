# Div PHP Ajax Mapping

Div PHP Ajax Mapping exposes PHP functions, static methods, and data to a
JavaScript client through a small AJAX bridge. You register what is allowed
on the server, and the client calls those endpoints as if they were local.

## Requirements

- PHP 8.2 or higher

## Installation

```shell
composer require divengine/ajaxmap
```

## Quick start

Server (`server.php`):

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
$server->go();
```

Client (`index.html`):

```html
<script src="server.php?lib"></script>
<script>
  var api = new ajaxmap("server.php");
  var result = api.sum(20, 10);
  var employees = api.Enterprise.getEmployees();
  console.log(result, employees[0].name);
</script>
```

## How it works

- `server.php?lib` serves the client library (`ajaxmap.js`).
- `new ajaxmap("server.php")` fetches a mapping and builds methods on the client.
- Calls are sent as POST requests to `server.php?execute=...`.

## Security notes

Only register what you want to expose. This library executes registered calls
server-side and returns results to the browser.

- Use `addMethod(..., $security = true)` and implement `checkMethodAccess()`.
- Restrict by IP using the `$hosts` parameter in `addMethod()`.
- The client evaluates responses; keep endpoints same-origin and trusted.

## Docs

See `docs/README.md` for detailed guides and FAQ.

## License

This project is licensed under the GNU General Public License. See `LICENSE`.
