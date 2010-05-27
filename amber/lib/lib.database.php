<?php 
/*****************************************************************************************************************************
*  This file contains a class for manipulating the database
*  
*  TODO:
* 	Add support for multiple database types
*	Need to make where recursive
* 
* @package Amber
* @subpackage Database
* @author John Janes <john@johnjanes.com> [http://www.johnjanes.com] 
* @version 0.1
*****************************************************************************************************************************/

// CONTANTS
// The RegEx for pulling data out of the db connection string 
// ie  mysql://username:password@host/db
DEFINE('CONNECTION_PREG',"/^(.*)\:\/\/(.*):(.*)@(.*)$/");

// Parameter that can  be passed 
DEFINE('LEFT_JOIN',uniqid());
DEFINE('RIGHT_JOIN',uniqid());
DEFINE('INNER_JOIN',uniqid());
DEFINE('SELECT_IGNORE',uniqid());
DEFINE('DB_OR',uniqid());
DEFINE('DB_AND',uniqid());
DEFINE('DB_INC',uniqid());
DEFINE('DB_IGNORE',uniqid());

// Database Connections Factory
final class Database_Connections 
{
	// satic variables 
	private static 	$default = null, 			// containts default db connection 
					$cons = array();			// connection array
	
	/**
	* Store function.
	*
	* @access public 
	* @param connection id 
	* @param an array with various values about the db
	*/
	public function store($connection_id,$connection = array())
	{
		self::$cons[$connection_id] = array();
		self::$cons[$connection_id]  = & $connection;
	}
	
	public function count()
	{
		return count(self::$cons);
	}

	public function _default()
	{
		if (self::$default)
		{
			return  self::$cons[self::$default];
		}
		else 
		{
			$keys = array_keys(self::$cons);
			return self::$cons[$keys[0]];
		}
	}
	
	public function retrieve($connection_id = null)
	{
		if (!$connection_id)
		{
			return self::_default();
		}

		if (array_key_exists($connection_id,self::$cons))		
		{
			return self::$cons[$connection_id];
		}
		throw new Exception("Cannot locate connection with the ID $connection_id.");
	}
	
	public function set_default($connection_id)
	{
		self::$default = $connection_id;
	}
	
	public function destroy_all()
	{
		foreach (self::$cons as $connection)
		{
			mysql_close($connection['link']);
		}
	}
}


final class Database_Connection
{
	function __construct($db_string = null, $connect = false)
	{

					
	}
	
	public function connect($db_string, $connection_id = null)
	{
		$match = preg_match(CONNECTION_PREG, $db_string);
		
		if ($match)
		{
			$db_name 	= null;
			$matchs		= array();	
			
			preg_match_all(CONNECTION_PREG, $db_string, $matchs);
			
			$full 		= $matchs[0][0];
			$db   		= $matchs[1][0];		
			$username 	= $matchs[2][0];
			$password   = $matchs[3][0];
			$host		= $matchs[4][0];
			
			if (strstr($host,'/'))
			{
				list($host,$db_name) = explode('/',$host);
			}	
			
			$cid = ($connection_id)?$connection_id:uniqid();
			
			$link = mysql_connect($host,$username,$password);
			
			if (!$link)	
			{
				throw new Exception(mysql_error());
			}

			if ($db_name)
			{
				mysql_select_db($db_name, $link);
			}
			
			$connection_data = array(
				'database_name'			=> $db_name,
				'link'					=> $link,
				'connection_string'		=> $db_string,
				'connection_host'		=> $host,
				'connection_username'	=> $username,
				'connection_password'	=> $password,
				'connection_time'		=> time()
			);
			
			Database_Connections::store($cid,$connection_data);
			
			return $cid;
		}
	}
}

final class Database_Query
{
	private $query_builder = array(), $built_query, $connection_data, $result;
	
	function __construct($string = null, $connection_id = null, $execute = false)
	{
		$this->connection_data = Database_Connections::retrieve();
	}
	
	public function select($column = '*')
	{
		$select = array();
		$select[] = $column;
		
		if (func_num_args() > 1)
		{
			for ($i = 1; func_num_args() > $i; $i++)
			{
				$arg = func_get_arg($i);
				$select[] = $arg;
			}
		}
		
		$this->query_builder[] = array('SELECT',$select);
		
		return $this;
	}
	
	public function frm($table)
	{
		$this->query_builder[] = array('FROM',$table);
		return $this;
	}
	
	public function insert($table)
	{
		if (func_num_args() > 1)
		{
			$values = array();
			$ignored = false;
			
			for ($i = 1; func_num_args() > $i; $i++)
			{
				$arg = func_get_arg($i);
				
				switch ($arg)
				{
					case DB_IGNORE:
						$ignored = true;
						break;
					default:
						if (is_array($arg))
						{
							$values = array_merge($values,$arg);
						}
						else 
						{
							$values[] = $arg;	
						}		
				}
			}
			
			$insert = ($ignored)?'INSERT_IGNORE':'INSERT';
			
			if (count($values) > 0)
			{	
				$this->query_builder[] = array($insert,array($table,$values));
			} 
			else 
			{
				$this->query_builder[] = array($insert,$table);
			}
		} 
		else 
		{
			$this->query_builder[] = array('INSERT',$table);
		}
		return $this;
	}
	
