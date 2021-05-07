<?php

/**
 * MySQL Database Connection Class
 * @access public
 */
class MySQL {

  /**
   * MySQL server hostname
   * @access private
   * @var string
   */
  var $host;

  /**
   * MySQL username
   * @access private
   * @var string
   */
  var $dbUser;

  /**
   * MySQL user's password
   * @access private
   * @var string
   */
  var $dbPass;

  /**
   * Name of database to use
   * @access private
   * @var string
   */
  var $dbName;

  /**
   * MySQL Resource link identifier stored here
   * @access private
   * @var string
   */
  var $dbConn;

  /**
   * Stores error messages for connection errors
   * @access private
   * @var string
   */
  var $connectError;

  /**
   * Stores the error messages
   * @access private
   * @var string
   */
  var $errorMsg;

  /**
   * MySQL constructor
   *
   * @param string host (MySQL server hostname)
   * @param string dbUser (MySQL User Name)
   * @param string dbPass (MySQL User Password)
   * @param string dbName (Database to select)
   * @access public
   */
  function MySQL($host, $dbUser, $dbPass, $dbName) {
    $this->host = $host;
    $this->dbUser = $dbUser;
    $this->dbPass = $dbPass;
    $this->dbName = $dbName;
    $this->connect();
  }

  /**
   * Establishes connection to MySQL and selects a database
   *
   * @return void
   * @access private
   */
  function connect() {
    if (!$this->dbConn = @mysqli_connect($this->host, $this->dbUser, $this->dbPass)) {
      $this->errorMsg = 'Could not connect to server';
      $this->connectError = true;
      // Select database
    } else if (!@mysqli_select_db($this->dbConn, $this->dbName)) {
      $this->errorMsg = 'Could not select database';
      $this->connectError = true;
    }
    @mysqli_set_charset($this->dbConn, "utf8");
  }

