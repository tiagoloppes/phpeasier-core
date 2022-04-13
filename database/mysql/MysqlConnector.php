<?php
class MysqlConnector {
	private $connection;
	private $result;
	private $autocommit = true;
	private $transactionStarted = false;
	// METODO CONSTRUTOR COM CONEXAO PADRAO
	function __construct($servidor = DB_SERVER, $usuario = DB_USER, $senha = DB_PASS, $banco = DB_NAME) {
		if ($senha == "")
			$msgSenha = 'sem senha';
		else
			$msgSenha = 'utilizando senha';
		$this->connection = mysqli_connect ( $servidor, $usuario, $senha ) or die ( "Impossível estabelecer conexão com o servidor {$servidor}. Usu�rio: {$usuario}, {$msgSenha}." );
		
		// Configurando o charset
		mysql_query ( "SET NAMES 'utf8'", $this->connection );
		mysql_query ( 'SET character_set_connection=utf8_unicode_ci', $this->connection );
		mysql_query ( 'SET character_set_client=utf8', $this->connection );
		mysql_query ( 'SET character_set_results=utf8', $this->connection );
		mysql_query ( "SET time_zone='" . TIME_ZONE . "'", $this->connection );
		mysql_query ( "SET autocommit=1;", $this->connection );
		
		return true;
	}
	public function startTransaction() {
		if ($this->autocommit == true) {
			mysql_query ( "SET autocommit=0;", $this->connection );
			$this->autocommit = false;
		}
		if ($this->transactionStarted == false) {
			mysql_query ( "START TRANSACTION;", $this->connection );
			$this->transactionStarted = true;
		}
	}
	public function autocommit() {
		if ($this->autocommit == false) {
			mysql_query ( "SET autocommit=1;", $this->connection );
			$this->autocommit = true;
		}
	}
	public function commit() {
		if ($this->transactionStarted == true) {
			mysql_query ( "COMMIT;", $this->connection );
			$this->transactionStarted = false;
		}
	}
	public function rollback() {
		if ($this->transactionStarted == true) {
			mysql_query ( "ROLLBACK;", $this->connection );
			$this->transactionStarted = false;
		}
	}
	// EXECUTA UMA QUERY
	public function query($sql) {
		if ($this->connection != NULL) {
			return mysql_query ( $sql, $this->connection );
		}
	}
	public function fetchAll() {
		if (! empty ( $this->result )) {
			$results = array ();
			while ( $item = mysql_fetch_object ( $this->result ) ) {
				$results [] = $item;
			}
			return $results;
		} else
			return false;
	}
	public function getSingleResult() {
		if (! empty ( $this->result )) {
			$results = array ();
			return mysql_fetch_object ( $this->result );
		} else
			return false;
	}
	
	// RETORNA O NUMERO DE REGISTROS DE UMA CONSULTA
	function getNumRows() {
		if (! empty ( $this->result )) {
			return mysql_num_rows ( $this->result );
		} else
			return false;
	}
	
	// RETORNA A QUANTIDADE DE LINHAS AFETADAS
	function getUpdatedRows() {
		if (! empty ( $this->connection )) {
			return mysql_affected_rows ( $this->connection );
		} else
			return false;
	}
	
	// RETORNA O ID DA ULTIMA INSERÇÃO
	function getLastId() {
		return mysql_insert_id ( $this->connection );
	}
	public function __destruct() {
		if ($this->transactionStarted == true && $this->autocommit == false) {
			$this->rollback ();
		}
		mysql_close ( $this->connection );
	}
}