	public function set($value)
	{
		$set = array();
		$set[] = $value;
		
		if (func_num_args() > 1)
		{
			for ($i = 1; func_num_args() > $i; $i++)
			{
				$arg = func_get_arg($i);
				$set[] = $arg;
			}
		}		
		
		$this->query_builder[] = array('SET',$set);
			
		return $this;
	}
	

	public function join()
	{

		return $this;
	}
	

	
	public function _where($where)
	{
		$_where = array();

		$_where[] = $where;
		
		if (func_num_args() > 1)
		{
			for ($i = 1; func_num_args() > $i; $i++)
			{
				$arg = func_get_arg($i);
				$_where[] = $arg;
			}
		}

		return array('_WHERE',$_where);
	}
	
	public function limit($limit)
	{
		$this->query_builder[] =  array('LIMIT', $limit);
	}
	
	public function where($where) 
	{
		$_where = array();

		$_where[] = $where;
		
		if (func_num_args() > 1)
		{
			for ($i = 1; func_num_args() > $i; $i++)
			{
				$arg = func_get_arg($i);
				$_where[] = $arg;
			}
		}
		
		$this->query_builder[] =  array('WHERE', $_where);
				
		return $this;
	}

	public function show()
	{
		echo $this->built_query;
		return $this;
	}
	
	public function execute($destroy_query = false)
	{
		if (!$this->built_query)
		{
			throw new Exception("Need to build() query before you execute().");
		}
		
		$link = $this->connection_data['link'];
		
		$res = mysql_query($this->built_query,$link);
		
		if (!$res)
		{
			throw new Exception(mysql_error());
		}
		
		$this->result = $res;

		return $this;
	}	
	
	public function count()
	{
		return mysql_num_rows($this->result);
	}
	
	public function results()
	{
		$rows = array();
		
		for ($i = 0; $this->count() > $i; $i++)
		{
			$row 	= mysql_fetch_row($this->result);
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	public function bind()
	{
		
		return $this;
	}
	
	public function build()
	{
		$built_query = '';
		$in_set 	= false;
		$set_data	= array();
		
		foreach ($this->query_builder as $array)
		{
			list ($type,$data) = $array;
			
			if ($type != 'SET' && $in_set)
			{
				$built_query 	.= "SET " . join(', ',$set_data) . ' ';
				$set_data 		= array();
				$in_set 		= false;
			}
			
			switch ($type)
			{
				case 'LIMIT':
					$built_query 	.= "LIMIT $data ";
					BREAK;
				case 'SET':
					$in_set = true;
					if (is_array($data[0]))
					{
						foreach ($data as $item)
						{
							if (is_array($item))
							{
								throw new Exception("Must pass consistent data types to the set() method.");
							}
							
							list($var,$value) = $item;
							$set_data[] = "$var = '" . addslashes($value) . "' ";
						}
					}
					else 
					{
						if (count($data) > 1)
						{
							list($var,$value) = $data;
							
							switch ($value)
							{
								default:
									$i_val = "'" . addslashes($value) . "'";
							}
							
							$set_data[] = "$var = {$i_val} ";						
						} 
						else 
						{
							$set_data[] = $data[0] . ' ';
						}
					}
					
					break;
				case 'INSERT_IGNORE':
				case 'INSERT':
					$INSERT = ($type == 'INSERT_IGNORE')?'INSERT IGNORE':'INSERT';
					
					if (is_array($data)) 
					{
						list($table,$array) = $data;	
						$built_query .= "$INSERT INTO $table (" . join(',',$array) . ') ';
					}
					else 
					{
						$built_query .= "$INSERT INTO $data ";
					}
					break;
				case 'SELECT':
					$built_query .= "SELECT " . join(', ',$data) . ' ';
					break;
				case 'FROM':
					$built_query .= "FROM $data ";
					break;
				case 'WHERE':
					$string = 'WHERE ( ';
					
					foreach ($data as $arg)
					{
						if (is_array($arg))
						{
							
							switch ($arg[0])
							{
								case '_WHERE':
									$substring = '( ';

									foreach ($arg[1] as $element)
									{
										switch ($element)
										{
											case DB_AND:
												$substring .= 'AND ';
												break;
											case DB_OR:
												$substring .= 'OR ';
												break;
											default:
												$substring .= "$element ";
										}
										
									}
									$substring .= ') ';
									
									$string .= $substring;
									break;
							}
						}
						else 
						{ 
							switch ($arg)
							{
								case DB_AND:
									$string .= 'AND ';
									break;
								case DB_OR:
									$string .= 'OR ';
									break;
								default:
									$string .= "$arg ";
							}
						}
					}
					$string .= ') ';
					
					$built_query .= $string;
					
					break;
			}
		}
		
		if ($in_set)
		{
			$built_query 	.= "SET " . join(', ',$set_data) . ' ';
			$set_data 		= array();
			$in_set 		= false;
		}
					
		$this->built_query = $built_query;
		return $this;
	}
		
}


?>