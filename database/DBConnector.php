<?php
require SYSTEM_PATH . 'database/mysql/MysqlConnector.php';
class DBConnector {
	private static $instance;
	private static $dataSourceName;
	public static function getInstance($dataSourceName = 'DEFAULT') {
		if (! isset ( self::$instance ) || self::$dataSourceName != $dataSourceName) {
			self::$dataSourceName = $dataSourceName;
			self::$instance = new PDO ( DataSource::get ( $dataSourceName ) ['DB_TYPE'] . ':host=' . DataSource::get ( $dataSourceName ) ['DB_SERVER'] . ';dbname=' . DataSource::get ( $dataSourceName ) ['DB_NAME'], DataSource::get ( $dataSourceName ) ['DB_USER'], DataSource::get ( $dataSourceName ) ['DB_PASS'], array (
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8" 
			) );
			self::$instance->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			self::$instance->setAttribute ( PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING );
			self::$instance->setAttribute ( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ );
		}
		
		return self::$instance;
	}
}