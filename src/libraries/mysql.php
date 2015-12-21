<?php

namespace Xiaoju\Beatles\Utils;

use Xiaoju\Beatles\Utils as Utils;

/**
 * Created by PhpStorm.
 *
 * pdo 操作
 * User: xingmin
 * Date: 2014/12/30
 * Time: 14:32
 */
class Mysql
{

    const INVALIDATE_ARGUMENTS = 'invalidate arguments';
    const CHAR_SET_UTF8 = 'utf8';
    const CHAR_SET_GBK = 'gbk';
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT = '3306';
    const EXCEPTION_CODE_UNKNOWN_ACTION = 10001;
    const  EXCEPTION_CODE_OTHER = 99999;

    const EXCEPTION_CODE_SERVER_BEGIN = 1000;
    const EXCEPTION_CODE_SERVER_END = 1999;

    const EXCEPTION_CODE_CLIENT_BEGIN = 2000;
    const EXCEPTION_CODE_CLIENT_END = 2999;

    const ERR_CODE_SQL_DUP_CODE = 1062;


    private $allowCharSet = array(
        self::CHAR_SET_UTF8,
        self::CHAR_SET_GBK
    );


    private $pdo = null;

    public function __construct($config)
    {
        $dsn = 'mysql:';
        if (isset($config['host'])) {
            if (!is_string($config['host'])) {
                throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':host');
            }
            $dsn .= 'host=' . $config['host'];
        } else {
            $dsn .= 'host=' . self::DEFAULT_HOST;
        }

