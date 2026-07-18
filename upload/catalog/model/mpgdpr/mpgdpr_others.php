<?php
/*
 * Author: ModulePoints
 * any other developer can modify this file freely. to anonymoise customer other data store on other database tables, apart from opencart core customer table and extension tables. or opencart core customer table by adding new columns/fields
*/
class ModelMpGdprMpgdprOthers extends Model {
    /*
     * add other customer data anonymouse table queires here.
     * [example sql]
     * [$this->db->query("UPDATE " . DB_PREFIX . "sample_table SET customer_data='". $this->mpgdpr->anonymouse('any other customer data') ."' WHERE unique_id='" . (int)1 . "'")]
     * follow example sql to call anonymouse function of extension.
     * $customer_data_query->row; This contain customer data like firstname, lastname, email, telephone, fax, cart, wishlist, custom_field, ip. data can be missing as per opencart version
     * [access to $customer_data_query]
     * $customer_data_query->row['firstname'] - give firstname of customer: john
     * $customer_data_query->row['lastname'] - give firstname of customer: doe
     * $customer_data_query->row['email'] - give firstname of customer: johndoe@example.com

    */
    public function anonymouseCustomerOtherData($customer_id, $customer_data_query) {

        $this->mpgdpr->log("called front model.mpgdpr_others.anonymouseCustomerOtherData({$customer_id}, customer_data_query)");

    }
}