<?php

namespace Paris\Tests;

use paris\orm\Model;

/**
 * CouponingNewApiSpecialProducts.php
 *
 * @property-read int    $id
 * @property-read int    $couponing_api_id_fk
 * @property-read string $product
 * @property-read int    $count
 */
class CouponingNewApiSpecialProducts extends Model
{
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
    return $this->has_one('\Paris\Tests\CouponingNewApi');
  }

}
