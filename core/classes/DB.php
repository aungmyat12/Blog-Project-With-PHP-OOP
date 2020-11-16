<?php

class DB {
    private static $dbh,$res,$data,$count,$sql;
    public function __construct()
    {
        self::$dbh = new PDO("mysql:host=localhost;dbname=php_course","root","");
//        self::$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    public function query($params=[]) {
        self::$res = self::$dbh->prepare(self::$sql);
        self::$res->execute($params);
        return $this;
    }
    public static function table($table) {
        $sql = "SELECT * FROM $table";
        self::$sql = $sql;
        $db = new DB();
        return $db;
    }
    public function get() {
        $this->query();
        self::$data = self::$res->fetchAll(PDO::FETCH_OBJ);
        return self::$data;
    }
    public function getOne() {
        $this->query();
        self::$data = self::$res->fetch(PDO::FETCH_OBJ);
        return self::$data;
    }
    public function count() {
        $this->query();
        self::$count = self::$res->rowCount();
        return self::$count;
    }
    public function where($col,$operator,$value='') {
        if(func_num_args() == 2) {
            self::$sql .= " WHERE $col='$operator'";
        } else {
            self::$sql .= " WHERE $col $operator '$value'";
        }
        return $this;
    }
    public function orderBy($col,$format) {
        self::$sql .= " ORDER BY $col $format";
        return $this;
    }
    public function andWhere($col,$operator,$value='') {
        if(func_num_args() == 2) {
            self::$sql .= " and $col='$operator'";
        } else {
            self::$sql .= " and $col $operator '$value'";
        }
        return $this;
    }
    public function orWhere($col,$operator,$value='') {
        if(func_num_args() == 2) {
            self::$sql .= " or $col='$operator'";
        } else {
            self::$sql .= " or $col $operator '$value'";
        }
        return $this;
    }
    public static function create($table,$data) {
        $str_col = implode(',',array_keys($data));
        $v = '';
        $x = 1;
        foreach ($data as $d) {
            $v .= '?';
            if($x < count($data)) {
                $v .= ',';
                $x++;
            }
        }
        $sql = "INSERT INTO $table ($str_col) VALUES ($v)";
        self::$sql = $sql;
        $db = new DB();
        $values = array_values($data);
        $db->query($values);
        $id = self::$dbh->lastInsertId();
        return DB::table($table)->where('id',$id)->getOne();
    }
    public static function update($table,$data,$id) {
        $db = new DB();
        $value = "";
        $x = 1;
        foreach ($data as $k => $v) {
            $value .= "$k=?";
            if($x < count($data)) {
                $value .= ",";
                $x++;
            }
        }
        $sql = "UPDATE $table SET $value WHERE id=$id";
        self::$sql = $sql;
        $value = array_values($data);
        $db->query($value);
        return DB::table("$table")->where('id',$id)->getOne();
    }
    public static function delete($table,$id) {
        $sql = "DELETE FROM $table WHERE id='$id'";
        self::$sql = $sql;
        $db = new DB();
        $db->query();
        return true;
    }
    public function paginate($record_per_page,$append="") {
        if(isset($_GET['page'])) {
            $page_no = $_GET['page'];
        }
        if(!isset($_GET['page'])) {
            $page_no = 1;
        }
        if(isset($_GET['page']) and $_GET['page'] < 1) {
            $page_no = 1;
        }
        // 0,5  1   (1-1)*5 = 0
        // 5,5  2   (2-1)*5 = 5
        // 10,5 3   (3-1)*5 = 10
//        get total count
        $this->query();
        $count = self::$res->rowCount();
//        echo $count / $record_per_page;
//        if($page_no > ($count / $record_per_page)) {
//            $page_no = ($count / $record_per_page);
//        }
        // select * from users limit 0,5

        $index = ($page_no-1)*$record_per_page;
        self::$sql .= " LIMIT $index,$record_per_page";
        $this->query();
        self::$data = self::$res->fetchAll(PDO::FETCH_OBJ);
        $prev_no = $page_no-1;
        $next_no = $page_no+1;

        if($append) {
            $prev_page = "?page=$prev_no&$append";
            $next_page = "?page=$next_no&$append";
        } else {
            $prev_page = "?page=$prev_no";
            $next_page = "?page=$next_no";
        }
        $data = [
            'data' => self::$data,
            'total' => $count,
            'prev_page' => $prev_page,
            'next_page' => $next_page
        ];
        return $data;
    }
    public static function raw($sql) {
        self::$sql = $sql;
        $db = new DB();
        return $db;
    }
}

//$db = new DB();
//$users = $db->query('SELECT * FROM users')->get();
//print_r($users);
//$count = $db->query('SELECT * FROM users')->count();
//print_r($count);

//$users = DB::table("articles")->get();
//print_r($users);
//$user = DB::table("users")->where('id',6)->getOne();
//print_r($user->name);
//$users = DB::table("users")->where('id',">",6)->get();
//$count = DB::table("users")->where('id',">",6)->count();
//print_r($count);
//$users = DB::table("users")->orderBy('name',"ASC")->get();
//print_r($users);
//$user = DB::table("users")->where('id',2)->andWhere("name","zaw zaw")->getOne();
//print_r($user);
//$users = DB::table("users")->where('name','LIKE','%ba%')->orWhere('location','Yangon')->get();
//print_r($users);

//$user = DB::create('users', [
//    "name" => "myo pyae sone",
//    "age" => 25,
//    "location" => "Kachin"
//]);
//print_r($user);
//if($user) {
//    echo "Success user inserted";
//}

//$user = DB::update('users',[
//    "name" => "update user",
//    'age' => 70,
//    'location' => 'location'
//],1);
//print_r($user);
//$user = DB::delete('users',12);
//echo $user;
//$user = DB::table("users")->paginate(2);
//[
//    "data" => [],
//    "total" => ,
//    "next_page" => ,
//    "pre_page" =>
//]

?>
<?php
//foreach ($user['data'] as $d) {
//    echo $d->name . "<br>";
//}
//$user = DB::raw("SELECT * FROM users")->paginate(2);
//print_r($user);
?>
