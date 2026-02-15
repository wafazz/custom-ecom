<?php

namespace HUB;

require_once __DIR__ . '/../../config/mainConfig.php';

class PickupHubController
{
    public function __construct()
    {
        // This runs automatically when the controller is instantiated
        if (!is_login()) {
            header("Location: login");
            exit;
        }
    }

    public function dashboard()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $pageName = "Pickup Hub Dashboard";

        $listPickupHub = $conn->query("SELECT * FROM `pickup_hubs`");
        $allHub = $listPickupHub->num_rows;

        $hubStaff = $conn->query("SELECT * FROM `pickup_hub_staff` WHERE `status` = 'active'");
        $allHubStaff = $hubStaff->num_rows;

        //pickup order count
        $sql = "
            SELECT 
                co.id,
                co.session_id,
                co.order_to,
                co.product_var_id,
                co.total_qty,
                co.total_price,
                co.postage_cost,
                co.currency_sign,
                co.country_id,
                co.country,
                co.state,
                co.city,
                co.postcode,
                co.address_2,
                co.address_1,
                co.customer_name,
                co.customer_name_last,
                co.customer_phone,
                co.customer_email,
                co.status,
                co.payment_channel,
                co.payment_code,
                co.payment_url,
                co.ship_channel,
                co.courier_service,
                co.awb_number,
                co.tracking_url,
                co.created_at,
                co.updated_at,
                co.deleted_at,
                co.remark_comment,
                co.tracking_milestone,
                co.to_myr_rate,
                co.myr_value_include_postage,
                co.myr_value_without_postage,
                co.printed_awb,

                ph.hub_code,
                ph.hub_name,
                ph.contact_person,
                ph.phone AS hub_phone,
                ph.email AS hub_email,
                ph.address AS hub_address,
                ph.country AS hub_country,
                ph.state AS hub_state,
                ph.city AS hub_city,
                ph.postcode AS hub_postcode

            FROM `2025_rozeyana`.customer_orders co
            LEFT JOIN `2025_rozeyana`.pickup_hubs ph
                ON co.tracking_milestone = ph.id

            WHERE co.status IN (1,2,3)
            AND co.ship_channel = 'hub_pickup'
            ORDER BY co.created_at DESC
            ";

        $result = $conn->query($sql);
        

        $successPickup = $conn->query("SELECT * FROM `customer_orders` WHERE `tracking_milestone` IS NOT NULL AND `status` = 4 AND `ship_channel` = 'hub_pickup'"); 
        $failed = $conn->query("SELECT * FROM `customer_orders` WHERE `tracking_milestone` IS NOT NULL AND `status` = 6 AND `ship_channel` = 'hub_pickup'"); 
        $pickupsValue = $conn->query("SELECT SUM(myr_value_include_postage) AS pickups_total_value FROM `customer_orders` WHERE `tracking_milestone` IS NOT NULL AND `status` IN(1,2,3,4) AND `ship_channel` = 'hub_pickup'");
        $pickupsValueVal = $pickupsValue->fetch_assoc();

        if ($allHub >= 1) {
            $allHubCount = $allHub;
        } else {
            $allHubCount = 0;
        }

        if ($allHubStaff >= 1) {
            $allHubStaffCount = $allHubStaff;
        } else {
            $allHubStaffCount = 0;
        }

        if($result->num_rows >= 1) {
            $pickupOrderCount = $result->num_rows;
        } else {
            $pickupOrderCount = 0;
        }  
        
        if($successPickup->num_rows >= 1) {
            $successfulPickupCount = $successPickup->num_rows;
        } else {
            $successfulPickupCount = 0;
        }   

        if($failed->num_rows >= 1) {
            $failedPickupCount = $failed->num_rows;
        } else {
            $failedPickupCount = 0;
        }   

        if($pickupsValueVal['pickups_total_value'] > 0) {
            $totalPickupsValue = $pickupsValueVal['pickups_total_value'];
        } else {
            $totalPickupsValue = 0.00;
        }

        require_once __DIR__ . '/../../view/Admin/pickup_hub_dashboard.php';
    }

    public function allPickupHubs()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = getDbConnection();

        $pageName = "All Pickup Hubs";

        $listPickupHub = $conn->query("SELECT * FROM `pickup_hubs` ORDER BY `created_at` DESC");

        require_once __DIR__ . '/../../view/Admin/all_pickup_hubs.php';
    }
}
