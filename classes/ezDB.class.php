<?php

/**
 * A database based on mysqli that can do normal and prepared query.
 * Query debugger included.
 * @version 1.8
 * @author Antoine Aflalo
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright     This file is part of ezDB.
 *
 *   ezDB is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   ezDB is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *  along with ezDB.  If not, see <http://www.gnu.org/licenses/>.
 */
class ezDB {

    protected $user, $pass, $host, $database, $dbWrap, $lastQuery, $numQuerries;
    protected $colInfos, $lastResults, $lastInsertedID, $debugCalled, $debugAll, $lastQueryInfos;
    protected $prepQuery, $boundParams, $affectedRows, $diskCache, $cacheTimeOut, $cachePath, $fromCache;
    public static $instance;

    /**
     * Private contructor (singleton)
     */
    private function __construct() {
        $this->debugAll = false;
        $this->multiQuery = false;
        $this->diskCache = false;
        $this->fromCache = false;
    }

    public function __destruct() {
        unset($this->user, $this->pass, $this->db, $this->host, $this->lastQuery, $this->lastResults);
        if ($this->isConnected())
            $this->dbWrap->close();
        unset($this->dbWrap);
    }

    /**
     * Create a mySQLi connection using params set in the ini file.
     * Ini structure :
     * <code>
     * [mySQL]
     * Hostname = "localhost"
     * Username = "root"
     * Password = "toor"
     * Database = "test"
     * </code>
     * @param string $path path to the ini file
     */
    public function iniFileConnect($path) {
        if (!file_exists($path))
            throw new Exception('ezDB ERROR: iniFileConnect() -> The ini file to be parsed for the connection must exist.');

        $info = parse_ini_file($path, true);
        $info = $info['mySQL'];
        $this->connect($info['Hostname'], $info['Username'], $info['Password'], $info['Database']);
    }

    /**
     * Create the connection to the database
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $db
     */
    public function connect($host, $user, $pass, $db) {
        if ($this->isConnected())
            return false;

        $this->user = $user;
        $this->database = $db;
        $this->pass = $pass;
        $this->host = $host;
        $this->dbWrap = new mysqli($this->host, $this->user, $this->pass, $this->database);
        if (mysqli_connect_error())
            throw new Exception('ezDB ERROR: connect() -> Cant connect to DB : ' . mysqli_connect_error(), mysqli_connect_errno());
    }

    public static function getInstance() {
        if (!isset(self::$instance))
        {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    public function __clone() {
        throw new Exception('ezDB ERROR: __clone() -> Singleton can\'t be duplicated');
    }

    /**
     * To enable the cache of queries.
     * @param string $path path to the dir cache
     * @param double $timeOut before destroying the querie cache in MIN.
     */
    public function enableDiskCache($path, $timeOut=1) {
        $this->diskCache = true;
        $this->changeCacheTimeout($timeOut);
        $this->cachePath = $path;
        if (!is_dir($this->cachePath))
            throw new Exception('ezDB ERROR: enableDiskCache() -> The path (' . $path . ') used is not a valid directory.');
    }
    /**
     * Allow to change the timeOut value on-the-fly
     * @param double $timeOut  before destroying the querie cache in MIN.
     */
    public function changeCacheTimeout($timeOut) {
        if ($this->diskCache)
             if (is_numeric($timeOut) && $timeOut!=0)
                $this->cacheTimeOut = $timeOut;
            else
                throw new Exception('ezDB ERROR: changeCacheTimeout() -> The timeout value must be a non-null number.');
        else
            throw new Exception('ezDB ERROR: changeCacheTimeout() -> The Disk Cache is not activated, do it before change the timeout.');
    }

    /**
     * Used to cache the query results on the disk.
     */
    protected function setCacheFile() {
        if (preg_match("/^(select)\s+/i", $this->lastQuery))
        {
            $params = '';
            if (is_array($this->boundParams) && isset($this->boundParams[0]))
                $params = serialize($this->boundParams);
            $hash = 'ezDB_' . sha1($this->lastQuery . $params);
            $cacheFile = $this->cachePath . '/' . $hash;
            if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) > ($this->cacheTimeOut * 60))
                unlink($cacheFile);

            $infoToCache = array(
                'affectedRows' => $this->affectedRows,
                'colInfos' => $this->colInfos,
                'lastResults' => $this->lastResults
            );
            error_log(serialize($infoToCache), 3, $this->cachePath . '/' . $hash);
        }
    }

