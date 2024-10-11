<?php
class Database
{
    private $host = 'aws-0-us-east-1.pooler.supabase.com';
    private $db_name = 'postgres';
    private $username = 'postgres.etyhnplamagtxvxhrkqp';
    private $password = 'col#Page9415590';
    private $port = '6543';
    private $conn;

    public function connect()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("pgsql:host=$this->host;port=$this->port;dbname=$this->db_name", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }
        return $this->conn;
    }
}

?>