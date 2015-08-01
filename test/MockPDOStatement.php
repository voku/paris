<?php

/**
 *
 * Mock version of the PDOStatement class.
 *
 */
class MockPDOStatement extends PDOStatement
{

  private $current_row = 0;

  /**
   * __construct
   */
  public function __construct()
  {
  }

  /**
   * @param array $params
   *
   * @return null
   */
  public function execute($params = array())
  {
    return null;
  }

  /**
   * Return some dummy data
   *
   * @param int|null $fetch_style
   * @param int      $cursor_orientation
   * @param int      $cursor_offset
   *
   * @return array|bool
   */
  public function fetch($fetch_style = PDO::FETCH_BOTH, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
  {
    if ($this->current_row == 5) {
      return false;
    } else {
      return array('name' => 'Fred', 'age' => 10, 'id' => ++$this->current_row);
    }
  }
}