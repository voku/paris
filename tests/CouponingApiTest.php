<?php

namespace Paris\Tests;

use idiorm\orm\ORM;
use MockPDO;
use paris\orm\Model;
use PHPUnit_Framework_TestCase;

/**
 * Class CouponingApiTest
 *
 * @package Paris\Tests
 */
class CouponingApiTest extends PHPUnit_Framework_TestCase
{
  public function testInsert()
  {
    $json = '
    {
      "id": 123,
      "fields": {
        "Markt": "FooBar",
        "Datum": "30.03.2016",
        "Uhrzeit": "12:17",
        "Gesamtbetrag": "13.37",
        "Universal": {
          "product1": 1,
          "product2": 4
        },
        "Spezial": {
          "Foo": 3
        },
        "E-Mail-Adresse": "max.mustermann@beispiel.de"
      },
      "valid": true
    }
    ';
    $array = json_decode($json, true);

    // ----------------------------

    /* @var $orm \Paris\Tests\CouponingApi */
    $orm = Model::factory('\Paris\Tests\CouponingApi')->create();

    $orm->id = 666; // if we use a real DB, this will be a "AUTO_INCREMENT"-value
    $orm->setCouponingId($array['id']);
    $orm->setValid($array['valid']);
    $orm->setEmail($array['fields']['E-Mail-Adresse']);
    $orm->setMarket($array['fields']['Markt']);
    $orm->setDate(date('Y-m-d', strtotime($array['fields']['Datum'])) . ' ' . $array['fields']['Uhrzeit'] . ':00');
    $orm->setDateCreated('2016-04-28');
    $orm->setSum($array['fields']['Gesamtbetrag']);

    $orm->save();

    // test
    $lastQuery = ORM::get_last_query();
    $expected = 'INSERT INTO `tracking_couponing` (`id`, `couponing_id`, `valid`, `email`, `market`, `date`, `date_created`, `sum`) VALUES (\'666\', \'123\', \'1\', \'max.mustermann@beispiel.de\', \'FooBar\', \'2016-03-30 12:17:00\', \'2016-04-28\', \'13.37\')';
    self::assertEquals($expected, $lastQuery);

    if (
        isset($array['fields']['Universal'])
        &&
        is_array($array['fields']['Universal'])
    ) {
      foreach ($array['fields']['Universal'] as $product => $count) {
        /* @var $ormTmp \Paris\Tests\CouponingApiSpecialProducts */
        $ormTmp = $orm->couponingApiUniversalProducts()->create();
        $ormTmp->setProduct($product);
        $ormTmp->setCount($count);
        $ormTmp->setCouponingApiIdFk($orm->getId());

        $ormTmp->save();

        // test
        if ($product == 'product1') {
          $lastQuery = ORM::get_last_query();
          $expected = 'INSERT INTO `tracking_couponing_universal_products` (`product`, `count`, `couponing_api_id_fk`) VALUES (\'product1\', \'1\', \'666\')';
          self::assertEquals($expected, $lastQuery);
        } elseif ($product == 'product2') {
          $lastQuery = ORM::get_last_query();
          $expected = 'INSERT INTO `tracking_couponing_universal_products` (`product`, `count`, `couponing_api_id_fk`) VALUES (\'product2\', \'4\', \'666\')';
          self::assertEquals($expected, $lastQuery);
        }

      }
    }

    if (
        isset($array['fields']['Spezial'])
        &&
        is_array($array['fields']['Spezial'])
    ) {
      foreach ($array['fields']['Spezial'] as $product => $count) {
        /* @var $ormTmp \Paris\Tests\CouponingApiSpecialProducts */
        $ormTmp = $orm->couponingApiSpecialProducts()->create();
        $ormTmp->setProduct($product);
        $ormTmp->setCount($count);
        $ormTmp->setCouponingApiIdFk($orm->getId());

        $ormTmp->save();

        $lastQuery = ORM::get_last_query();
        $expected = 'INSERT INTO `tracking_couponing_special_products` (`product`, `count`, `couponing_api_id_fk`) VALUES (\'Foo\', \'3\', \'666\')';
        self::assertEquals($expected, $lastQuery);
      }
    }

  }

  public function setUp()
  {
    // Set up the dummy database connection
    ORM::set_db(new MockPDO('sqlite::memory:'));

    // Enable logging
    ORM::configure('logging', true);

    Model::$auto_prefix_models = null;
  }

  public function tearDown()
  {
    ORM::configure('logging', false);
    ORM::set_db(null);

    Model::$auto_prefix_models = null;
  }
}
