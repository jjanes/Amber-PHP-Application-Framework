<?php

class Model_Test extends System_Model {
	public $v_test = array();
	
	public function __init()
	{
		$q1 = new Database_Query();
		// $q1->insert("names", DB_IGNORE)->set('name','test1')->build()->show()->execute();

		
		/*	
		$q1 = new Database_Query();
		$q1	->insert("names",'name',DB_IGNORE)
			->select('name')
			->frm('names')
			->build()
			->show()
			->execute();
		*/
	
		
		$db = new Database_Query();
		$db	->select('id as id','name as test')
			->frm('names')
			->build()
			->execute();
			
		/*
			->where(
				$db->_where('id = 1',DB_OR,'id = 4'),
				DB_AND,
				$db->_where('id = 2'),
				DB_AND,
				'id = 3'
			)
		 */
		// print_r($db->results());
		
		$results = $db->results();
		$this->v_test = $results;
		$this->extract['test'] = $results;

		
	}
	
	public function __index()
	{	
	}
	
	public function __finished()
	{

	}
	
}