    /**
     * Get the info stored in the cache and if the file is older than the timeout
     * we delete it.
     * @return bool
     */
    protected function getCacheFile() {
        $params = '';
        if (is_array($this->boundParams) && isset($this->boundParams[0]))
            $params = serialize($this->boundParams);
        $hash = 'ezDB_' . sha1($this->lastQuery . $params);
        $cacheFile = $this->cachePath . '/' . $hash;
        if (file_exists($cacheFile))
        {
            if ((time() - filemtime($cacheFile)) > ($this->cacheTimeOut * 60))
            {
                unlink($cacheFile);
                $this->fromCache = false;
                return false;
            } else
            {
                $cacheResult = unserialize(file_get_contents($cacheFile));
                $this->affectedRows = $cacheResult['affectedRows'];
                $this->colInfos = $cacheResult['colInfos'];
                $this->lastResults = $cacheResult['lastResults'];
                $this->fromCache = true;
                if ($this->debugAll)
                    $this->debugReport();
                return true;
            }
        } else
        {
            $this->fromCache = false;
            return false;
        }
    }

    /**
     * Display the debug report
     * @param bool $echo
     */
    public function debugReport($echo=true) {
        if (!$echo)
            ob_start();

        echo "<blockquote>";

        if (!$this->debugCalled)
        {
            echo "<font color=800080 face=arial size=2><b>ezDB</b> <b>Debug..</b></font><p>\n";
        }

        if ($this->dbWrap->error)
        {
            echo "<font face=arial size=2 color=000099><b>Last Error --</b> [<font color=000000><b>{$this->dbWrap->error}</b></font>]<p>";
        }
        $prep = '';
        if ($this->prepQuery)
            $prep = 'Prepared ';
        else if ($this->multiQuery)
            $prep = 'Multi-';
        if ($this->fromCache)
            $prep.= 'From Cache :';

        echo "<font face=arial size=2 color=000099><b>$prep Query</b> [{$this->numQuerries}] <b>--</b> ";
        echo "[<font color=#999><b>{$this->lastQuery}</b></font>]</font><p>";
        if ($this->prepQuery)
        {
            echo "Type and value : [<font color=#999 face=arial size=2><b> ";
            foreach ($this->boundParams as $param)
                echo $param . ', ';
            echo "</b></font>]</font></p><p>";
        }
        if (!empty($this->lastQueryInfos))
            echo "<font face=arial size=2 color=000099><b>MySQL Query Info : </b></font>[<font color=#999>{$this->lastQueryInfos}</font>]</p><p>";
        else
            echo "<font face=arial size=2 color=000099><b>MySQL Query affected_rows : </b></font>[<font color=#999>{$this->affectedRows}</font>]</p><p>";

        echo "<font face=arial size=2 color=000099><b>Query Result :</b></font>";
        echo "<blockquote>";

        if (!empty($this->colInfos))
        {

            echo "<table cellpadding=5 cellspacing=1 bgcolor=555555>";
            echo "<tr bgcolor=eeeeee><td nowrap valign=bottom><font color=555599 face=arial size=2><b>(row)</b></font></td>";

            foreach ($this->colInfos as $col)
                for ($i = 0; $i < count($col); $i++)
                {
                    echo "<td nowrap align=left valign=top><font size=1 color=555599 face=arial>{$col[$i]->type} {$col[$i]->max_length}</font><br><span style='font-family: arial; font-size: 10pt; font-weight: bold;'>{$col[$i]->name}</span></td>";
                }

            echo "</tr>";


            if (!empty($this->lastResults))
            {

                $i = 0;
                foreach ($this->getResults(false, false) as $selectedRow)
                {
                    $i++;
                    echo "<tr bgcolor=ffffff><td bgcolor=eeeeee nowrap align=middle><font size=2 color=555599 face=arial>$i</font></td>";

                    foreach ($selectedRow as $item)
                    {
                        echo "<td nowrap><font face=arial size=2>$item</font></td>";
                    }

                    echo "</tr>";
                }
            } else
                echo "<tr bgcolor=ffffff><td colspan=" . (count($this->colInfos) + 1) . "><font face=arial size=2>No Results</font></td></tr>";


            echo "</table>";
        }
        else if (!empty($this->lastInsertedID))
            echo "<tr bgcolor=ffffff><td colspan=" . (count($this->colInfos) + 1) . "><font face=arial size=2>Last inserted ID :{$this->lastInsertedID}</font></td></tr>";
        else
        {
            echo "<font face=arial size=2>No Results</font>";
        }

        echo "</blockquote></blockquote>";

        $this->debugCalled = true;
        if (!$echo)
        {
            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }
    }

