<?php

namespace Paris\Tests;

use paris\orm\Model;

/**
 * CouponingApiUniversalProducts
 *
 * @property int    $id
 * @property int    $couponing_api_id_fk
 * @property string $product
 * @property int    $count
 */
class CouponingApiUniversalProducts extends Model
{
  /**
   * @var bool
   */
  public static $_table_use_short_name = true;

  /**
   * @var string
   */
  public static $_table = 'tracking_couponing_universal_products'; // this will be overwritten by the constructor

  /**
   * __construct
   */
  public function __construct()
  {
    static::$_table = 'tracking_couponing_universal_products';
  }

  /**
   * @return int
   */
  public function getId()
  {
    return (int)$this->id;
  }

  /**
   * @return string
   */
  public function getProduct()
  {
    return $this->product;
  }

  /**
   * @param string $product
   */
  public function setProduct($product)
  {
    $this->product = $product;
  }

  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }

  /**
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }

  /**
   * @return int
   */
  public function getCouponingApiIdFk()
  {
    return $this->couponing_api_id_fk;
  }

  /**
   * @param int $id
   */
  public function setCouponingApiIdFk($id)
  {
    $this->couponing_api_id_fk = (int)$id;
  }

  /**
   * @return \idiorm\orm\ORM
   */
  public function couponingApi()
  {
    return $this->has_one('\Paris\Tests\CouponingApi', 'couponing_api_id_fk', 'id');
  }

}
