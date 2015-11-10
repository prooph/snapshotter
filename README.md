# snapshotter

Snapshot tool for the prooph event-store

Take aggregate snapshots with ease

[![Build Status](https://travis-ci.org/prooph/snapshotter.svg?branch=master)](https://travis-ci.org/prooph/snapshotter)
[![Coverage Status](https://coveralls.io/repos/prooph/snapshotter/badge.svg?branch=master&service=github)](https://coveralls.io/github/prooph/snapshotter?branch=master)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/prooph/improoph)

## Documentation

Documentation is [in the doc tree](docs/), and can be compiled using [bookdown](http://bookdown.io) and [Docker](https://www.docker.com/)

```console
$ docker run -it --rm -v $(pwd):/app sandrokeil/bookdown docs/bookdown.json
$ docker run -it --rm -p 8080:8080 -v $(pwd):/app php:5.6-cli php -S 0.0.0.0:8080 -t /app/docs/html
```

or make sure bookdown is installed globally via composer and `$HOME/.composer/vendor/bin` is on your `$PATH`.

```console
$ bookdown docs/bookdown.json
$ php -S 0.0.0.0:8080 -t docs/html/
```

Then browse to [http://localhost:8080/](http://localhost:8080/)

## Support

- Ask questions on [prooph-users](https://groups.google.com/forum/?hl=de#!forum/prooph) google group.
- File issues at [https://github.com/prooph/snapshotter/issues](https://github.com/prooph/snapshotter/issues).
- Say hello in the [prooph gitter](https://gitter.im/prooph/improoph) chat.

## Contribute

Please feel free to fork and extend existing or add new features and send a pull request with your changes!
To establish a consistent code quality, please provide unit tests for all your changes and may adapt the documentation.

## Dependencies

Please refer to the project [composer.json](composer.json) for the list of dependencies.

## License

Released under the [New BSD License](LICENSE).