    /**
     * Trace all the query by lauching debug() after each call
     * @param bool $flag
     */
    public function setTrace($flag=true) {
        if ($flag === true || $flag === false)
            $this->debugAll = $flag;
    }

    /**
     * Last inserted id when you do a SQL insert.
     * @return int
     */
    public function getLastID() {
        return $this->lastInsertedID;
    }

    /**
     * Test if there is a connection.
     * @return bool
     */
    public function isConnected() {
        return!is_null($this->dbWrap);
    }

    /**
     * Function to be used for a prepared query to bind parameters.
     * Example of array to use:
     * <code>
     * <?php
     * array('si',$string,$integer);
     * ?>
     * </code>
     * @param mysqli_stmt $stmt
     * @return int
     */
    protected function bindParams(&$stmt) {
        $i = 1;
        $array[] = $this->boundParams[0];
        $max = count($this->boundParams);
        while ($i < $max)
        {
            $$i = $this->boundParams[$i];
            $array[] = &${$i};
            $i++;
        }
        return call_user_func_array(array($stmt, "bind_param"), $array);
    }

    /**
     * Function used in pQuery to bind the results
     * @param mysqli_stmt $obj
     * @param array $bindResults
     */
    protected function bindResults(&$obj, &$bindResults) {
        return call_user_func_array(array($obj, "bind_result"), $bindResults);
    }

    /**
     * Trasform an array with objects to an associative array
     * @param array $arrayObjs
     * @return array
     */
    protected function objsToAssocArray($arrayObjs) {
        $array = array();
        foreach ($arrayObjs as $row)
            $array[] = get_object_vars($row);

        return $array;
    }

    /**
     * Query for a prepared query.
     * @param string $query the query to be prepared and exec
     * @param associative array $bindParams see function bindParams
     */
    public function preparedQuery($query, $bindParams) {
        if (!$this->isConnected())
            throw new Exception('ezDB ERROR: preparedQuery() -> To do a query, you must be connected to a SQL database.');

        $this->lastQuery = trim($query);
        $this->lastResults = array();
        $this->prepQuery = true;
        $this->multiQuery = false;
        $this->colInfos = array();
        $this->numQuerries++;
        if (!is_array($bindParams))
            throw new Exception('ezDB ERROR: preparedQuery() -> $bindParams must be an array');

        $this->boundParams = $bindParams;

        if ($this->diskCache && $this->getCacheFile())
            return $this->affectedRows;

        $stmt = $this->dbWrap->stmt_init();
        if ($stmt->prepare($this->lastQuery) === false)
        {
            throw new Exception('ezDB ERROR: preparedQuery() -> Preparation of the query ERROR : ' . $stmt->error, $stmt->errno);
            $stmt->close();
        }

        $this->bindParams($stmt);

        if ($stmt->execute())
        {
            if (preg_match("/^(insert|replace)\s+/i", $this->lastQuery))
            {
                $this->lastInsertedID = $this->dbWrap->insert_id;
                $this->affectedRows = $stmt->affected_rows;
            }
            else if (preg_match("/^select\s+/i", $this->lastQuery))
            {
                $stmt->store_result();

                $this->affectedRows = $stmt->num_rows;

                $result = $stmt->result_metadata();
//Used for the debug
                $this->colInfos[] = $result->fetch_fields();

                $fieldsName = array();
//recuperation of the fields names
//and now create var for binding result, using the name of the choosen field
//Pushing all the adress of the var to a vector, used by bindResults
                foreach ($this->colInfos[0] as $field)
                {
                    $fieldName[] = $field->name;
                    $bindResults[] = &${$field->name};
                }
                $result->close();
                $this->bindResults($stmt, $bindResults);

//now just have to fetch, and the vars will be update for each row
                $i = 0;
                while ($stmt->fetch())
                {
                    $this->lastResults[$i] = new stdClass();
                    foreach ($fieldName as $field)
                        $this->lastResults[$i]->{$field} = $$field;
                    $i++;
                }
            }


            $this->lastQueryInfos = $this->dbWrap->info;
            $stmt->close();

            if ($this->debugAll)
                $this->debugReport();

            if ($this->diskCache && !$this->fromCache)
                $this->setCacheFile();

            return $this->affectedRows;
        } else
        {
            throw new Exception('ezDB ERROR: preparedQuery() -> Execution of the query ERROR : ' . $stmt->error, $stmt->errno);
            $stmt->close();
        }
    }

    /**
     * Alias to preparedQuery
     * @param string $query
     * @param asscociative array $bindParams
     * @return int
     */
    public function pQuery($query, $bindParams) {
        return $this->preparedQuery($query, $bindParams);
    }

