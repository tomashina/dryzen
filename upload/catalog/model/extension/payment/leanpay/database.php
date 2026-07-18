<?php
/**
 * @author Benjamin Cizej, Moja koda d.o.o.
 */

use Mojakoda\Generator\Uuid;

/**
 * Class ModelExtensionPaymentLeanpayDatabase
 *
 * @property DB $db
 * @property Loader $load
 * @property ModelCheckoutOrder $model_checkout_order
 */
class ModelExtensionPaymentLeanpayDatabase extends Model
{
    protected $tableName = DB_PREFIX . 'mojakoda_leanpay_transactions';

    public function createPayment($orderId, $total, $currencyCode, $currencyValue)
    {
        do {
            $vendorTransactionId = Uuid::uuid4();
        } while ($this->vendorTransactionIdExists($vendorTransactionId));

        $this->db->query(sprintf(
            'INSERT INTO %s (vendor_transaction_id, amount, currency_code, currency_value, order_id)
             VALUES ("%s", %.2f, "%s", %.8f, %d)',
            $this->tableName,
            $vendorTransactionId,
            round($total, 2),
            $currencyCode,
            $currencyValue,
            (int)$orderId
        ));

        return $vendorTransactionId;
    }

    public function vendorTransactionIdExists($vendorTransactionId)
    {
        $query = $this->db->query(sprintf(
            'SELECT vendor_transaction_id FROM %s WHERE vendor_transaction_id = "%s" LIMIT 1',
            $this->db->escape($this->tableName),
            $this->db->escape($vendorTransactionId)
        ));

        return (bool)$query->row;
    }

    public function updateStatus($vendorTransactionId, $status)
    {
        $this->db->query(sprintf(
            'UPDATE %s SET status = "%s" WHERE vendor_transaction_id = "%s"',
            $this->db->escape($this->tableName),
            $status,
            $this->db->escape($vendorTransactionId)
        ));
    }

    public function loadFromVendorTransactionId($vendorTransactionId)
    {
        $query = $this->db->query(sprintf(
            'SELECT * FROM %s WHERE vendor_transaction_id = "%s" LIMIT 1',
            $this->db->escape($this->tableName),
            $this->db->escape($vendorTransactionId)
        ));

        return $query->row;
    }
}
