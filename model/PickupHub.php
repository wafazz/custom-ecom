<?php

require_once __DIR__ . '/BaseModel.php';

class PickupHub extends BaseModel
{
    protected $table = 'pickup_hubs';

    public function getAll()
    {
        $sql = "SELECT * FROM `pickup_hubs`";
        return $this->query($sql);
    }

    public function getAllOrdered()
    {
        $sql = "SELECT * FROM `pickup_hubs` ORDER BY `created_at` DESC";
        return $this->query($sql);
    }

    public function countAll()
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `pickup_hubs`";
        $rows = $this->query($sql);
        return (int) ($rows[0]['cnt'] ?? 0);
    }

    public function countActiveStaff()
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `pickup_hub_staff` WHERE `status` = 'active'";
        $rows = $this->query($sql);
        return (int) ($rows[0]['cnt'] ?? 0);
    }

    public function getHubPickupOrders()
    {
        $sql = "
            SELECT
                co.id, co.session_id, co.order_to, co.product_var_id, co.total_qty, co.total_price,
                co.postage_cost, co.currency_sign, co.country_id, co.country, co.state, co.city,
                co.postcode, co.address_2, co.address_1, co.customer_name, co.customer_name_last,
                co.customer_phone, co.customer_email, co.status, co.payment_channel, co.payment_code,
                co.payment_url, co.ship_channel, co.courier_service, co.awb_number, co.tracking_url,
                co.created_at, co.updated_at, co.deleted_at, co.remark_comment, co.tracking_milestone,
                co.to_myr_rate, co.myr_value_include_postage, co.myr_value_without_postage, co.printed_awb,
                ph.hub_code, ph.hub_name, ph.contact_person, ph.phone AS hub_phone, ph.email AS hub_email,
                ph.address AS hub_address, ph.country AS hub_country, ph.state AS hub_state,
                ph.city AS hub_city, ph.postcode AS hub_postcode
            FROM customer_orders co
            LEFT JOIN pickup_hubs ph ON co.tracking_milestone = ph.id
            WHERE co.status IN (1,2,3) AND co.ship_channel = 'hub_pickup'
            ORDER BY co.created_at DESC
        ";
        return $this->query($sql);
    }

    public function countSuccessPickup()
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `customer_orders` WHERE `tracking_milestone` IS NOT NULL AND `status` = 4 AND `ship_channel` = 'hub_pickup'";
        $rows = $this->query($sql);
        return (int) ($rows[0]['cnt'] ?? 0);
    }

    public function countFailedPickup()
    {
        $sql = "SELECT COUNT(*) AS cnt FROM `customer_orders` WHERE `tracking_milestone` IS NOT NULL AND `status` = 6 AND `ship_channel` = 'hub_pickup'";
        $rows = $this->query($sql);
        return (int) ($rows[0]['cnt'] ?? 0);
    }

    public function pickupsValue()
    {
        $sql = "SELECT SUM(myr_value_include_postage) AS pickups_total_value FROM `customer_orders` WHERE `tracking_milestone` IS NOT NULL AND `status` IN(1,2,3,4) AND `ship_channel` = 'hub_pickup'";
        $rows = $this->query($sql);
        return (float) ($rows[0]['pickups_total_value'] ?? 0);
    }
}
