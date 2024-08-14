<?php
class DB
{
    private $conn;
    public $stati;
    /*
    ['query']
    ['query_time']
    ['error']
    */

    function __construct($host, $port, $dbname, $user, $password,$dbtype='mysql', $charset='UTF8')
    {
       try
       {
          $this->conn = new PDO
          (
              $dbtype . ':host=' . $host . ';dbname=' . $dbname . ';charset=' . $charset,
              $user,
              $password,
              [
                  PDO::ATTR_PERSISTENT            => true,
                  PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
                  PDO::MYSQL_ATTR_INIT_COMMAND    => 'SET NAMES ' . $charset
              ]
          );
       }
       catch (PDOException $e)
       {
          exit('Connection failed: '.$e->getMessage());
       }
    }

    // простой запрос, используется для SELECT, в общем где не требуется блокировка
    function query($sql, $params = [])
    {
        //$sql=str_replace('`','"',$sql);

        $this->stati['query']++;

        //Exit($sql.'<pre>'.print_r($params,true).'</pre>');

        try
        {
            $qs=microtime(true);
                             //exit($sql);
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            $qe=microtime(true)-$qs;
            if(function_exists('bcsub'))
            {
               $qe=bcsub(microtime(true),$qs,6);
            }
            $this->stati['query_time']+=$qe;

            return array(
                         'error'=>0,
                         'queryData'=>array($sql, $params),
                         'queryTime'=>$qe,
                         'rows'=>$stmt->rowCount(),
                         'last_id'=>(mb_substr($sql,0,6)=='INSERT'?$this->conn->lastInsertId():''),
                         'data'=>(mb_substr($sql,0,6)=='SELECT'?$stmt->fetchAll(PDO::FETCH_ASSOC):'')
                        );
        }
        catch (PDOException $e)
        {
            $this->stati['error'][]=array($sql, $e->getMessage());

            //echo "Query failed: " . $e->getMessage();
            return array(
                         'error'=>1,
                         'comment'=>$e->getMessage()
                        );
        }
    }

    function close()
    {
        $this->conn = null;
    }
}

$GLOBALS['DB'] = new DB(CFG_DB_HOST, CFG_DB_PORT, CFG_DB_BASE, CFG_DB_LOGIN, CFG_DB_PASS);
?>