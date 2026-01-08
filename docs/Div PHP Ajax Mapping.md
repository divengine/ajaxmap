[![Readme Card](https://github-readme-stats.vercel.app/api/pin/?username=divengine&repo=ajaxmap&show_owner=true&rand=23)](https://github.com/anuraghazra/github-readme-stats)

# Div PHP Ajax Mapping

Ajaxmap is a small bridge that maps PHP functions, static class methods, and
data to a JavaScript client. It predates modern API tooling and focuses on a
simple, explicit registration model.

## What it is good for

- Small internal tools where you want quick RPC-style calls.
- Exposing a narrow set of server utilities to a browser UI.
- Prototyping without building a full API layer.

## What it is not

- A full REST or JSON:API framework.
- An automatic mapper of all PHP code.
- A secure-by-default public API.

## Core idea

On the server you register methods and data. On the client you create an
`ajaxmap` instance that fetches the mapping and builds callable methods.

See `Server.md` and `Client.md` for implementation details.