    /**
     * Used to do multiple queries
     * @param string $sql
     * @return int
     */
    public function mQuery($sql) {
        if (!$this->isConnected())
            throw new Exception('ezDB ERROR: mQuery() -> To do a query, you must be connected to a SQL database.');

        $this->lastQuery = trim($sql);
        $this->lastResults = array();
        $this->colInfos = array();
        $this->prepQuery = false;
        $this->multiQuery = true;

        if ($this->diskCache && $this->getCacheFile())
            return $this->affectedRows;

        $result = $this->dbWrap->multi_query($this->lastQuery);
        if ($this->dbWrap->error)
            throw new Exception('ezDB ERROR: mQuery() -> multi_query ERROR :' . $this->dbWrap->error, $this->dbWrap->errno);

        while(true)
        {
            $result = $this->dbWrap->store_result();
            if ($result !== false)
            {

                //Used for the debug            
                $this->colInfos[] = $result->fetch_fields();

                //Store all the row
                while ($row = $result->fetch_object())
                {
                    $this->lastResults[] = $row;
                }
            }
            $this->numQuerries++;
            if ($this->dbWrap->more_results())
                $this->dbWrap->next_result();
            else
                break;
        }
        $this->lastQueryInfos = $this->dbWrap->info;

        if ($this->debugAll)
            $this->debugReport();
        $this->affectedRows = $this->dbWrap->affected_rows;

        if ($this->diskCache && !$this->fromCache)
            $this->setCacheFile();

        return $this->affectedRows;
    }

    /**
     * Parse and execute the sql request
     * @param string $sql SQL request
     */
    public function query($sql) {
        if (!$this->isConnected())
            throw new Exception('ezDB ERROR: query() -> To do a query, you must be connected to a SQL database.');

        $this->lastQuery = trim($sql);
        $this->lastResults = array();
        $this->colInfos = array();
        $this->prepQuery = false;
        $this->multiQuery = false;
        $this->numQuerries++;

        if ($this->diskCache && $this->getCacheFile())
            return $this->affectedRows;

        $result = $this->dbWrap->query($this->lastQuery);
        if ($this->dbWrap->error)
            throw new Exception('ezDB ERROR: query() -> query ERROR :' . $this->dbWrap->error, $this->dbWrap->errno);

        if (preg_match("/^(insert|replace)\s+/i", $this->lastQuery))
            $this->lastInsertedID = $this->dbWrap->insert_id;
        else if (preg_match("/^(select|show)\s+/i", $this->lastQuery))
        {
//Used for the debug            
            $this->colInfos[] = $result->fetch_fields();

//Store all the row
            while ($row = $result->fetch_object())
            {
                $this->lastResults[] = $row;
            }

            $result->close();
        }
        $this->lastQueryInfos = $this->dbWrap->info;

        if ($this->debugAll)
            $this->debugReport();
        $this->affectedRows = $this->dbWrap->affected_rows;

        if ($this->diskCache && !$this->fromCache)
            $this->setCacheFile();

        return $this->affectedRows;
    }

    /**
     * Results wrapper, cant return the result with an object or an associative array
     * @param string $query
     * @param bool $object
     * @return mixed
     */
    public function getResults($query=false, $object=true) {
        if ($query !== false)
        {
            $this->query($query);
        }
        if ($object === true)
            return $this->lastResults;
        else
            return $this->objsToAssocArray($this->lastResults);
    }

    /**
     * Return the selected row in the result.
     * @param string $query
     * @param bool $object
     * @param int $offset
     * @return mixed
     */
    public function getRow($query=false, $object=true, $offset=0) {
        if ($query !== false)
        {
            $this->query($query);
        }
        if ($object === true)
            return isset($this->lastResults[$offset]) ? $this->lastResults[$offset] : null;
        else
            return isset($this->lastResults[$offset]) ? get_object_vars($this->lastResults[$offset]) : null;
    }

    /**
     * Get the value of the SQL request
     * @param string $query
     * @return mixed
     */
    public function getVar($query=false) {
        if ($query !== false)
        {
            $this->query($query);
        }
        if (isset($this->lastResults[0]))
        {
            $values = array_values(get_object_vars($this->lastResults[0]));
            return!empty($values[0]) ? $values[0] : null;
        }
        else
            return null;
    }
    public function get_var($query=false){
        return $this->getVar($query);
    }
    public function get_results($query=false, $object=false) {
         return $this->getResults($query, $object);
     }
    public function get_row($query=false, $object=false, $offset=0) {
        return $this->getRow($query, $object, $offset);
    }

    public function disableDiskCache() {
        $this->diskCache = false;
    }

}

?>