        if (isset($config['port'])) {
            if (!is_numeric($config['port']) || intval($config['port']) != $config['port']) {
                throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':port');
            }
            $dsn .= ';port=' . $config['port'];
        } else {
            $dsn .= ';port=' . self::DEFAULT_PORT;
        }

        if (isset($config['dbname'])) {
            if (!is_string($config['dbname'])) {
                throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':dbname');
            }
            $dsn .= ';dbname=' . $config['dbname'];
        }

        $user = '';
        if (isset($config['user'])) {
            if (!is_string($config['user'])) {
                throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':user');
            }
            $user = $config['user'];
        }

        $password = '';
        if (isset($config['password'])) {
            if (!is_string($config['password'])) {
                throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':password');
            }
            $password = $config['password'];
        }

        $charSet = self::CHAR_SET_UTF8;
        if (isset($config['charSet'])) {
            if (!is_string($config['charSet'])) {
                throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':charSet');
            }

            $charSet = strtolower($config['charSet']);
            if (!in_array($charSet, $this->allowCharSet)) {
                throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':charSet');
            }
        }
        $options = array(
            \PDO::ATTR_PERSISTENT => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false, //通过sql模板传递参数，防止注入
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $charSet
        );

        try {
            $this->pdo = new \PDO($dsn, $user, $password, $options);
            $this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = $e->getCode();
            self::log('fatal', $message . ' errcode:' . $code, 0, $config);

            if (!$this->isInternalCode($code)) {
                $code = self::EXCEPTION_CODE_OTHER;
            }
            throw new Utils\UtilsException("sql connect err, $message", $code);
        }
    }

    private function isInternalCode($code)
    {
        return (($code >= self::EXCEPTION_CODE_CLIENT_BEGIN && $code <= self::EXCEPTION_CODE_CLIENT_END) ||
            ($code >= self::EXCEPTION_CODE_SERVER_BEGIN && $code <= self::EXCEPTION_CODE_SERVER_END));
    }

    private function filter($info)
    {
        return array_keys($info);
    }

    private function cleanup($bind)
    {
        if (!is_array($bind)) {
            if (!empty($bind)) {
                $bind = array($bind);
            } else {
                $bind = array();
            }
        }
        return $bind;
    }

    public static function isLogicError($code)
    {
        return $this->isInternalCode($code) && (!self::isSystemError($code));
    }

    public static function isSystemError($code)
    {
        $systemErrCode = array(
            1021, 1037, 1038, 1039, 1040, 1041, 1042, 1043, 1044, 1045,
            1053, 1081, 1094, 1095, 1105, 1119, 1122, 1123, 1124, 1125,
            1126, 1127, 1128, 1129, 1130, 1131, 1132, 1135, 1152, 1155,
            1156, 1157, 1158, 1159, 1160, 1161, 1184, 1193, 1197, 1198,
            1199, 1200, 1201, 1202, 1203, 1218, 1219, 1227, 1235, 1244,
            1251, 1254, 1255, 1256, 1257, 1258, 1259, 1274, 1275, 1285,
            2000, 2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009,
            2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019,
            2020, 2021, 2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029,
            2030, 2031, 2032, 2033, 2034, 2035, 2036, 2037, 2038, 2039,
            2040, 2041, 2042, 2043, 2044, 2045, 2046, 2047, 2048, 2049,
            2050, 2051, 2052, 2053, 2054, 2055, 2056, 2057, 2058, 2059,
        );
        return in_array($code, $systemErrCode);
    }

    public static function isDupKeyError($code)
    {
        return $code === self::ERR_CODE_SQL_DUP_CODE;
    }

    public function exec($statement)
    {
        if (!is_null($this->pdo)) {
            return $this->pdo->exec($statement);
        } else {
            return 0;
        }
    }

    public function run($sql, $bind = "", $action = "")
    {
        if (!is_string($sql) || empty($sql)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':sql');
        }

        if (!is_string($bind) && !is_array($bind)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':bind');
        }

        $sql = trim($sql);
        $bind = $this->cleanup($bind);

        $params = array(
            'sql' => $sql,
        );

        $params = array_merge($params, $bind);
        $prepareSuccess = false;
        try {
            $pdoStmt = $this->pdo->prepare($sql);

            $prepareSuccess = true;
            $return = true;
            $t1 = microtime(true);
            $result = $pdoStmt->execute($bind);
            $t2 = microtime(true);
            $params['duration'] = $t2 - $t1;
            if ($result !== false) {
                self::log('warning', 'execute ok', 0, $params);
                if (preg_match("/^(" . implode("|", array("select", "describe", "pragma")) . ") /i", $sql)) {
                    $return = $pdoStmt->fetchAll(\PDO::FETCH_ASSOC);
                } elseif (preg_match("/^(" . implode("|", array("delete", "insert", "update")) . ") /i", $sql)) {
                    $return = $pdoStmt->rowCount();
                }
            } else {
                self::log('fatal', 'execute failed', 0, $params);
            }
            $pdoStmt->closeCursor();
            return $return;
        } catch (\PDOException $e) {
            self::log('fatal', $e->getMessage() . ' errcode:' . $e->getCode(), 0, $params);
            $errInfo = array();
            if ($prepareSuccess && $pdoStmt) {
                $errInfo = $pdoStmt->errorInfo();
            } else {
                $errInfo = $this->pdo->errorInfo();
            }
            $code = self::EXCEPTION_CODE_OTHER;
            if (!is_string($action) || empty($action)) {
                $code = self::EXCEPTION_CODE_UNKNOWN_ACTION;
            } else {
                if (is_integer($errInfo[1]) && $this->isInternalCode($errInfo[1])) {
                    $code = $errInfo[1];
                }
            }
            throw new Utils\UtilsException("sql $action err, $errInfo[2]", $code);
        }
    }

    /**
     *
     * @param $table string
     * @param $info array
     */
    public function insert($table, $info)
    {
        if (!is_array($info)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':info');
        }

        if (!is_string($table)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':table');
        }

        $fields = $this->filter($info);
        $sql = "INSERT INTO " . $table . " (" . implode($fields, ", ") . ") VALUES (:" . implode($fields, ", :") . ");";
        $bind = array();
        foreach ($fields as $field) {
            $bind[":$field"] = $info[$field];
        }
        return $this->run($sql, $bind, 'insert');
    }

    public function select($table, $where = "", $bind = "", $fields = "*")
    {
        if (!is_string($table)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':table');
        }

        if (!is_string($where)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':where');
        }

        if (!is_string($bind) && !is_array($bind)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':bind');
        }

        if (!is_string($fields)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':$fields');
        }

        $sql = "SELECT " . $fields . " FROM " . $table;
        if (!empty($where)) {
            $sql .= " WHERE " . $where;
        }
        $sql .= ";";
        return $this->run($sql, $bind, 'select');
    }

    public function update($table, $info, $where, $bind = "")
    {
        if (!is_string($table)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':table');
        }

        if (!is_string($where)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':where');
        }

        if (!is_array($info)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':info');
        }

        if (!is_string($bind) && !is_array($bind)) {
            throw new \InvalidArgumentException(self::INVALIDATE_ARGUMENTS . ':bind');
        }

        $fields = $this->filter($info);
        $fieldSize = sizeof($fields);

        $sql = "UPDATE " . $table . " SET ";
        for ($f = 0; $f < $fieldSize; ++$f) {
            if ($f > 0) {
                $sql .= ", ";
            }
            $sql .= $fields[$f] . " = :update_" . $fields[$f];
        }
        $sql .= " WHERE " . $where . ';';

        $bind = $this->cleanup($bind);
        foreach ($fields as $field) {
            $bind[":update_$field"] = $info[$field];
        }

        return $this->run($sql, $bind, 'update');
    }

    private static function log($type, $msg, $errorNo, $params)
    {
        if (class_exists('Xiaoju\Beatles\Utils\Logger')) {
            switch ($type) {
                case 'debug':
                    Logger::debug($msg, $errorNo, $params);
                    break;
                case 'trace':
                    Logger::trace($msg, $errorNo, $params);
                    break;
                case 'notice':
                    Logger::notice($msg, $errorNo, $params);
                    break;
                case 'warning':
                    Logger::warning($msg, $errorNo, $params);
                    break;
                case 'fatal':
                    Logger::fatal($msg, $errorNo, $params);
                    break;
            }
        }
    }
}
