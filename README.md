## Development

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
$ docker run --rm -v $(pwd):/app -w /app damax-media ./vendor/bin/php-cs-fixer fix
```

Running tests:

```bash
$ docker run --rm -v $(pwd):/app -w /app damax-media ./vendor/bin/simple-phpunit
$ docker run --rm -v $(pwd):/app -w /app damax-media ./bin/phpunit-coverage
```
