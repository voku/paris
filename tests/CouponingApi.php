<?php

namespace Paris\Tests;

use paris\orm\Model;
use paris\orm\ORMWrapper;

/**
 * CouponingApi
 *
 * @property int    $id
 * @property int    $couponing_id
 * @property string $date
 * @property string $date_created
 * @property int    $valid
 * @property string $email
 * @property string $sum
 * @property string $market
 */
class CouponingApi extends Model
{
  /**
   * @var bool
   */
  public static $_table_use_short_name = true;

  /**
   * @var string
   */
  public static $_table = 'tracking_couponing'; // this will be overwritten by the constructor

  /**
   * __construct
   */
  public function __construct()
  {
    static::$_table = 'tracking_couponing';
  }

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
    return $this->has_many('\Paris\Tests\CouponingApiSpecialProducts', 'couponing_api_id_fk', 'id');
  }

  /**
   * @return \idiorm\orm\ORM
   */
  public function couponingApiUniversalProducts()
  {
    return $this->has_many('\Paris\Tests\CouponingApiUniversalProducts', 'couponing_api_id_fk', 'id');
  }
}
