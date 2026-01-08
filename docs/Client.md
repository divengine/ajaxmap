# Client usage

## Load the client library

You can load the bundled library from the server:

```html
<script src="server.php?lib"></script>
```

Or host it yourself:

```html
<script src="/path/to/ajaxmap.js"></script>
```

## Create a client

```html
<script>
  var api = new ajaxmap("server.php");
</script>
```

The constructor requests the mapping and builds methods on the instance.

## Call mapped methods

```html
<script>
  var total = api.sum(10, 5);
  var employees = api.Enterprise.getEmployees();
</script>
```

## Read mapped data

```html
<script>
  console.log(api.Server_Description);
</script>
```

Data keys use underscores instead of spaces.

## Login and logout

```html
<script>
  api.__login('user', 'pass');
  api.__logout();
</script>
```

## Note about sync requests

The current client sends synchronous XHR requests. This can block the UI on
modern browsers. If you need async behavior, consider adapting `ajaxmap.js`
to use asynchronous requests or a Promise-based wrapper.
