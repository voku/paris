<?php

namespace paris\orm;

/**
 *
 * Paris | http://github.com/j4mie/paris/
 *
 * A simple Active Record implementation built on top of Idiorm
 * ( http://github.com/j4mie/idiorm/ ).
 *
 * BSD Licensed.
 *
 * Copyright (c) 2010, Jamie Matthews
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 */

/**
 * Model base class. Your model objects should extend
 * this class. A minimal subclass would look like:
 *
 * class Widget extends Model { }
 *
 * The methods documented below are magic methods that conform to PSR-1.
 * This documentation exposes these methods to doc generators and IDEs.
 *
 * @see     http://www.php-fig.org/psr/psr-1/
 *
 * @method void setOrm($orm)
 * @method $this setExpr($property, $value = null)
 * @method bool isDirty($property)
 * @method bool isNew()
 * @method Array asArray()
 * @method static ORMWrapper belongsTo($string, $custom_fk = null)
 * @method static ORMWrapper hasOne($string, $custom_fk = null)
 * @method static ORMWrapper hasMany($string, $custom_fk = null)
 * @method static ORMWrapper hasManyThrough($string_1, $custom_1_fk = null, $string_2 = null, $custom_2_fk = null)
 * @method static ORMWrapper find_one($id = null)
 * @method static ORMWrapper find_many()
 *
 * @package paris\orm
 */
class Model
{
  /**
   * Default ID column for all models. Can be overridden by adding
   * a public static _id_column property to your model classes.
   */
  const DEFAULT_ID_COLUMN = 'id';

  /**
   * Default foreign key suffix used by relationship methods
   */
  const DEFAULT_FOREIGN_KEY_SUFFIX = '_id';

  /**
   * Set a prefix for model names. This can be a namespace or any other
   * abitrary prefix such as the PEAR naming convention.
   *
   * @example Model::$auto_prefix_models = 'MyProject_MyModels_'; //PEAR
   * @example Model::$auto_prefix_models = '\MyProject\MyModels\'; //Namespaces
   *
   * @var string $auto_prefix_models
   */
  public static $auto_prefix_models = null;

  /**
   * Set true to to ignore namespace information when computing table names
   * from class names.
   *
   * @example Model::$short_table_names = true;
   * @example Model::$short_table_names = false; // default
   *
   * @var bool $short_table_names
   */
  public static $short_table_names = false;

  /**
   * The ORM instance used by this model
   * instance to communicate with the database.
   *
   * @var \ORM $orm
   */
  public $orm;

  /**
   * Calls static methods directly on the ORMWrapper
   *
   * @param  string $method
   * @param  Array  $parameters
   *
   * @return Array|false
   */
  public static function __callStatic($method, $parameters)
  {
    if (function_exists('get_called_class')) {
      $model = self::factory(get_called_class());

      return call_user_func_array(array($model, $method), $parameters);
    } else {
      return false;
    }
  }

  /**
   * Factory method used to acquire instances of the given class.
   * The class name should be supplied as a string, and the class
   * should already have been loaded by PHP (or a suitable autoloader
   * should exist). This method actually returns a wrapped ORM object
   * which allows a database query to be built. The wrapped ORM object is
   * responsible for returning instances of the correct class when
   * its find_one or find_many methods are called.
   *
   * @param  string      $class_name
   * @param  null|string $connection_name
   *
   * @return ORMWrapper
   */
  public static function factory($class_name, $connection_name = null)
  {
    $class_name = self::$auto_prefix_models . $class_name;
    $table_name = self::_get_table_name($class_name);

    if (!$connection_name) {
      $connection_name = self::_get_class_property(
          $class_name,
          '_connection_name',
          ORMWrapper::DEFAULT_CONNECTION
      );
    }
    $wrapper = ORMWrapper::for_table($table_name, $connection_name);
    $wrapper->set_class_name($class_name);
    $wrapper->use_id_column(self::_get_id_column_name($class_name));

    return $wrapper;
  }

  /**
   * Static method to get a table name given a class name.
   * If the supplied class has a public static property
   * named $_table, the value of this property will be
   * returned.
   *
   * If not, the class name will be converted using
   * the _class_name_to_table_name method method.
   *
   * If Model::$short_table_names == true or public static
   * property $_table_use_short_name == true then $class_name passed
   * to _class_name_to_table_name is stripped of namespace information.
   *
   * @param  string $class_name
   *
   * @return string
   */
  protected static function _get_table_name($class_name)
  {
    $specified_table_name = self::_get_class_property($class_name, '_table');
    $use_short_class_name = self::_use_short_table_name($class_name);

    if ($use_short_class_name) {
      $exploded_class_name = explode('\\', $class_name);
      $class_name = end($exploded_class_name);
    }

    if (is_null($specified_table_name)) {
      return self::_class_name_to_table_name($class_name);
    }

    return $specified_table_name;
  }

