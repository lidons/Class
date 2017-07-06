<?php
$config = include 'config.php';
$m = new Model($config);
// $m->limit('0,5')
//   ->table('imooc_cate')
//   ->field('id,cName')
//   ->order('id desc')
//   ->where('id>3')
//   ->select();
// var_dump($m->sql);

// var_dump($m->limit('0,5')->table('imooc_cate')->field('id,cName') ->where('id>=3') ->select());
// var_dump($m->sql);

//插入
// $data = ['Cname'=>'面包屑'];
// $insertId = $m->table('imooc_cate')->insert($data);
// var_dump($insertId);

//删除
//   $deleteId = $m->table('imooc_cate')->where('cName=数码产品')->delete();
//   var_dump($deleteId);
//   var_dump($m->sql);

// 更新
// $data = ['cName'=>'你真搞笑'];
// var_dump($m->table('imooc_cate')->where('id=7')->update($data));
// var_dump($m->sql);

//max函数
// var_dump($m->table('imooc_cate')->max('id'));

var_dump($m->getBycName('你真搞笑'));
class Model{
//	主机名
	protected $host;
// 	用户名
	protected $user;
// 	密码
	protected $pwd;
// 	数据库名
	protected $dbname;
// 	字符集
	protected $charset;
// 	数据表前缀
	protected $prefix;
	
	
// 	数据库连接资源
	protected $link;
// 	数据表名 可以指定表名
	protected $tableName='imooc_cate';
// 	sql语句
	protected $sql;
// 	操作数组  存放的是所有的查询条件
	protected $options;
	
// 	构造方法，对成员变量进行初始化
function __construct($config){
	//对成员变量进行初始化
	$this->host  = $config['DB_HOST'];
	$this->user = $config['DB_USER'];
	$this->pwd = $config['DB_PWD'];
	$this->dbname = $config['DB_NAME'];
	$this->charset = $config['DB_CHARSET'];
	$this->prefix = $config['DB_PREFIX'];
	
	//连接数据库
	$this->link = $this->connect();
	
	//得到数据表名 User=》UserModel Article=》ArticleModel
	$this->tableName = $this->getTableName();
	//初始化options数组
	$this->initOptions();
}
//连接数据库
protected function connect(){
	$link = mysqli_connect($this->host, $this->user, $this->pwd);
	if (!$link){
		die('数据库连接失败');
	}
	mysqli_select_db($link, $this->dbname);
	mysqli_set_charset($link, $this->charset);
	return $link;
}
//得到表名
protected function getTableName(){
	//如果设置了成员变量，那么就通过成员变量来得到表名
	if (!empty($this->tableName)){
		return $this->prefix.$this->tableName;
	}
	//如果没有设置成员变量，那么通过类名来得到表名
	 /*得到当前类名  表名字符串*/
	$className = get_class($this);
	$table = strtolower(substr($className, 0,-5));
	return $this->prefix.$table;
}
//初始化操作数组
protected function initOptions(){
	$arr = ['where','table','field','order','group','having','limit'];
	foreach ($arr as $value){
		//将options数组中键的值全部清空
		$this->options[$value]= '';
		//将table默认设置为table
		if($value == 'table'){
			$this->options[$value] =$this->tableName;
		}elseif ($value == 'field'){
			$this->options[$value] = '*';
		}
	}
}
// 	field方法
function field($field){
	//如果不为空再进行处理
	if (!empty($field)){
		if (is_string($field)){
			$this->options['field'] = $field;
		}elseif (is_array($field)){
			$this->options['field']= join(',', $field);
		}
	}
	return $this;
}
// 	table方法
function table($table){
	if (!empty($table)){
		$this->options['table']=$table;
	}
	return $this;
}
// 	where方法
function where($where){
	if (!empty($where)){
		$this->options['where'] = 'where '.$where;
	}
	return $this;
}
// 	group方法
function group($group){
	if (!empty($group)) {
		$this->options['group'] = 'group by'.$group;
	}
	return $this;
}
// 	having方法
function having($having){
	if (!empty($having)) {
		$this->options['having'] = 'having '.$having;
	}
	return $this;
}
// 	order方法
function order($order){
	if (!empty($order)) {
		$this->options['order'] =' order by '.$order ;
	}
	return $this;
}
// 	limit方法
function limit($limit){
	if (!empty($limit)) {
		if (is_string($limit)){
			$this->options['limit'] = ' limit '.$limit;
		}elseif (is_array($limit)){
			$this->options['limit'] = ' limit ' .join(',',$limit);
		}
	}
	return $this;
}
// 	select方法 查询
function select(){
      //先预写一个带有占位符的sql语句
     $sql = 'select %FIELD% from %TABLE%
     	 %WHERE% %GROUP% %HAVING% %ORDER% %LIMIT%';
     //将options中对应的值以此替换上面的占位符
     $sql  = str_replace(['%FIELD%','%TABLE%','%WHERE%','%GROUP% ','%HAVING%','%ORDER%',' %LIMIT%']
     		            , [$this->options['field'],$this->options['table'],$this->options['where'],
     		               $this->options['group'],$this->options['having'],$this->options['order'],
     		               $this->options['limit']], $sql);
     //保存一份sql语句,便于调试
     $this->sql = $sql;
     //执行生气了语句
     return $this->query($sql);
}
//insert方法 插入 
//$data为关联数组，键是字段名，值就是字段值
function insert($data){
	//处理字符串问题 两边需要加单双引号
	$data  = $this->parseValue($data);
	//insert into table(字段) value (值)
	//提取所以的键(字段)
	$keys = array_keys($data);
	//提取所有的值
	$values = array_values($data);
	//增加数据的sql语句
	$sql = 'insert into %TABLE%(%FIELE%) values (%VALUES%)';
	$sql = str_replace(['%TABLE%','%FIELE%','%VALUES%'], 
			           [$this->options['table'],join(',', $keys),join(',', $values)], $sql);
	$this->sql = $sql;
	return $this->exec($sql,true);
}

//传递进来一个数组，将数组中值为字符串的两边加引号
protected function parseValue($data){
	foreach ($data as $key => $value){
		//判断是不是为字符串
		if(is_string($value)){
			$value = '"'.$value.'"';
		}
		$newData[$key] = $value;
	}
	return $newData;
}


//delete函数 删除
function delete(){
	$sql = 'delete from %TABLE% %WHERE%';
	$sql = str_replace(['%TABLE%','%WHERE%'],
		   [$this->options['table'],$this->options['where']], 
			$sql);
	$this->sql = $sql;
	
	return $this->exec($sql);
}
//更新函数 update
//$data为关联数组，键是字段名，值就是字段值
//update 表名  set 字段名=字段值，字段名=字段值
function update($data){
	//处理字符串问题 两边需要加单双引号
	$data  = $this->parseValue($data);
	//拼接为固定的格式
	$value = $this->parseUpdate($data);
	//准备sql语句
	$sql = 'update %TABLE%   set  %VALUE% %WHERE%';
	$sql = str_replace(['%TABLE% ','%VALUE%','%WHERE%']
			, [$this->options['table'],$value,$this->options['where']], $sql);
	$this->sql = $sql;
	return $this->exec($sql);
}
protected function parseUpdate($data){
	foreach ($data as $key => $value){
		$newData[] = $key.'='.$value;
	}
	return join(',', $newData);
}



// 	query 有结果集
function query($sql){
	//清空options中的值
	$this->initOptions();
	
// 	var_dump($sql);
// 	exit();

	$result = mysqli_query($this->link, $sql);
	//提取结果集存放到数组中
	if ($result && mysqli_affected_rows($this->link)) {
		while ( $data = mysqli_fetch_assoc($result)){
			$newData[] = $data;
		}
	}
	return $newData;
}
// 	exec 无结果集
function exec($sql,$isInsert=false){
	//清空options中的值
	$this->initOptions();
	
	$result = mysqli_query($this->link, $sql);
	if ($result && mysqli_affected_rows($this->link)) {
		if($isInsert){
			return mysqli_insert_id($this->link);
		}else{
			return mysqli_affected_rows($this->link);
		}
	}
	return false;
}

//获取sql语句
function __get($name){
	if ($name == 'sql') {
		return $this->sql;
	}
	return false;
} 


// max函数
function max($field){
	$result = $this->field('max('.$field.') as max')
	->select();
	return $result[0]['max'];
}
//析构方法
function __destruct(){
	mysqli_close($this->link);
}
//魔术方法 getBy+你要查询的字段名称
function __call($name,$args){
		//获取钱5个字符
		$str = substr($name, 0,5);
		//获取后面的字段名
		$field = strtolower(substr($name, 5));
		//判断前五个是否为getBy
		if ($str == 'getBy') {
			return $this->where($field.'="'.$args[0].'"')->select();
		}
		return false;
}



}