  /**
   * Checks for MySQL errors
   *
   * @return boolean
   * @access public
   */
  function isError() {
    if ($this->connectError) {
      return true;
    }
    $this->errorMsg = mysqli_error($this->dbConn);

    if (empty($this->errorMsg)) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Returns the error message if there is one set
   *
   * @return string
   * @access public
   */
  function getErrorMsg() {
    return '<p style="color:red">' . $this->errorMsg . '</p>';
  }

  /**
   * Returns an instance of MySQLResult to fetch rows with
   *
   * @param $sql string (the database query to run)
   * @return MySQLResult
   * @access public
   */
  function query($sql) {
    if (!$this->query = @mysqli_query($this->dbConn, $sql)) {
      $this->errorMsg = 'Query failed: ' . mysqli_error($this->dbConn) . ' SQL: ' . $sql;
    }
    return $this->query;
  }

  /*
   * MySQLResult(& $mysql, $query)
   * 
   */
  function MySQLResult(& $mysql, $query) {
    $this->mysql = & $mysql;
    $this->query = $query;
  }

  /*
   * fetchRow($query)
   * 
   */
  function fetchRow($query) {
    if ($row = @mysqli_fetch_array($query, MYSQL_ASSOC)) {
      return $row;
    } else if ($this->numRows($query) > 0) {
      @mysqli_data_seek($query, 0);
      return false;
    } else {
      return false;
    }
  }

  /**
   * Returns an result from the database
   *
   * @param $sql string (the database query to run)
   * @return MySQLResult
   * @access public
   */
  function select($sql) {
    if (!$query = @mysqli_query($this->dbConn, $sql)) {
      $this->errorMsg = 'Query failed: ' . mysqli_error($this->dbConn) . ' SQL: ' . $sql;
      return false;
    }
    if (@mysqli_num_rows($query) > 0) {
      $resultado = array();
      while ($row = @mysqli_fetch_array($query, MYSQL_ASSOC)) {
        array_push($resultado, $row);
      }
    } else {
      return false;
    }
    return $resultado;
  }

  /**
   * Returns the number of rows selected
   *
   * @return int
   * @access public
   */
  function numRows($query) {
    return @mysqli_num_rows($query);
  }

  /**
   * Returns the number of fields selected
   *
   * @return int
   * @access public
   */
  function numFields($query) {
    return @mysqli_num_fields($query);
  }

  /**
   * Fetches a row from the result
   *
   * @return array
   * @access public
   */
  function fetchField($query, $i) {
    /*
    if (mysqli_field_flags($query, $i) == "primary_key") {
      $row = @mysqli_field_name($query, $i);
      $rs = array($row, 1);
      return $rs;
    }*/
    /*
    if ($row = @mysqli_field_name($query, $i)) {
      $rs = array($row, 0);
      return $rs;
    } else if ($this->numFields($query) > 0) {
      @mysqli_data_seek($query, 0);
      return false;
    } else {
      return false;
    }*/
  }

  /**
   * Returns the ID of the last row inserted
   *
   * @return int
   * @access public
   */
  function insertID() {
    return @mysqli_insert_id($this->dbConn);
  }

  /*
   * insert($strSQL, $getLastID = true, &$error_sql = '')
   * 
   * Funcion para realizar consultas insert, si la consulta pasada como parametro no es
   * una consulta insert se termina la ejecucion y se informa el error.
   * Si la consulta se realiza con exito y el parametro $getLastId = false, se retorna
   * true en caso de haber podido realizar la insersion y false en caso contrario.
   * Si la consulta se realiza con exito y el parametro $getLastId = true, se retorna el
   * id de la ultima insersion.
   * 
   */
  function insert($strSQL, $getLastID = true, &$error_sql = '') {
    if (strtolower(substr(trim($strSQL), 0, 6)) != "insert")
      die("An error has happened in the insert function in MySQL class. "
              . "A INSERT query was expected <br/> The query was $strSQL");
    $this->connect();
    $resource = mysqli_query($this->dbConn, $strSQL);
    if (!$resource) {
      $mysql_error = mysqli_error($this->dbConn);
      $error_sql = $mysql_error;  // Esto es para devolver el mensaje de error
      //$error_sql = mysql_errno(); // devuelve el codigo de error dado por el motor mysql
      //dump("entro a false del insert!!!!!");
      /*
        die ("An error has happened in the insert function in MySQL class. "
        ."$mysql_error <br/> The query was $strSQL" );
       */
      return false;
    }
    $this->rows = NULL;
    $this->lastOperation = 'insert';
    $this->numOfRows = 0;
    $this->actualRow = 0;

    if ($getLastID == false) {
      return true;
    } else {
      $lastID = mysqli_insert_id($this->dbConn);
      return $lastID;
    }
  }

  /*
   * begin()
   * 
   */
  function begin() {
    return @mysqli_query($this->dbConn, "BEGIN");
  }

  /*
   * rollback()
   * 
   */
  function rollback() {
    return @mysqli_query($this->dbConn, "ROLLBACK");
  }

  /*
   * commit()
   * 
   */
  function commit() {
    return @mysqli_query($this->dbConn, "COMMIT");
  }

  /*
   * update($strSQL, &$error_sql = '')
   * 
   * Función para realizar consultas update, si la consulta pasada como parámetro no es
   * una consulta update se termina la ejecución y se informa el error.
   * Si la consulta se realiza con éxito esta función retorna el número de filas afectadas
   * por esta actualización (solo el número de filas realmente actualizadas, y no aquellas que
   * cumplan con la clausula WHERE y posean el mismo valor que el valor a actualizar)
   * 
   */
  function update($strSQL, &$error_sql = '') {
    if (strtolower(substr(trim($strSQL), 0, 6)) != "update")
      die("An error has happened in the update function in MySQL class. "
              . "A UPDATE query was expected <br/> The query was " . $strSQL);
    $this->connect();
    $resource = @mysqli_query($this->dbConn, $strSQL);
    if (!$resource) {
      $mysql_error = mysqli_error($this->dbConn);
      $error_sql = $mysql_error;  // Esto es para devolver el mensaje de error
      //$error_sql = mysql_errno(); // devuelve el codigo de error dado por el motor mysql
      return false;
      //$this->close();
      //die ("An error has happened in the update function in MySQL class. "
      //   ."$mysql_error <br/> The query was $strSQL" );
    }
    $affectedRows = mysqli_affected_rows($this->dbConn);
    $this->rows = NULL;
    $this->lastOperation = 'update';
    $this->numOfRows = 0;
    $this->actualRow = 0;
    return $affectedRows;
  }

  /*
   * delete($strSQL, &$error_sql = '')
   * 
   * Funcion para realizar consultas delete, si la consulta pasada como parametro no es
   * una consulta delete se termina la ejecucion y se informa el error.
   * Si la consulta se realiza con exito esta funcion retorna el numero de filas eliminadas
   * 
   */
  function delete($strSQL, &$error_sql = '') {
    if (strtolower(substr(trim($strSQL), 0, 6)) != "delete")
      die("An error has happened in the delete function in MySQL class. "
              . "A DELETE query was expected <br/> The query was " . $strSQL);
    $this->connect();
    $resource = @mysqli_query($this->dbConn, $strSQL);
    if (!$resource) {
      $mysql_error = mysqli_error($this->dbConn);
      $error_sql = $mysql_error;  // Esto es para devolver el mensaje de error
      //$error_sql = mysql_errno(); // devuelve el codigo de error dado por el motor mysql
      return false;
      //die ("An error has happened in the delete function in MySQL class. "
      //   ."$mysql_error <br/> The query was $strSQL" );
    }
    $affectedRows = mysqli_affected_rows($this->dbConn);
    $this->rows = NULL;
    $this->lastOperation = 'delete';
    $this->numOfRows = 0;
    $this->actualRow = 0;
    return $affectedRows;
  }

}

?>