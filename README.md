[![Build Status](https://travis-ci.org/voku/paris.png?branch=master)](https://travis-ci.org/voku/paris)
[![codecov.io](http://codecov.io/github/voku/paris/coverage.svg?branch=master)](http://codecov.io/github/voku/paris?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/e46cf50ac9e142668e0d6b47a8ed7cdb)](https://www.codacy.com/app/voku/paris?utm_source=github.com&utm_medium=referral&utm_content=voku/paris&utm_campaign=badger)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/92e21e1f-d31e-4449-92bf-f895ff87f7d2/mini.png)](https://insight.sensiolabs.com/projects/92e21e1f-d31e-4449-92bf-f895ff87f7d2)
[![Latest Stable Version](https://poser.pugx.org/voku/paris/v/stable)](https://packagist.org/packages/voku/paris) 
[![Total Downloads](https://poser.pugx.org/voku/paris/downloads)](https://packagist.org/packages/voku/paris) 
[![Latest Unstable Version](https://poser.pugx.org/voku/paris/v/unstable)](https://packagist.org/packages/voku/paris)
[![PHP 7 ready](http://php7ready.timesplinter.ch/voku/paris/badge.svg)](https://travis-ci.org/voku/paris)
[![License](https://poser.pugx.org/voku/paris/license)](https://packagist.org/packages/voku/paris)

# Paris

[http://j4mie.github.com/idiormandparis/](http://j4mie.github.com/idiormandparis/)

WARNING: this is only a Maintained-Fork from: "https://github.com/j4mie/paris/"

INFO: you can use my Simple Active Record lib instad of Paris: "https://github.com/voku/simple-active-record"

---
## Installation

The recommended installation way is through [Composer](https://getcomposer.org).

```bash
$ composer require voku/paris
```

A lightweight Active Record implementation for PHP5.

Built on top of [Idiorm](http://github.com/j4mie/idiorm/).

Tested on PHP 5.3+ - may work on earlier versions with PDO and the correct database drivers.

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
/**
 * User: a sample user-class
 *
 * @property-read int    $id
 * @property-read string $first_name
 */
class User extends Model {
  public function tweets() {
      return $this->has_many('Tweet');
  }
  
  public function getId()
  {
    return $this->id;
  }
    
  public function getFirstName()
  {
    return $this->first_name
  }
}

/**
 * Tweet: a sample twitter-class
 *
 * @property-read int    $id
 * @property-read string $text
 */
class Tweet extends Model {
  
}

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
