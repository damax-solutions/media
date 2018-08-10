# Development

Build image:

```bash
$ docker build -t damax-media .
```

Install dependencies:

```bash
$ docker run --rm -v $(pwd):/app -w /app damax-media composer install
```

Fix php coding standards:

```bash
$ docker run --rm -v $(pwd):/app -w /app damax-media composer cs
```

Running tests:

```bash
$ docker run --rm -v $(pwd):/app -w /app damax-media composer test
$ docker run --rm -v $(pwd):/app -w /app damax-media composer test-cc
```
