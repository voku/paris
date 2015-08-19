<?php

/**
 * A different mock database class, for testing multiple connections
 * Mock database class implementing a subset of the PDO API.
 */
class MockDifferentPDO extends MockPDO
{

  /**
   * Return a dummy PDO statement
   *
   * @param string $statement
   * @param array  $driver_options
   *
   * @return MockDifferentPDOStatement
   */
  public function prepare($statement, $driver_options = array())
  {
    $this->last_query = new MockDifferentPDOStatement($statement);

    return $this->last_query;
  }
}
