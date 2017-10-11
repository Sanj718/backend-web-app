<?php
class Dbmanagement{
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "webapp";
    public static $tablename = "usertable";
    public static $conn;

    public function __construct(){
        self::$conn = new mysqli($this->servername, $this->username, $this->password);
        if (self::$conn->connect_error) {
            die("Connection failed: " . self::$conn->connect_error);
        }else{
            echo "<!-- DB estamblished correctly!<br> -->";
        }
        $sql = "CREATE DATABASE $this->dbname";
        if (self::$conn->query($sql) === TRUE){
            echo "<!-- Database created successfully <br> -->";
            self::$conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        }{
            echo "<!-- Database $this->dbname already created <br> -->";
            self::$conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        }

    }
    public function createTable(){
        $tablename = self::$tablename;
        $sql = "CREATE TABLE $tablename (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                username VARCHAR(50) NOT NULL,
                password VARCHAR(50) NOT NULL,
                firstname VARCHAR(30),
                lastname VARCHAR(30),
                email VARCHAR(30) NOT NULL,
                sex VARCHAR(30),
                dob DATE,
                ava VARCHAR(255),
                status VARCHAR(50),
                about TEXT,
                reg_date TIMESTAMP CURRENT_TIMESTAMP
                )";

        if (self::$conn->query($sql) === TRUE) {
            $tablename = self::$tablename;
            echo "<!-- Table $tablename created successfully \n -->";
        } else {
            echo "<!-- Error creating table: " . self::$conn->error . "\n -->";
        }
    }
    public function __get($name){
     return self::$conn;
    }
}
$link = new Dbmanagement();
require_once "form.php";