  /**
   * Retrieve the value of a static property on a class. If the
   * class or the property does not exist, returns the default
   * value supplied as the third argument (which defaults to null).
   *
   * @param  string      $class_name
   * @param  string      $property
   * @param  null|string $default
   *
   * @return string
   */
  protected static function _get_class_property($class_name, $property, $default = null)
  {
    if (!class_exists($class_name) || !property_exists($class_name, $property)) {
      return $default;
    }

    $properties = get_object_vars(new $class_name);
    if (isset($properties[$property]) && $properties[$property]) {
      return $properties[$property];
    }

    $properties = get_class_vars($class_name);

    return $properties[$property];
  }

  /**
   * Should short table names, disregarding class namespaces, be computed?
   *
   * $class_property overrides $global_option, unless $class_property is null
   *
   * @param string $class_name
   *
   * @return bool
   */
  protected static function _use_short_table_name($class_name)
  {
    $global_option = self::$short_table_names;
    $class_property = self::_get_class_property($class_name, '_table_use_short_name');

    return is_null($class_property) ? $global_option : $class_property;
  }

  /**
   * Convert a namespace to the standard PEAR underscore format.
   *
   * Then convert a class name in CapWords to a table name in
   * lowercase_with_underscores.
   *
   * Finally strip doubled up underscores
   *
   * For example, CarTyre would be converted to car_tyre. And
   * Project\Models\CarTyre would be project_models_car_tyre.
   *
   * @param  string $class_name
   *
   * @return string
   */
  protected static function _class_name_to_table_name($class_name)
  {
    return strtolower(
        preg_replace(
            array('/\\\\/', '/(?<=[a-z])([A-Z])/', '/__/'),
            array('_', '_$1', '_'),
            ltrim($class_name, '\\')
        )
    );
  }

  /**
   * Return the ID column name to use for this class. If it is
   * not set on the class, returns null.
   *
   * @param  string $class_name
   *
   * @return string|null
   */
  protected static function _get_id_column_name($class_name)
  {
    return self::_get_class_property($class_name, '_id_column', self::DEFAULT_ID_COLUMN);
  }

  /**
   * Set the wrapped ORM instance associated with this Model instance.
   *
   * @param \ORM $orm
   *
   * @return void
   */
  public function set_orm($orm)
  {
    $this->orm = $orm;
  }

  /**
   * Magic getter method, allows $model->property access to data.
   *
   * @param  string $property
   *
   * @return null|string
   */
  public function __get($property)
  {
    return $this->orm->get($property);
  }

  /**
   * Magic setter method, allows $model->property = 'value' access to data.
   *
   * @param  string $property
   * @param  string $value
   *
   * @return void
   */
  public function __set($property, $value)
  {
    $this->orm->set($property, $value);
  }

  /**
   * Magic isset method, allows isset($model->property) to work correctly.
   *
   * @param  string $property
   *
   * @return bool
   */
  public function __isset($property)
  {
    return $this->orm->__isset($property);
  }

  /**
   * Magic unset method, allows unset($model->property)
   *
   * @param  string $property
   *
   * @return void
   */
  public function __unset($property)
  {
    $this->orm->__unset($property);
  }

  /**
   * Getter method, allows $model->get('property') access to data
   *
   * @param  string $property
   *
   * @return string
   */
  public function get($property)
  {
    return $this->orm->get($property);
  }

  /**
   * Setter method, allows $model->set('property', 'value') access to data.
   *
   * @param  string|array $property
   * @param  string|null  $value
   *
   * @return Model
   */
  public function set($property, $value = null)
  {
    $this->orm->set($property, $value);

    return $this;
  }

  /**
   * Setter method, allows $model->set_expr('property', 'value') access to data.
   *
   * @param  string|array $property
   * @param  string|null  $value
   *
   * @return Model
   */
  public function set_expr($property, $value = null)
  {
    $this->orm->set_expr($property, $value);

    return $this;
  }

  /**
   * Check whether the given field has changed since the object was created or saved
   *
   * @param  string $property
   *
   * @return bool
   */
  public function is_dirty($property)
  {
    return $this->orm->is_dirty($property);
  }

  /**
   * Check whether the model was the result of a call to create() or not
   *
   * @return bool
   */
  public function is_new()
  {
    return $this->orm->is_new();
  }

