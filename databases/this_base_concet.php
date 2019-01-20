<?php 
class This_base_concet
{
	private $link;
	public function __construct()
	{
		$this->link = @new mysqli('localhost','root','root');
		if ($this->link->connect_error) {
		    die("连接失败: " . $this->link->connect_error);
		}
		$dbint = $this->link->select_db('kefu');
		if( $dbint == false )
		{
			die("请选择数据库");
		}	
		$this->link->query("set names utf8");  
	}
	/**
	 * 执行sql语句,添、删、改、查
	 * @param string $sql
	 * @return 添、删、改=boolean
	 * @return 查=resource
	 */
	public function query($sql)
	{	
		$resource = $this->link->query($sql) or exit('sql语法错误 '.mysqli_errno($this->link)."  <br/>\n\n  ".mysqli_error($this->link)." <br/>\n\n ".$sql);
		return $resource;
	}
	/**
	 * //开启事务 - 使用说明
		$mySQLi -> query('start transaction');
		$mySQLi -> query('set autocommit = false'); //第二种方式
		$mySQLi -> begin_transaction();//第三种方式	
		//发送sql语句,因为sql语句是插入和修改语句，返回的结果是一个布尔值。
		$res1 = $mySQLi -> query($sql1);
		$res2 = $mySQLi -> query($sql2);		
		if($res1 && $res2)
		{
		    echo '操作成功';
		    //提交事务。
		    $mySQLi -> commit();
		}
		else
		{
		    echo '操作失败';
		    //进行数据的回滚
		    $mySQLi -> rollback();
		}	
		$mySQLi -> close();
	 */
	public function transaction()
	{
		$this->link->query('start transaction');
		//$this->link->query('set autocommit = false'); //第二种方式
		//$this->link->begin_transaction();//第三种方式
	}
	//操作成功
	public function commit()
	{
		$this->link->commit();
	}
	//进行数据的回滚
	public function rollback()
	{
		$this->link->rollback();
	}
	/**
	 * 获取总记录数
	 * @param string $sql
	 * @return number
	 */
	public function counts($sql)
	{
		$resource = $this->query($this->get_sql($sql));
		$counts = $resource->num_rows;
		return $counts;
	}
	/**
	 * 获取单条记录
	 * @param string $sql
	 * @return array
	 */
	public function row($sql)
	{
		$resource = $this->query($this->get_sql($sql));
		$row = $resource->fetch_assoc();
		return $row;
	}
	/**
	 * 获取多条记录
	 * @param string $sql
	 * @return array
	 */
	public function rows($sql)
	{
		$resource = $this->query($this->get_sql($sql));
		$rows = null;
		if ($resource->num_rows > 0 ) 
		{
	    	// 输出数据
		    while(@$row = $resource->fetch_assoc()) 
		    {
		        $rows[] = $row;
		    }
		} 
			
		return $rows;
	}
	/**
	 * 获取最新记录的ID号
	 * @return int
	 */
	public function insert_id()
	{
		$id = mysqli_insert_id($this->link);;
		return $id;
	}
	/**
	 * 获取sql语句
	 * @param string $sql
	 * @return string
	 */
	public function get_sql($sql)
	{
		return $sql;
	}
	/**
	 * 关闭链接
	 */
	public function close()
	{
		$this->link->close();
	}
	public function __destruct()
	{
		$this->close();
	}
}