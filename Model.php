<?php
class Model {
	protected $name;
	private $tableName;
	protected $db;
	protected $idColumn = 'id';
	protected $fk;
	public function __construct($dataSourceName = 'DEFAULT') {
		$this->db = DBConnector::getInstance ( $dataSourceName );
		$this->tableName = isset ( $this->name ) ? $this->name : strtolower ( preg_replace ( '/^(.+)Model$/', '$1', get_called_class () ) );
	}
	public function __destruct() {
		if ($this->db->inTransaction ()) {
			$this->db->commit ();
		}
	}
	public function beginTransaction() {
		$this->db->beginTransaction ();
	}
	public function commit() {
		if ($this->db->inTransaction ()) {
			$this->db->commit ();
		}
	}
	public function rollback() {
		if ($this->db->inTransaction ()) {
			$this->db->rollBack ();
		}
	}
	public function findAll($orderColumn = NULL, $direction = 'asc', $cascate = false) {
		$query = 'select * from ' . $this->tableName;
		if ($orderColumn) {
			$query .= " order by {$orderColumn} {$direction}";
		}
		$st = $this->db->prepare ( $query );
		$st->execute ();
		$result = $st->fetchAll ();
		if ($cascate) {
			$level = 0;
			$result = $this->loadFkObjects ( $this, $result, $level );
		}
		return $result;
	}
	public function findById($id) {
		$st = $this->db->prepare ( "select * from {$this->tableName} where {$this->tableName}.{$this->idColumn} = :id" );
		$st->bindParam ( ':id', $id );
		$st->execute ();
		$result = $st->fetch ();
		$level = 0;
		$result = $this->loadFkObjects ( $this, $result, $level );
		return $result;
	}
	private function loadFkObjects($object, $result, $level) {
		if ($level == 3) {
			return $result;
		}
		$level ++;
		$properties = get_object_vars ( $object );
		if ($properties ['fk'] != NULL && count ( $properties ['fk'] ) > 0) {
			foreach ( $properties ['fk'] as $model => $relation ) {
				$model = preg_replace ( '/(.+)\[[0-9]+\]$/', "$1", $model );
				if (is_object ( $result )) {
					LoadModel ( $model );
					$theModel = new $model ();
					$objQuery = new stdClass ();
					$objQuery->{$relation [1]} = $result->{$relation [0]};
					$tmpResult = $theModel->findByObject ( $objQuery, $relation [4], $relation [5] );
					$alias = $relation [3] != NULL ? $relation [3] : $model;
					if ($relation [2] == 'one') {
						$object = $this->loadFkObjects ( $theModel, current ( $tmpResult ), $level );
						if ($object != NULL)
							$result->{$alias} = $object;
					} else {
						foreach ( $tmpResult as $oneResult ) {
							$oneResult = $this->loadFkObjects ( $theModel, $oneResult, $level );
							if ($oneResult != NULL)
								$result->{$alias} [] = $oneResult;
						}
					}
				} elseif (is_array ( $result )) {
					foreach ( $result as $k => $oneResult ) {
						$result [$k] = $this->loadFkObjects ( $object, $oneResult, 1 );
					}
					return $result;
				}
			}
		}
		return $result;
	}
	public function findByObject($object, $orderColumn = NULL, $direction = 'ASC', $fullLoad = false) {
		$query = "select * from {$this->tableName} where ";
		$index = 0;
		foreach ( $object as $key => $val ) {
			if ($index > 0) {
				$query .= " AND ";
			}
			if (is_object ( $val ) && get_class ( $val ) == 'Between') {
				$query .= "{$this->tableName}.{$key} BETWEEN '{$val->startValue}' AND '{$val->endValue}' ";
			} elseif ($val === SQLUtils::NOT_NULL ()) {
				$query .= "{$this->tableName}.{$key} IS NOT NULL ";
			} elseif ($val === NULL) {
				$query .= "{$this->tableName}.{$key} IS NULL ";
			} else {
				$query .= "{$this->tableName}.{$key} = :{$key} ";
			}
			$index ++;
		}
		$orderColumn = $orderColumn != NULL ? $orderColumn : $this->idColumn;
		$query .= "ORDER BY {$orderColumn} {$direction}";
		$st = $this->db->prepare ( $query );
		foreach ( $object as $key => &$val ) {
			if ((is_object ( $val ) && get_class ( $val ) == 'Between') || $val === NULL || $val === SQLUtils::NOT_NULL ()) {
				continue;
			}
			$st->bindParam ( $key, $val );
		}
		$st->execute ();
		
		if ($fullLoad == true) {
			$level = 0;
			$newResult = NULL;
			foreach ( $st->fetchAll () as $key => $oneResult ) {
				$newResult [$key] = $this->loadFkObjects ( $this, $oneResult, $level );
			}
			return $newResult;
		} else {
			return $st->fetchAll ();
		}
	}
	public function setPropertyAlias($alias, $colunmName) {
		$this->properties [$alias] = $colunmName;
	}
	public function update($obj) {
		$strQuerySet = '';
		$attrIndex = 0;
		foreach ( $obj as $rowName => $rowVal ) {
			if ($rowName == $this->idColumn || is_object ( $rowVal ) || is_array ( $rowVal ))
				continue;
			if ($attrIndex > 0) {
				$strQuerySet .= ', ';
			}
			if ($rowVal === NULL || $rowVal === '') {
				$strQuerySet .= " {$rowName} = NULL";
			} else {
				$strQuerySet .= " {$rowName} = '{$rowVal}'";
			}
			$attrIndex ++;
		}
		if ($attrIndex > 0 && $obj->{$this->idColumn} != NULL && $obj->{$this->idColumn} > 0) {
			$this->db->query ( "update {$this->tableName} set {$strQuerySet} where {$this->idColumn} = {$obj->{$this->idColumn}}" );
			return true;
		} else {
			return false;
		}
	}
	public function insert($obj) {
		try {
			$query = 'INSERT INTO ' . $this->tableName;
			$attrs = array ();
			$vals = array ();
			if (! key_exists ( $this->idColumn, $obj )) {
				$obj->{$this->idColumn} = NULL;
			}
			foreach ( $obj as $k => $v ) {
				$attrs [] = $k;
				$vals [] = ":{$k}";
			}
			$query .= ' ( ';
			$query .= implode ( ',', $attrs ) . ' ) ';
			$query .= ' VALUES ( ';
			$query .= implode ( ',', $vals ) . ' ); ';
			$st = $this->db->prepare ( $query );
			foreach ( $obj as $k => &$v ) {
				if ($v === '')
					$v = NULL;
				$st->bindParam ( $k, $v );
			}
			if ($st->execute ()) {
				return $this->db->lastInsertId ();
			} else {
				return 0;
			}
		} catch ( Exception $e ) {
			throw new Exception($e);
		}
	}
	public function remove($obj) {
		if ($obj->{$this->idColumn} != NULL && $obj->{$this->idColumn} != 0) {
			$query = 'DELETE FROM ' . $this->tableName;
			$query .= ' WHERE ' . $this->idColumn . ' = ' . $obj->{$this->idColumn};
			$st = $this->db->prepare ( $query );
			return $st->execute ();
		} else {
			return false;
		}
	}
	public function getObject() {
		$st = $this->db->prepare ( 'SHOW COLUMNS FROM ' . $this->tableName );
		$st->execute ();
		$obj = new stdClass ();
		$result = $st->fetchAll ();
		foreach ( $result as $column ) {
			$obj->{$column->Field} = NULL;
		}
		return $obj;
	}
}