  /**
   * Wrapper for Idiorm's as_array method.
   *
   * @return Array
   */
  public function as_array()
  {
    $args = func_get_args();

    return call_user_func_array(array($this->orm, 'as_array'), $args);
  }

  /**
   * Save the data associated with this model instance to the database.
   *
   * @return null
   */
  public function save()
  {
    return $this->orm->save();
  }

  /**
   * Delete the database row associated with this model instance.
   *
   * @return null
   */
  public function delete()
  {
    return $this->orm->delete();
  }

  /**
   * Hydrate this model instance with an associative array of data.
   * WARNING: The keys in the array MUST match with columns in the
   * corresponding database table. If any keys are supplied which
   * do not match up with columns, the database will throw an error.
   *
   * @param  Array $data
   *
   * @return void
   */
  public function hydrate($data)
  {
    $this->orm->hydrate($data)->force_all_dirty();
  }

  /**
   * Magic method to capture calls to undefined class methods.
   * In this case we are attempting to convert camel case formatted
   * methods into underscore formatted methods.
   *
   * This allows us to call methods using camel case and remain
   * backwards compatible.
   *
   * @param  string $name
   * @param  array  $arguments
   *
   * @throws ParisMethodMissingException
   * @return bool|ORMWrapper
   */
  public function __call($name, $arguments)
  {
    $method = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
    if (method_exists($this, $method)) {
      return call_user_func_array(array($this, $method), $arguments);
    } else {
      throw new ParisMethodMissingException("Method $name() does not exist in class " . get_class($this));
    }
  }

  /**
   * Helper method to manage one-to-one relations where the foreign
   * key is on the associated table.
   *
   * @param  string      $associated_class_name
   * @param  null|string $foreign_key_name
   * @param  null|string $foreign_key_name_in_current_models_table
   * @param  null|string $connection_name
   *
   * @return ORMWrapper
   */
  protected function has_one($associated_class_name, $foreign_key_name = null, $foreign_key_name_in_current_models_table = null, $connection_name = null)
  {
    return $this->_has_one_or_many($associated_class_name, $foreign_key_name, $foreign_key_name_in_current_models_table, $connection_name);
  }

  /**
   * Internal method to construct the queries for both the has_one and
   * has_many methods. These two types of association are identical; the
   * only difference is whether find_one or find_many is used to complete
   * the method chain.
   *
   * @param  string      $associated_class_name
   * @param  null|string $foreign_key_name
   * @param  null|string $foreign_key_name_in_current_models_table
   * @param  null|string $connection_name
   *
   * @return ORMWrapper
   */
  protected function _has_one_or_many($associated_class_name, $foreign_key_name = null, $foreign_key_name_in_current_models_table = null, $connection_name = null)
  {
    $base_table_name = self::_get_table_name(get_class($this));
    $foreign_key_name = self::_build_foreign_key_name($foreign_key_name, $base_table_name);

    // Value of foreign_table.{$foreign_key_name} we're
    // looking for. Where foreign_table is the actual
    // database table in the associated model.

    if (is_null($foreign_key_name_in_current_models_table)) {
      // Match foreign_table.{$foreign_key_name} with the value of
      // {$this->_table}.{$this->id()}
      $where_value = $this->id();
    } else {
      // Match foreign_table.{$foreign_key_name} with the value of
      // {$this->_table}.{$foreign_key_name_in_current_models_table}
      $where_value = $this->$foreign_key_name_in_current_models_table;
    }

    return self::factory($associated_class_name, $connection_name)->where($foreign_key_name, $where_value);
  }

  /**
   * Build a foreign key based on a table name. If the first argument
   * (the specified foreign key column name) is null, returns the second
   * argument (the name of the table) with the default foreign key column
   * suffix appended.
   *
   * @param  string $specified_foreign_key_name
   * @param  string $table_name
   *
   * @return string
   */
  protected static function _build_foreign_key_name($specified_foreign_key_name, $table_name)
  {
    if (!is_null($specified_foreign_key_name)) {
      return $specified_foreign_key_name;
    }

    return $table_name . self::DEFAULT_FOREIGN_KEY_SUFFIX;
  }

  /**
   * Get the database ID of this model instance.
   *
   * @return integer
   */
  public function id()
  {
    return $this->orm->id();
  }

  /**
   * Helper method to manage one-to-many relations where the foreign
   * key is on the associated table.
   *
   * @param  string      $associated_class_name
   * @param  null|string $foreign_key_name
   * @param  null|string $foreign_key_name_in_current_models_table
   * @param  null|string $connection_name
   *
   * @return ORMWrapper
   */
  protected function has_many($associated_class_name, $foreign_key_name = null, $foreign_key_name_in_current_models_table = null, $connection_name = null)
  {
    return $this->_has_one_or_many($associated_class_name, $foreign_key_name, $foreign_key_name_in_current_models_table, $connection_name);
  }

