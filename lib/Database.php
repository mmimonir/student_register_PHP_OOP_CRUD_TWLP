<?php

class Database
{
    private $hostdb = "localhost";
    private $userdb = "root";
    private $passdb = "";
    private $namedb = "db_student";
    private $pdo;

    public function __construct()
    {
        if (!isset($this->pdo)) {
            try {
                $link = new PDO("mysql:host=" . $this->hostdb . ";dbname=" . $this->namedb, $this->userdb, $this->passdb);
                $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $link->exec("SET CHARACTER SET utf8");
                $this->pdo = $link;
            } catch (PDOException $e) {
                die("Failed to connect with Database" . $e->getMessage());
            }
        }
    }

//Read Data
    /*
    $sql = "SELECT * FROM tableName WHERE id=:id AND email=:email LIMIT 5";
    $query = $this->pdo->prepare($sql);
    $query->bindValue(':id', $id);
    $query->bindValue(':email', $email);
    $query->execute();
     */
    public function select($table, $data = array())
    {
        $sql = 'SELECT ';
        $sql .= array_key_exists('select', $data) ? $data['select'] : '*';
        //this call ternary oparator. if(array_key_exists('select', $data){$dara['select']}else{'*'})
        $sql .= ' FROM ' . $table;
        if (array_key_exists('where', $data)) {
            $sql .= ' WHERE ';
            $i = 0;
            foreach ($data['where'] as $key => $value) {
                $add = ($i > 0) ? ' AND ' : ''; //protom bar and + korbey na karon i er man 0, 2nd time korbey.
                $sql .= "$add" . "$key=:$key"; //$key ekoi value print korbey. id=:id
                $i++;
            }
        }

        if (array_key_exists("order_by", $data)) {
            $sql .= ' ORDER BY ' . $data['order_by'];
        }
        if (array_key_exists("start", $data) && array_key_exists("limit", $data)) {
            $sql .= ' LIMIT ' . $data['start'] . ',' . $data['limit'];
        } elseif (!array_key_exists("start", $data) && array_key_exists("limit", $data)) {
            $sql .= ' LIMIT ' . $data['limit'];
        }

        $query = $this->pdo->prepare($sql);

        if (array_key_exists("where", $data)) {
            foreach ($data['where'] as $key => $value) {
                //$query->bindValue(':id', $id);
                $query->bindValue(":$key", $value);
            }
        }

        $query->execute();

        if (array_key_exists("return_type", $data)) {
            switch ($data['return_type']) {
                case 'count':
                    $value = $query->rowCount();
                    break;

                case 'single':
                    $value = $query->fetch(PDO::FETCH_ASSOC);
                    break;

                default:
                    $value = "";
                    break;
            }
        } else {
//switch na peley sobgulu fetch korbey.
            if ($query->rowCount() > 0) {
                $value = $query->fetchAll();
            }
        }

        return !empty($value) ? $value : false;
    }

//Insert Data
    /*
    $sql = "INSERT INTO tableName (name, email) VALUES (:name, :email);
    $query = $this->pdo->prepare($sql);
    $query->bindValue(':name', $name);
    $query->bindValue(':email', $email);
    $query->execute();
     */
    public function insert($table, $data)
    {
        if (!empty($data) && is_array($data)) {
            $keys   = '';
            $values = '';            

            $keys   = implode(',', array_keys($data));
            $values = ":" . implode(', :', array_keys($data));

            $sql   = "INSERT INTO " . $table . " (" . $keys . ") VALUES (" . $values . ")";
            $query = $this->pdo->prepare($sql);

            foreach ($data as $key => $val) {
                $query->bindValue(":$key", $val);
            }

            $insertdata = $query->execute();

            if ($insertdata) {
                $lastId = $this->pdo->lastInsertId();
                return $lastId;
            } else {
                return false;
            }
        }
    }

//Update Data
    /*
    $sql = "UPDATE tableName SET name=:name, email=:email WHERE id=:id";
    $query = $this->pdo->prepare($sql);
    $query->bindValue(':name', $name);
    $query->bindValue(':email', $email);
    $query->bindValue(':id', $id);
    $query->execute();
     */
    public function update($table, $data, $cond)
    {
        if (!empty($data) && is_array($data)) {
            $keyvalue  = '';
            $whereCond = '';

            $i = 0;
            foreach ($data as $key => $val) {
                $add = ($i > 0) ? ' , ' : ''; //protom bar and + korbey na karon i er man 0, 2nd time korbey.
                $keyvalue .= "$add" . "$key=:$key"; //$key ekoi value print korbey. id=:id
                $i++;
            }

            if (!empty($cond) && is_array($cond)) {
                $whereCond .= " WHERE ";
                $i = 0;
                foreach ($cond as $key => $val) {
                    $add = ($i > 0) ? ' AND ' : ''; //protom bar and + korbey na karon i er man 0, 2nd time korbey.
                    $whereCond .= "$add" . "$key=:$key"; //$key ekoi value print korbey. id=:id
                    $i++;
                }
            }
            $sql = "UPDATE " . $table . " SET " . $keyvalue . $whereCond;

            $query = $this->pdo->prepare($sql);

            foreach ($data as $key => $val) {
                $query->bindValue(":$key", $val);
            }

            foreach ($cond as $key => $val) {
                $query->bindValue(":$key", $val);
            }

            $update = $query->execute();
            return $update ? $query->rowCount() : false;
        } else {
            return false;
        }
    }
//Delete Data
    /*
    $sql = "DELETE FROM tableName WHERE id=:id";
    $query = $this->pdo->prepare($sql);
    $query->bindValue(':id', $id);
    $query->execute();
     */
    public function delete($table, $cond)
    {
        if (!empty($cond) && is_array($cond)) {
            $whereCond .= " WHERE ";
            $i = 0;
            foreach ($cond as $key => $val) {
                $add = ($i > 0)?' AND ':''; //protom bar and + korbey na karon i er man 0, 2nd time korbey.
                //$whereCond .= $add.$key."=$val";
                $whereCond .= "$add"."$key=:$key"; //$key ekoi value print korbey. id=:id
                $i++;
            }
        }
        $sql = "DELETE FROM ".$table.$whereCond;        
        //$delete = $this->pdo->exec($sql);
        //return $delete?true:false;
            
            $query = $this->pdo->prepare($sql);
             
            foreach ($cond as $key => $val) {
                $query->bindValue(":$key", $val);
            }
             $delete = $query->execute();
             return $delete?true:false;
            
            
            
    }
}
