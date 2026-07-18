<?php
/**
 * @author Benjamin Cizej, Moja Koda d.o.o.
 */

namespace Mojakoda\MySQL;

use DB;

class Script
{
    protected $file;
    
    public function __construct($file)
    {
        $this->file = $file;
    }
    
    public function execute(DB $database)
    {
        $sql_content = file_get_contents($this->file);
        $sql_content = str_replace('PREFIX_', DB_PREFIX, $sql_content);
        $sql_content = str_replace('DB_ENGINE', 'InnoDB', $sql_content);
        $sql_requests = preg_split("/;\s*[\r\n]+/", $sql_content);
        $result = true;
        foreach($sql_requests as $request) {
            if (!empty($request)) {
                $result &= $database->query(trim($request));
            }
        }

        return $result;
    }
}