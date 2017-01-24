<?php

namespace Paris\Tests;

use paris\orm\Model;

/**
 * CouponingNewApi
 *
 * @property-read int    $id
 * @property-read int    $couponing_id
 * @property-read string $date
 * @property-read string $date_created
 * @property-read int    $valid
 * @property-read string $email
 * @property-read string $sum
 * @property-read string $market
 */
class CouponingNewApi extends Model
{
  /**
   * @return int
   */
  public function getId()
  {
    return (int)$this->id;
  }

  /**
   * @return int
   */
  public function getCouponingId()
  {
    return $this->couponing_id;
  }

  /**
   * @param int $couponing_id
   */
  public function setCouponingId($couponing_id)
  {
    $this->couponing_id = $couponing_id;
  }
  
  /**
   * @return string
   */
  public function getDate()
  {
    return $this->date;
  }

  /**
   * @param string $date
   */
  public function setDate($date)
  {
    $this->date = $date;
  }

  /**
   * @return string
   */
  public function getDateCreated()
  {
    return $this->date_created;
  }

  /**
   * @param string $date_created
   */
  public function setDateCreated($date_created)
  {
    $this->date_created = $date_created;
  }

  /**
   * @return int
   */
  public function getValid()
  {
    return $this->valid;
  }

  /**
   * @param int $valid
   */
  public function setValid($valid)
  {
    $this->valid = $valid;
  }

  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }

  /**
   * @return string
   */
  public function getSum()
  {
    return $this->sum;
  }

  /**
   * @param string $sum
   */
  public function setSum($sum)
  {
    $this->sum = $sum;
  }

  /**
   * @return string
   */
  public function getMarket()
  {
    return $this->market;
  }

  /**
   * @param string $market
   */
  public function setMarket($market)
  {
    $this->market = $market;
  }

  /**
   * @return \idiorm\orm\ORM
   */
  public function couponingApiSpecialProducts()
  {
    return $this->has_many('\Paris\Tests\CouponingNewApiSpecialProducts');
  }

  /**
   * @return \idiorm\orm\ORM
   */
  public function couponingApiUniversalProducts()
  {
    return $this->has_many('\Paris\Tests\CouponingNewApiUniversalProducts');
  }
}
