<?php

use idiorm\orm\ORM;
use paris\orm\Model;

/**
 * Models for use during testing
 */

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class Simple extends Model
{
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class ComplexModelClassName extends Model
{
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class ModelWithCustomTable extends Model
{
  public static $_table = 'custom_table';
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class ModelWithCustomTableAndCustomIdColumn extends Model
{
  public static $_table     = 'custom_table';
  public static $_id_column = 'custom_id_column';
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class ModelWithFilters extends Model
{
  /**
   * @param ORM $orm
   *
   * @return ORM
   */
  public static function name_is_fred(ORM $orm)
  {
    return $orm->where('name', 'Fred');
  }

  /**
   * @param ORM    $orm
   * @param string $name
   *
   * @return ORM
   */
  public static function name_is(ORM $orm, $name)
  {
    return $orm->where('name', $name);
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class ModelWithCustomConnection extends Model
{
  const ALTERNATE = 'alternate';
  public static $_connection_name = self::ALTERNATE;
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class Profile extends Model
{
  /**
   * @return ORM|null
   */
  public function user()
  {
    return $this->belongs_to('User');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class User extends Model
{
  /**
   * @return ORM
   */
  public function profile()
  {
    return $this->has_one('Profile');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class UserTwo extends Model
{
  /**
   * @return ORM
   */
  public function profile()
  {
    return $this->has_one('Profile', 'my_custom_fk_column');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class UserFive extends Model
{
  /**
   * @return ORM
   */
  public function profile()
  {
    return $this->has_one('Profile', 'my_custom_fk_column', 'name');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class ProfileTwo extends Model
{
  /**
   * @return ORM|null
   */
  public function user()
  {
    return self::belongs_to('User', 'custom_user_fk_column');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class ProfileThree extends Model
{
  /**
   * @return ORM|null
   */
  public function user()
  {
    return $this->belongs_to('User', 'custom_user_fk_column', 'name');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class Post extends Model
{
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class UserThree extends Model
{
  /**
   * @return ORM
   */
  public function posts()
  {
    return $this->has_many('Post');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class UserFour extends Model
{
  /**
   * @return ORM
   */
  public function posts()
  {
    return $this->has_many('Post', 'my_custom_fk_column');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class UserSix extends Model
{
  /**
   * @return ORM
   */
  public function posts()
  {
    return $this->has_many('Post', 'my_custom_fk_column', 'name');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class Author extends Model
{
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class AuthorBook extends Model
{
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class Book extends Model
{
  /**
   * @return ORM
   */
  public function authors()
  {
    return $this->has_many_through('Author');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class BookTwo extends Model
{
  /**
   * @return ORM
   */
  public function authors()
  {
    return $this->has_many_through('Author', 'AuthorBook', 'custom_book_id', 'custom_author_id');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class BookThree extends Model
{
  /**
   * @return ORM
   */
  public function authors()
  {
    return $this->has_many_through('Author', 'AuthorBook', 'custom_book_id', 'custom_author_id', 'custom_book_id_in_book_table', 'custom_author_id_in_author_table');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class BookFour extends Model
{
  /**
   * @return ORM
   */
  public function authors()
  {
    return $this->has_many_through('Author', 'AuthorBook', 'custom_book_id', 'custom_author_id', null, 'custom_author_id_in_author_table');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class BookFive extends Model
{
  /**
   * @return ORM
   */
  public function authors()
  {
    return $this->has_many_through('Author', 'AuthorBook', 'custom_book_id', 'custom_author_id', 'custom_book_id_in_book_table');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class MockPrefix_Simple extends Model
{
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class MockPrefix_TableSpecified extends Model
{
  public static $_table = 'simple';
}
