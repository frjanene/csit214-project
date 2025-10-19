<?php
require_once __DIR__ . '/../../config/database.php';

abstract class Model {
  protected static function db(): PDO {
    return db(); // provided by config/database.php
  }
}
