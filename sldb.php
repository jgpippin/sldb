<?php


class sldbRequest {

  private $connection;

  var $output;
  var $result;
  var $table;

  var $uuid;

  function __construct($db_host, $db_user, $db_pass, $db_name, $table) {
    require_once('config.php');
    $this->connection = mysqli_connect($db_host, $db_user, $db_pass) or die ('ERROR: CANNOT CONNECT TO DATABASE.');
    mysqli_select_db($this->connection, $db_name) or die('ERROR: CANNOT SELECT DATABASE.');
    $this->table = $table;
  }


  /**
   * Getter function for the output of the query.
   * @param  string $separator
   *   (optional) The separator to use. LSL cannot handle arrays or objects
   *   because it was slapped together and never fixed, so the output has to be
   *   a list with a constant separator that can be used to parse it.
   */
  function getOutput($verbose = FALSE) {
    $output = json_encode($this->output);
    return $output;
  }


  /**
   * Create a table for use.
   */
  function createTable() {
    $sql = "CREATE TABLE IF NOT EXISTS `" . $this->table . "` (`uuid` varchar(32) NOT NULL DEFAULT '', `field` varchar(255) NOT NULL DEFAULT '', `value` longtext, `changed` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`uuid`,`field`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    $this->result = mysqli_query($this->connection, $sql) or die(mysqli_error());
    $this->output = "SUCCESS";
  }


  /**
   * Update uuid/field data pairs.
   *
   * @param  string  $uuid
   *   The user's uuid
   * @param  array  $data
   *   An array of data to update, where the keys are the fields and their
   *   values are the values stored in the db.
   * @param  boolean $verbose
   *   TRUE for longer output.
   */
  function updateData($uuid, $data) {
    foreach($data as $key => $value) {
      $value = addslashes($value);
      $sql = "INSERT INTO " . $this->table . " (uuid, field, value, changed) VALUES ('$uuid', '$key', '$value', UNIX_TIMESTAMP(NOW())) ON DUPLICATE KEY UPDATE value = '$value', changed = UNIX_TIMESTAMP(NOW())";
      $this->result = mysqli_query($this->connection, $sql) or die(mysqli_error($this->connection));
      $this->output = array(
        'uuid' => $uuid,
        'fields' => array_keys($data),
        'count' => mysqli_affected_rows($this->connection),
      );
    }
  }


  /**
   * Read data for a uuid or uuid/field combination.
   *
   * @param  string  $uuid
   *   The uuid for the user. The uuid can also be any unique string.
   * @param  array  $fields
   *   (optional) The fields element provided with the request. Contains a list
   *   of fields. If not provided, all field/value combinations will be returned.
   * @param  boolean $verbose
   *   (optional) TRUE to return field/value pairs, FALSE for just the value.
   * @param  string  $separator
   *   (optional) A glue string to implode the results. Default is '='.
   */
  function readData($uuid, $fields = array()) {
    $fields = (array)$fields;
    foreach($fields AS $key => $field) {
      $fields[$key] = "'" . $field . "'";
    }

    $sql = "SELECT field, value FROM " . $this->table . " WHERE uuid = '$uuid'";
    $sql .= empty($fields) ? '' : " AND field IN (" . implode(', ', (array)$fields) . ")";

    $this->result = mysqli_query($this->connection, $sql) or die(mysqli_error());
    $this->output = array(
      'uuid' => $uuid,
      'fields' => $fields,
      'count' => mysqli_affected_rows($this->connection),
    );

    while($record = mysqli_fetch_assoc($this->result)) {
      $this->output['data'][$record['field']] = empty($record['value']) ? NULL : $record['value'];
    }
  }


  /**
   * Delete data for a uuid or a uuid/field combination.
   *
   * @param  string  $uuid
   *   The uuid for the user. The uuid can also be any unique string.
   * @param  array   $fields
   *   (optional) An array of fields to delete. If no fields are provided, all
   *   fields for that user will be deleted.
   * @param  boolean $verbose
   *   (optional) TRUE for longer output.
   */
  function deleteData($uuid, $fields = array()) {
    $fields = (array)$fields;
    foreach($fields AS $key => $field) {
      $fields[$key] = "'" . $field . "'";
    }

    $sql = "DELETE FROM " . $this->table . " WHERE uuid = '$uuid'";
    $sql .= empty($fields) ? '' : " AND field IN (" . implode(', ', (array)$fields) . ")";

    $this->result = mysqli_query($this->connection, $sql) or die(mysqli_error());
    $this->output = array(
      'uuid' => $uuid,
      'fields' => $fields,
      'count' => mysqli_affected_rows($this->connection),
    );
  }
}