  /**
   * Helper method to manage one-to-one and one-to-many relations where
   * the foreign key is on the base table.
   *
   * @param  string      $associated_class_name
   * @param  null|string $foreign_key_name
   * @param  null|string $foreign_key_name_in_associated_models_table
   * @param  null|string $connection_name
   *
   * @return self|null
   */
  protected function belongs_to($associated_class_name, $foreign_key_name = null, $foreign_key_name_in_associated_models_table = null, $connection_name = null)
  {
    $associated_table_name = self::_get_table_name(self::$auto_prefix_models . $associated_class_name);
    $foreign_key_name = self::_build_foreign_key_name($foreign_key_name, $associated_table_name);
    $associated_object_id = $this->$foreign_key_name;

    $desired_record = null;

    if (is_null($foreign_key_name_in_associated_models_table)) {
      // "{$associated_table_name}.primary_key = {$associated_object_id}"
      // NOTE: primary_key is a placeholder for the actual primary key column's name
      // in $associated_table_name
      $desired_record = self::factory($associated_class_name, $connection_name)->where_id_is($associated_object_id);
    } else {
      // "{$associated_table_name}.{$foreign_key_name_in_associated_models_table} = {$associated_object_id}"
      $desired_record = self::factory($associated_class_name, $connection_name)->where($foreign_key_name_in_associated_models_table, $associated_object_id);
    }

    return $desired_record;
  }

  /**
   * Helper method to manage many-to-many relationships via an intermediate model. See
   * README for a full explanation of the parameters.
   *
   * @param  string      $associated_class_name
   * @param  null|string $join_class_name
   * @param  null|string $key_to_base_table
   * @param  null|string $key_to_associated_table
   * @param  null|string $key_in_base_table
   * @param  null|string $key_in_associated_table
   * @param  null|string $connection_name
   *
   * @return ORMWrapper
   */
  protected function has_many_through($associated_class_name, $join_class_name = null, $key_to_base_table = null, $key_to_associated_table = null, $key_in_base_table = null, $key_in_associated_table = null, $connection_name = null)
  {
    $base_class_name = get_class($this);

    // The class name of the join model, if not supplied, is
    // formed by concatenating the names of the base class
    // and the associated class, in alphabetical order.
    if (is_null($join_class_name)) {
      $base_model = explode('\\', $base_class_name);

      $base_model_name = end($base_model);
      if (substr($base_model_name, 0, strlen(self::$auto_prefix_models)) == self::$auto_prefix_models) {
        $base_model_name = substr($base_model_name, strlen(self::$auto_prefix_models), strlen($base_model_name));
      }

      // Paris wasn't checking the name settings for the associated class.
      $associated_model = explode('\\', $associated_class_name);
      $associated_model_name = end($associated_model);
      if (substr($associated_model_name, 0, strlen(self::$auto_prefix_models)) == self::$auto_prefix_models) {
        $associated_model_name = substr($associated_model_name, strlen(self::$auto_prefix_models), strlen($associated_model_name));
      }

      $class_names = array($base_model_name, $associated_model_name);

      sort($class_names, SORT_STRING);
      $join_class_name = join("", $class_names);
    }

    // get table names for each class
    $base_table_name = self::_get_table_name($base_class_name);
    $associated_table_name = self::_get_table_name(self::$auto_prefix_models . $associated_class_name);
    $join_table_name = self::_get_table_name(self::$auto_prefix_models . $join_class_name);

    // get ID column names
    $base_table_id_column = (is_null($key_in_base_table)) ?
        self::_get_id_column_name($base_class_name) :
        $key_in_base_table;
    $associated_table_id_column = (is_null($key_in_associated_table)) ?
        self::_get_id_column_name(self::$auto_prefix_models . $associated_class_name) :
        $key_in_associated_table;

    // get the column names for each side of the join table
    $key_to_base_table = self::_build_foreign_key_name($key_to_base_table, $base_table_name);
    $key_to_associated_table = self::_build_foreign_key_name($key_to_associated_table, $associated_table_name);

    return self::factory($associated_class_name, $connection_name)
               ->select("{$associated_table_name}.*")
               ->join(
                   $join_table_name, array(
                                       "{$associated_table_name}.{$associated_table_id_column}",
                                       '=',
                                       "{$join_table_name}.{$key_to_associated_table}",
                                   )
               )
               ->where("{$join_table_name}.{$key_to_base_table}", $this->$base_table_id_column);
  }
}
