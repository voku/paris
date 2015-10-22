<?php

use paris\orm\Model;

/**
 * Class ParisTest
 */
class ParisTest extends PHPUnit_Framework_TestCase
{

  const ALTERNATE = 'alternate';

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

  public function testSimpleAutoTableName()
  {
    Model::factory('Simple')->find_many();
    $expected = 'SELECT * FROM `simple`';
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testComplexModelClassName()
  {
    Model::factory('ComplexModelClassName')->find_many();
    $expected = 'SELECT * FROM `complex_model_class_name`';
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testModelWithCustomTable()
  {
    Model::factory('ModelWithCustomTable')->find_many();
    $expected = 'SELECT * FROM `custom_table`';
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testCustomIDColumn()
  {
    Model::factory('ModelWithCustomTableAndCustomIdColumn')->find_one(5);
    $expected = "SELECT * FROM `custom_table` WHERE `custom_id_column` = '5' LIMIT 1";
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testFilterWithNoArguments()
  {
    Model::factory('ModelWithFilters')->filter('name_is_fred')->find_many();
    $expected = "SELECT * FROM `model_with_filters` WHERE `name` = 'Fred'";
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testFilterWithArguments()
  {
    Model::factory('ModelWithFilters')->filter('name_is', 'Bob')->find_many();
    $expected = "SELECT * FROM `model_with_filters` WHERE `name` = 'Bob'";
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testInsertData()
  {
    $widget = Model::factory('Simple')->create();
    $widget->name = 'Fred';
    $widget->age = 10;
    $widget->save();
    $expected = "INSERT INTO `simple` (`name`, `age`) VALUES ('Fred', '10')";
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testUpdateData()
  {
    $widget = Model::factory('Simple')->find_one(1);
    $widget->name = 'Fred';
    $widget->age = 10;
    $widget->save();
    $expected = "UPDATE `simple` SET `name` = 'Fred', `age` = '10' WHERE `id` = '1'";
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testDeleteData()
  {
    $widget = Model::factory('Simple')->find_one(1);
    $widget->delete();
    $expected = "DELETE FROM `simple` WHERE `id` = '1'";
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testInsertingDataContainingAnExpression()
  {
    $widget = Model::factory('Simple')->create();
    $widget->name = 'Fred';
    $widget->age = 10;
    $widget->set_expr('added', 'NOW()');
    $widget->save();
    $expected = "INSERT INTO `simple` (`name`, `age`, `added`) VALUES ('Fred', '10', NOW())";
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testHasOneRelation()
  {
    /* @var $user User */
    $user = Model::factory('User')->find_one(1);
    $profile = $user->profile()->find_one();
    $expected = "SELECT * FROM `profile` WHERE `user_id` = '1' LIMIT 1";
    self::assertEquals($expected, ORM::get_last_query());
    self::assertTrue($profile instanceof Profile);
  }

  public function testHasOneWithCustomForeignKeyName()
  {
    /* @var $user2 UserTwo */
    $user2 = Model::factory('UserTwo')->find_one(1);
    $profile = $user2->profile()->find_one();
    $expected = "SELECT * FROM `profile` WHERE `my_custom_fk_column` = '1' LIMIT 1";
    self::assertEquals($expected, ORM::get_last_query());
    self::assertTrue($profile instanceof Profile);
  }

  public function testHasOneWithCustomForeignKeyNameInBaseAndAssociatedTables()
  {
    /* @var $user5 UserFive */
    $user5 = Model::factory('UserFive')->find_one(1);
    $profile = $user5->profile()->find_one();
    $expected = "SELECT * FROM `profile` WHERE `my_custom_fk_column` = 'Fred' LIMIT 1";
    self::assertEquals($expected, ORM::get_last_query());
    self::assertTrue($profile instanceof Profile);
  }

  public function testBelongsToRelation()
  {
    /* @var $user2 UserTwo */
    $user2 = Model::factory('UserTwo')->find_one(1);
    $profile = $user2->profile()->find_one();
    /* @var $profile Profile */
    $profile->user_id = 1;
    $user3 = $profile->user()->find_one();
    $expected = "SELECT * FROM `user` WHERE `id` = '1' LIMIT 1";
    self::assertEquals($expected, ORM::get_last_query());
    self::assertTrue($user3 instanceof User);
  }

  public function testBelongsToRelationWithCustomForeignKeyName()
  {
    /* @var $profile2 ProfileTwo */
    $profile2 = Model::factory('ProfileTwo')->find_one(1);
    $profile2->custom_user_fk_column = 5;
    $user4 = $profile2->user()->find_one();
    $expected = "SELECT * FROM `user` WHERE `id` = '5' LIMIT 1";
    self::assertEquals($expected, ORM::get_last_query());
    self::assertTrue($user4 instanceof User);
  }

  public function testBelongsToRelationWithCustomForeignKeyNameInBaseAndAssociatedTables()
  {
    /* @var $profile3 ProfileThree */
    $profile3 = Model::factory('ProfileThree')->find_one(1);
    $profile3->custom_user_fk_column = 'John Doe';
    $user4 = $profile3->user()->find_one();
    $expected = "SELECT * FROM `user` WHERE `name` = 'John Doe' LIMIT 1";
    self::assertEquals($expected, ORM::get_last_query());
    self::assertTrue($user4 instanceof User);
  }

  public function testHasManyRelation()
  {
    /* @var $user3 UserThree */
    $user3 = Model::factory('UserThree')->find_one(1);
    $posts = $user3->posts()->find_many();
    $expected = "SELECT * FROM `post` WHERE `user_three_id` = '1'";
    self::assertEquals($expected, ORM::get_last_query());
    self::assertTrue($user3 instanceof UserThree);
    self::assertTrue(is_array($posts));
  }

  public function testHasManyRelationWithCustomForeignKeyName()
  {
    /* @var $user4 UserFour */
    $user4 = Model::factory('UserFour')->find_one(1);
    $posts = $user4->posts()->find_many();
    $expected = "SELECT * FROM `post` WHERE `my_custom_fk_column` = '1'";
    self::assertEquals($expected, ORM::get_last_query());
    self::assertTrue(is_array($posts));
  }

  public function testHasManyRelationWithCustomForeignKeyNameInBaseAndAssociatedTables()
  {
    /* @var $user6 UserSix */
    $user6 = Model::factory('UserSix')->find_one(1);
    $posts = $user6->posts()->find_many();
    $expected = "SELECT * FROM `post` WHERE `my_custom_fk_column` = 'Fred'";
    self::assertEquals($expected, ORM::get_last_query());
    self::assertTrue(is_array($posts));
  }

  public function testHasManyThroughRelation()
  {
    /* @var $book Book */
    $book = Model::factory('Book')->find_one(1);
    $book->authors()->find_many();
    $expected = "SELECT `author`.* FROM `author` JOIN `author_book` ON `author`.`id` = `author_book`.`author_id` WHERE `author_book`.`book_id` = '1'";
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testHasManyThroughRelationWithCustomIntermediateModelAndKeyNames()
  {
    /* @var $book2 Book Two */
    $book2 = Model::factory('BookTwo')->find_one(1);
    $book2->authors()->find_many();
    $expected = "SELECT `author`.* FROM `author` JOIN `author_book` ON `author`.`id` = `author_book`.`custom_author_id` WHERE `author_book`.`custom_book_id` = '1'";
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testHasManyThroughRelationWithCustomIntermediateModelAndKeyNamesAndCustomForeignKeyInBaseAndAssociatedTables()
  {
    /* @var $book3 BookThree */
    $book3 = Model::factory('BookThree')->find_one(1);
    $book3->custom_book_id_in_book_table = 49;
    $book3->authors()->find_many();
    $expected = "SELECT `author`.* FROM `author` JOIN `author_book` ON `author`.`custom_author_id_in_author_table` = `author_book`.`custom_author_id` WHERE `author_book`.`custom_book_id` = '49'";
    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testHasManyThroughRelationWithCustomIntermediateModelAndKeyNamesAndCustomForeignKeyInAssociatedTable()
  {
    /* @var $book4 BookFour */
    $book4 = Model::factory('BookFour')->find_one(1);

    /* @var $authors \Tests3\Author[] */
    $authors = $book4->authors()->find_many();

    self::assertEquals('Fred', $authors[0]->get('name'));

    $expected = "SELECT `author`.* FROM `author` JOIN `author_book` ON `author`.`custom_author_id_in_author_table` = `author_book`.`custom_author_id` WHERE `author_book`.`custom_book_id` = '1'";

    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testHasManyThroughRelationWithCustomIntermediateModelAndKeyNamesAndCustomForeignKeyInBaseTable()
  {
    /* @var $book5 BookFive */
    $book5 = Model::factory('BookFive')->find_one(1);
    $book5->custom_book_id_in_book_table = 49;

    /* @var $authors \Tests3\Author[] */
    $authors = $book5->authors()->find_many();

    self::assertEquals('Fred', $authors[0]->get('name'));

    $expected = "SELECT `author`.* FROM `author` JOIN `author_book` ON `author`.`id` = `author_book`.`custom_author_id` WHERE `author_book`.`custom_book_id` = '49'";

    self::assertEquals($expected, ORM::get_last_query());
  }

  public function testFindResultSet()
  {
    $result_set = Model::factory('BookFive')->find_result_set();
    self::assertInstanceOf('IdiormResultSet', $result_set);
    self::assertSame(count($result_set), 5);
  }

  /**
   * @expectedException paris\orm\ParisMethodMissingException
   */
  public function testInvalidModelFunctionCallDoesNotRecurse()
  {
    $model = new Model();
    /** @noinspection PhpUndefinedMethodInspection */
    $model->noneExistentFunction();
  }

  /**
   * @expectedException IdiormMethodMissingException
   */
  public function testInvalidORMWrapperFunctionCallDoesNotRecurse()
  {
    $ORMWrapper = Model::factory('Simple');
    /** @noinspection PhpUndefinedMethodInspection */
    $ORMWrapper->noneExistentFunction();
  }

  /**
   * Regression tests
   */
  public function testIssue80RecursiveErrorFromInstantiatingModelClass()
  {
    $user = new User();
    self::assertInstanceOf('User', $user);
    self::assertSame($user->orm, null);
  }
}
