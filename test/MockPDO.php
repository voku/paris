<?php

/**
 *
 * Mock database class implementing a subset
 * of the PDO API.
 *
 */
class MockPDO extends PDO
{

  /**
   * @var string
   */
  public $last_query = '';

  /**
   * Return a dummy PDO statement
   *
   * @param string $statement
   * @param array  $driver_options
   *
   * @return MockPDOStatement
   */
  public function prepare($statement, $driver_options = array())
  {
    $this->last_query = new MockPDOStatement($statement);

    return $this->last_query;
  }
}