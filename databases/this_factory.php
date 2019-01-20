<?php
class This_Linked
{
	private $linked=array();
	private $sqlstr=null;
	private $flag=null;
	#########################################################################################
	#执行删除数据
	public function delete($table,$where=null)
	{
		if(is_string($where))
		{
			$wheres = $where==''?'':"where {$where}";
		}
		else if(is_array($where))
		{
			foreach($where as $k=>$v)
			{
				$wheres .= ($wheres==''?'':'and ').$k."='".$v."'";
			}
			$wheres = 'where '.$wheres;
		}		
		$this->linked[0] = __FUNCTION__." from {$table} {$wheres}";
		if( $where != null )
		{
			$db = This_single_case::getConcetBase();
			$this->get();
			$bool = $db->query($this->sqlstr);
			
			return $bool;
		}
		else 
		{
			$this->flag = __FUNCTION__;
			return $this;
		}
	}
	#########################################################################################
	#执行修改数据
	public function update($table,$data,$where=null)
	{
		$tmp = '';
		if(is_array($data))
		{
			foreach($data as $k=>$v)
			{
				$tmp .= ($tmp==''?'':',').$k."='".$v."'";
			}
		}
		else 
		{
			$tmp = $data;
		}
		if(is_string($where))
		{
			$wheres = $where==''?'':"where {$where}";
		}
		else if(is_array($where))
		{
			foreach($where as $k=>$v)
			{
				$wheres .= ($wheres==''?'':'and ').$k."='".$v."'";
			}
			$wheres = 'where '.$wheres;
		}		
		$this->linked[0] = __FUNCTION__." {$table} set {$tmp} {$wheres}";
		if( $where != null )
		{
			$db = This_single_case::getConcetBase();
			$this->get();
			$bool = $db->query($this->sqlstr);
			
			return $bool;
		}
		else 
		{
			$this->flag = __FUNCTION__;
			return $this;
		}
	}
	#########################################################################################
	#执行添加数据
	public function insert($table,$valuse=array())
	{
		$flag = '';
		if(!empty($valuse))
		{
			$key = join(",",array_keys($valuse));
			$val = "'".join("','",array_values($valuse))."'";
			$flag = "({$key}) values({$val})";
		}
		$this->linked[0] = __FUNCTION__." into {$table}{$flag}";
		if(!empty($valuse))
		{
			$db = This_single_case::getConcetBase();
			$this->get();
			$bool = $db->query($this->sqlstr);

			return $bool;
		}
		else 
		{
			return $this;
		}
		
	}
	#执行添加数据包
	public function values($data)
	{
		if(is_array($data))
		{
			$key = join(",",array_keys($data));
			$val = "'".join("','",array_values($data))."'";
			$flag = "({$key}) values({$val})";
			$this->linked[1] = $flag;
			
			$db = This_single_case::getConcetBase();
			$this->get();
			$bool = $db->query($this->sqlstr);

			return $bool;
		}
		else
		{
			echo '只允许关联数组结果';
		}
	}
	#########################################################################################
	#执行sql字段查询
	public function select($field)
	{
		if(is_array($field))
		{
			$field = join(',',array_values($field));
		}
		$this->linked[0] = __FUNCTION__." {$field} ";
		return $this;
	}
	#执行sql数据表名
	public function from($table)
	{
		$this->linked[1] = ' '.__FUNCTION__." {$table} ";
		return $this;
	}
	#执行sql按条件查询
	public function where($where)
	{
		if(is_string($where))
		{
			$wheres = $where==''?'':"where {$where}";
		}
		else if(is_array($where))
		{
			foreach($where as $k=>$v)
			{
				@$wheres .= ($wheres==''?' ':' and ').$k."='".$v."'";
			}
			$wheres = __FUNCTION__.$wheres;
		}
		$this->linked[2] = $wheres;
		if( $this->flag != null )
		{
			$db = This_single_case::getConcetBase();
			$this->get();
			$bool = $db->query($this->sqlstr);

			return $bool;
		}
		else 
		{
			return $this;
		}
	}
	#执行sql提供数值查询
	public function in($str)
	{
		if(is_array($str))
		{
			$str = join(',',array_values($str));
		}
		$this->linked[3] = ' '.__FUNCTION__."({$str})";
		return $this;
	}
	#执行sql两个数之间查询
	public function between($str)
	{
		if(is_array($str))
		{
			$str = join(' and ',array_values($str));
		}
		$this->linked[4] = ' '.__FUNCTION__." {$str} ";
		return $this;
	}
	#执行sql排序
	public function order_by($orderby)
	{
		$tmp = '';
		if(is_array($orderby))
		{
			foreach($orderby as $k=>$v)
			{
				$tmp .= ($tmp==''?'':',').$k.' '.$v;
			}
			$orderby = $tmp;
		}
		$this->linked[8] = ' '.str_replace('_', ' ', __FUNCTION__)." {$orderby} ";
		return $this;
	}	
	#执行sql模糊查询
	public function like($str)
	{
		$this->linked[6] = ' '.__FUNCTION__." {$str} ";
		return $this;
	}
	#执行sql分组查询
	public function group_by($field)
	{
		$this->linked[5] = ' '.str_replace('_', ' ', __FUNCTION__)." {$field} ";
		return $this;
	}
	#执行sql分组条件
	public function having($field)
	{
		$this->linked[7] = ' '.__FUNCTION__." {$field} ";
		return $this;
	}
	#执行sql分页查询
	public function limit($offset)
	{
		$tmp = '';
		if(is_array($offset))
		{
			foreach($offset as $k=>$v)
			{
				$tmp .= ($tmp==''?'':',').$k.','.$v;
			}
			$offset = $tmp;
		}
		$this->linked[9] = ' '.__FUNCTION__." {$offset} ";
		return $this;
	}
	#########################################################################################
	#执行sql连表语法形式
	public function get()
	{
		ksort($this->linked);
		$sql = join(' ',$this->linked);
		$this->sqlstr = $sql;
		
		return $this;
	}
	#########################################################################################
	#执行sql语法形式
	public function query($sql)
	{		
		$this->sqlstr = $sql;
	
		return $this;
	}
	#执行，改、删
	public function exect()
	{		
		$db = This_single_case::getConcetBase();
		$int = $db->query($this->sqlstr);
		return $int;
	}
	#########################################################################################
	#查询一条记录
	public function array_row()
	{
		$db = This_single_case::getConcetBase();
		$row = $db->row($this->sqlstr);
		return $row;
	}
	#查询多条记录
	public function array_rows()
	{
		$db = This_single_case::getConcetBase();
		$row = $db->rows($this->sqlstr);
		return $row;
	}
	#查询记录总数
	public function array_nums()
	{
		$db = This_single_case::getConcetBase();
		$row = $db->counts($this->sqlstr);	
		return $row;
	}
	#加
	public function sum($field)
	{
		$this->linked[0] = 'select '.__FUNCTION__.'('.$field.') as '.$field.' ';	
		$this->get();
		$row = $this->array_row();
		return $row[$field];
	}
	#指定字段值
	public function value($field)
	{
		$this->linked[0] = 'select '.$field.' ';	
		$this->get();
		$row = $this->array_row();
		return $row[$field];
	}
	#自增长
	public function setInc($table,$field,$step=1,$LazyTime=1)
	{
		if( $LazyTime != null )
		{
			sleep($LazyTime);
		}
		$this->linked[0] = 'update '.$table.' set '.$field.'='.$field.'+'.$step.' ';
		$this->get();	
		return $this->exect();
	}
	#自减
	public function setDec($table,$field,$step=1,$LazyTime=1)
	{
		if( $LazyTime != null )
		{
			sleep($LazyTime);
		}
		$this->linked[0] = 'update '.$table.' set '.$field.'='.$field.'-'.$step.' ';
		$this->get();	
		return $this->exect();
	}
	#修改字段
	public function setField($table,$field,$value,$LazyTime=1)
	{
		if( $LazyTime != null )
		{
			sleep($LazyTime);
		}
		$this->linked[0] = 'update '.$table.' set '.$field.'="'.$value.'" ';
		$this->get();	
		return $this->exect();
	}
	#########################################################################################
	#获取sql语法字符形式
	public function getlast_sql()
	{
		return $this->sqlstr;
	}
	#获取sql最后执行ID
	public function getlast_id()
	{
		$db = This_single_case::getConcetBase();
		return $db->insert_id();
	}
}
/**
 * 静态模式
 * @author TanLin
 *
 */
