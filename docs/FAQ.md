# FAQ

## Is this a general API framework?

No. Ajaxmap is a small RPC-style bridge for explicit mappings. It is not a full
REST framework and does not auto-expose all PHP code.

## How are responses parsed on the client?

The client evaluates the response as JavaScript. Keep endpoints trusted and
same-origin. Do not expose ajaxmap publicly without additional safeguards.

## What about security?

Use `addMethod(..., $security = true)` and implement `checkMethodAccess()` or
override `login()` and `verifyAuth()` in a subclass. You can also restrict by
IP ranges with the `$hosts` parameter.

## Can I send arrays or objects?

Yes. The client serializes parameters and the server unserializes them. Keep
inputs trusted to avoid unsafe data.

## Why are calls synchronous?

The default client uses synchronous XHR. If you need async calls, you can fork
`ajaxmap.js` and adjust the `ajax()` method to use async requests.

## What PHP versions are supported?

The package requires PHP 8.2 or higher.
