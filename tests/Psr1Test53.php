<?php
use paris\orm\Model;

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class Psr1Test53 extends PHPUnit_Framework_TestCase
{

  public function setUp()
  {
    // Set up the dummy database connection
    ORM::set_db(new MockPDO('sqlite::memory:'));

    // Enable logging
    ORM::configure('logging', true);
  }

  public function tearDown()
  {
    ORM::configure('logging', false);
    ORM::set_db(null);
  }

  public function testInsertData()
  {
    $widget = Model::factory('Simple')->create();
    $widget->name = 'Fred';
    $widget->age = 10;
    $widget->save();
    $expected = "INSERT INTO `simple` (`name`, `age`) VALUES ('Fred', '10')";
    self::assertEquals($expected, ORM::getLastQuery());
  }

  public function testUpdateData()
  {
    $widget = Model::factory('Simple')->findOne(1);
    $widget->name = 'Fred';
    $widget->age = 10;
    $widget->save();
    $expected = "UPDATE `simple` SET `name` = 'Fred', `age` = '10' WHERE `id` = '1'";
    self::assertEquals($expected, ORM::getLastQuery());
  }

  public function testDeleteData()
  {
    $widget = Model::factory('Simple')->findOne(1);
    $widget->delete();
    $expected = "DELETE FROM `simple` WHERE `id` = '1'";
    self::assertEquals($expected, ORM::getLastQuery());
  }

  public function testInsertingDataContainingAnExpression()
  {
    $widget = Model::factory('Simple')->create();
    $widget->name = 'Fred';
    $widget->age = 10;
    $widget->setExpr('added', 'NOW()');
    $widget->save();
    $expected = "INSERT INTO `simple` (`name`, `age`, `added`) VALUES ('Fred', '10', NOW())";
    self::assertEquals($expected, ORM::getLastQuery());
  }

  public function testHasOneRelation()
  {
    /* @var $user User2 */
    $user = Model::factory('User2')->findOne(1);
    $profile = $user->profile()->findOne();
    $expected = "SELECT * FROM `profile2` WHERE `user2_id` = '1' LIMIT 1";
    self::assertEquals($expected, ORM::getLastQuery());
    self::assertTrue($profile instanceof Profile);
  }

  public function testHasOneWithCustomForeignKeyName()
  {
    /* @var $user2 UserTwo2 */
    $user2 = Model::factory('UserTwo2')->findOne(1);
    $profile = $user2->profile()->findOne();
    $expected = "SELECT * FROM `profile2` WHERE `my_custom_fk_column` = '1' LIMIT 1";
    self::assertEquals($expected, ORM::getLastQuery());
    self::assertTrue($profile instanceof Profile);
  }

  public function testBelongsToRelation()
  {
    /* @var $user2 UserTwo2 */
    $user2 = Model::factory('UserTwo2')->findOne(1);
    /* @var $profile2 Profile2 */
    $profile2 = $user2->profile()->findOne();
    $profile2->user_id = 1;
    /* @var $user3 User */
    $user3 = $profile2->user()->findOne();
    $expected = "SELECT * FROM `user2` WHERE `id` = '' LIMIT 1";
    self::assertEquals($expected, ORM::getLastQuery());
    self::assertTrue($profile2 instanceof Profile2);
    self::assertTrue($user3 instanceof User);
  }

  public function testBelongsToRelationWithCustomForeignKeyName()
  {
    /* @var $profile2 ProfileTwo2 */
    $profile2 = Model::factory('ProfileTwo2')->findOne(1);
    $profile2->custom_user_fk_column = 5;
    $user4 = $profile2->user()->findOne();
    $expected = "SELECT * FROM `user2` WHERE `id` = '5' LIMIT 1";
    self::assertEquals($expected, ORM::getLastQuery());
    self::assertTrue($user4 instanceof User2);
  }

  public function testHasManyRelation()
  {
    /* @var $user4 UserThree2 */
    $user4 = Model::factory('UserThree2')->findOne(1);
    $posts = $user4->posts()->findMany();
    $expected = "SELECT * FROM `post2` WHERE `user_three2_id` = '1'";
    self::assertEquals($expected, ORM::getLastQuery());
    self::assertTrue(is_array($posts));
  }

  public function testHasManyRelationWithCustomForeignKeyName()
  {
    /* @var $user5 UserFour2 */
    $user5 = Model::factory('UserFour2')->findOne(1);
    $posts = $user5->posts()->findMany();
    $expected = "SELECT * FROM `post2` WHERE `my_custom_fk_column` = '1'";
    self::assertEquals($expected, ORM::getLastQuery());
    self::assertTrue(is_array($posts));
  }

  public function testHasManyThroughRelation()
  {
    /* @var $book Book2 */
    $book = Model::factory('Book2')->findOne(1);
    $authors = $book->authors()->findMany();
    $expected = "SELECT `author2`.* FROM `author2` JOIN `author2book2` ON `author2`.`id` = `author2book2`.`author2_id` WHERE `author2book2`.`book2_id` = '1'";
    self::assertEquals($expected, ORM::getLastQuery());
    self::assertTrue(is_array($authors));
  }

  public function testHasManyThroughRelationWithCustomIntermediateModelAndKeyNames()
  {
    /* @var $book2 BookTwo2 */
    $book2 = Model::factory('BookTwo2')->findOne(1);
    $authors2 = $book2->authors()->findMany();
    $expected = "SELECT `author2`.* FROM `author2` JOIN `author_book2` ON `author2`.`id` = `author_book2`.`custom_author_id` WHERE `author_book2`.`custom_book_id` = '1'";
    self::assertEquals($expected, ORM::getLastQuery());
    self::assertTrue(is_array($authors2));
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class Profile2 extends Model
{
  /**
   * @return \paris\orm\ORMWrapper
   */
  public function user()
  {
    return self::belongsTo('User2');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class User2 extends Model
{
  /**
   * @return \paris\orm\ORMWrapper
   */
  public function profile()
  {
    return self::hasOne('Profile2');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class UserTwo2 extends Model
{
  /**
   * @return \paris\orm\ORMWrapper
   */
  public function profile()
  {
    return self::hasOne('Profile2', 'my_custom_fk_column');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class ProfileTwo2 extends Model
{
  /**
   * @return \paris\orm\ORMWrapper
   */
  public function user()
  {
    return self::belongsTo('User2', 'custom_user_fk_column');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class Post2 extends Model
{
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class UserThree2 extends Model
{
  /**
   * @return \paris\orm\ORMWrapper
   */
  public function posts()
  {
    return self::hasMany('Post2');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class UserFour2 extends Model
{
  /**
   * @return \paris\orm\ORMWrapper
   */
  public function posts()
  {
    return self::hasMany('Post2', 'my_custom_fk_column');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class Author2 extends Model
{
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class AuthorBook2 extends Model
{
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class Book2 extends Model
{
  /**
   * @return \paris\orm\ORMWrapper
   */
  public function authors()
  {
    return self::hasManyThrough('Author2');
  }
}

/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
class BookTwo2 extends Model
{
  /**
   * @return \paris\orm\ORMWrapper
   */
  public function authors()
  {
    return self::hasManyThrough('Author2', 'AuthorBook2', 'custom_book_id', 'custom_author_id');
  }
}