class This_factory
{
	/**
	 * 获取多条记录
	 * @param 表名 $table
	 * @param 字段 $field
	 * @param 条件 $where
	 * @return array[]
	 */
	public static function get_rows($table,$field,$where)
	{
		$db = This_single_case::getConcetBase();
		$sql = This_factory::select($table,$field,$where);
		$rows = $db->rows($sql);
		return $rows;
	}
	/**
	 * 获取单条记录
	 * @param 表名 $table
	 * @param 字段 $field
	 * @param 条件 $where
	 * @return array
	 */
	public static function get_row($table,$field,$where)
	{
		$db = This_single_case::getConcetBase();
		$sql = This_factory::select($table,$field,$where);
		$row = $db->row($sql);
		return $row;
	}
	/**
	 * 获取最新记录的ID号
	 * @return number
	 */
	public static function get_insert_id()
	{
		$db = This_single_case::getConcetBase();
		return $db->insert_id();
	}
	/**
	 * 获取总记录数
	 * @param u表名 $table
	 * @param 字段 $field
	 * @param 条件 $where
	 * @return number
	 */
	public static function count($table,$field,$where=null)
	{
		$db = This_single_case::getConcetBase();
		$sql = This_factory::select($table,$field,$where);
		return $db->counts($sql);
	}
	/**
	 * 查询记录,返回sql
	 * @param 表名 $table
	 * @param 字段 $field
	 * @param 条件 $where
	 * @return sql
	 */
	public static function select($table,$field,$where)
	{
		if(is_array($field))
		{
			$field = join(',',array_values($field));
		}
		if(is_string($where))
		{
			$wheres = $where==''?'':"where {$where}";
		}
		else if(is_array($where))
		{
			foreach($where as $k=>$v)
			{
				$wheres .= ($wheres==''?'':'and ').$k."='".$v."'";
			}
			$wheres = 'where '.$wheres;
		}
		$sql = "select {$field} from {$table} {$wheres}";
		return $sql;
	}
	/**
	 * 添加数据
	 * @param 表名 $table
	 * @param 数据 $data
	 * @return $bool
	 */
	public static function insert($table,$data)
	{
		$db = This_single_case::getConcetBase();
		$key = join(",",array_keys($data));
		$value = "'".join("','",array_values($data))."'";
		$sql = "insert into {$table}({$key}) values({$value})";
		$bool = $db->query($db->get_sql($sql));
		return $bool;
	}
	/**
	 * 修改数据
	 * @param 表名 $table
	 * @param 数据 $data
	 * @param 条件 $where
	 * @return $bool
	 */
	public static function update($table,$data,$where)
	{
		$db = This_single_case::getConcetBase();
		$tmp = '';
		foreach($data as $k=>$v)
		{
			$tmp .= ($tmp==''?'':',').$k."='".$v."'";
		}
		if(is_string($where))
		{
			$wheres = $where==''?'':"where {$where}";
		}
		else if(is_array($where))
		{
			foreach($where as $k=>$v)
			{
				$wheres .= ($wheres==''?'':'and ').$k."='".$v."'";
			}
			$wheres = 'where '.$wheres;
		}
		$sql = "update {$table} set {$tmp} {$wheres}";
		$bool = $db->query($db->get_sql($sql));
		return $bool;
	}
	/**
	 * 删除数据
	 * @param 表名 $table
	 * @param 条件 $where
	 * @return bool
	 */
	public static function delete($table,$where)
	{
		$db = This_single_case::getConcetBase();
		if(is_string($where))
		{
			$wheres = $where==''?'':"where {$where}";
		}
		else if(is_array($where))
		{
			foreach($where as $k=>$v)
			{
				$wheres .= ($wheres==''?'':'and ').$k."='".$v."'";
			}
			$wheres = 'where '.$wheres;
		}
		$sql = "delete from {$table} {$wheres}";
		$bool = $db->query($db->get_sql($sql));
		return $bool;
	}
}