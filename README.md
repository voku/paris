[![Build Status](https://travis-ci.org/voku/paris.png?branch=master)](https://travis-ci.org/voku/paris)
[![codecov.io](http://codecov.io/github/voku/paris/coverage.svg?branch=master)](http://codecov.io/github/voku/paris?branch=master)
[![Codacy Badge](https://www.codacy.com/project/badge/e46cf50ac9e142668e0d6b47a8ed7cdb)](https://www.codacy.com/app/voku/paris)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/92e21e1f-d31e-4449-92bf-f895ff87f7d2/mini.png)](https://insight.sensiolabs.com/projects/92e21e1f-d31e-4449-92bf-f895ff87f7d2)
[![Total Downloads](https://poser.pugx.org/voku/paris/downloads)](https://packagist.org/packages/voku/paris)
[![License](https://poser.pugx.org/voku/paris/license.svg)](https://packagist.org/packages/voku/paris)

# Paris

[http://j4mie.github.com/idiormandparis/](http://j4mie.github.com/idiormandparis/)

WARNING: this is only a Maintained-Fork of "https://github.com/j4mie/paris/"

---
## Installation

The recommended installation way is through [Composer](https://getcomposer.org).

```bash
$ composer require voku/paris
```

---
### Feature complete

Paris is now considered to be feature complete as of version 1.5.0. Whilst it will continue to be maintained with bug fixes there will be no further new features added from this point on.

**Please do not submit feature requests or pull requests adding new features as they will be closed without ceremony.**

---

A lightweight Active Record implementation for PHP5.

Built on top of [Idiorm](http://github.com/j4mie/idiorm/).

Tested on PHP 5.2.0+ - may work on earlier versions with PDO and the correct database drivers.

Released under a [BSD license](http://en.wikipedia.org/wiki/BSD_licenses).

Features
--------

* Extremely simple configuration.
* Exposes the full power of [Idiorm](http://github.com/j4mie/idiorm/)'s fluent query API.
* Supports associations.
* Simple mechanism to encapsulate common queries in filter methods.
* Built on top of [PDO](http://php.net/pdo).
* Uses [prepared statements](http://uk.php.net/manual/en/pdo.prepared-statements.php) throughout to protect against [SQL injection](http://en.wikipedia.org/wiki/SQL_injection) attacks.
* Database agnostic. Currently supports SQLite, MySQL, Firebird and PostgreSQL. May support others, please give it a try!
* Supports collections of models with method chaining to filter or apply actions to multiple results at once.
* Multiple connections are supported

Documentation
-------------

The documentation is hosted on Read the Docs: [paris.rtfd.org](http://paris.rtfd.org)

### Building the Docs ###

You will need to install [Sphinx](http://sphinx-doc.org/) and then in the docs folder run:

    make html

The documentation will now be in docs/_build/html/index.html

Let's See Some Code
-------------------
```php
class User extends Model {
    public function tweets() {
        return $this->has_many('Tweet');
    }
}

class Tweet extends Model {}

$user = Model::factory('User')
    ->where_equal('username', 'j4mie')
    ->find_one();
$user->first_name = 'Jamie';
$user->save();

$tweets = $user->tweets()->find_many();
foreach ($tweets as $tweet) {
    echo $tweet->text;
}
```
