<?php
/**
 * @author Benjamin Cizej, Moja koda d.o.o.
 */

use Mojakoda\MySQL\Script;

/**
 * Class ModelExtensionPaymentLeanpayDatabase
 *
 * @property DB $db
 */
class ModelExtensionPaymentLeanpayDatabase extends Model
{
    public function installTables()
    {
        $script = new Script(DIR_SYSTEM . 'library/leanpay/sql/install.sql');
        $script->execute($this->db);
    }

    public function uninstallTables()
    {
        $script = new Script(DIR_SYSTEM . 'library/leanpay/sql/uninstall.sql');
        $script->execute($this->db);
    }
}
