<?php

namespace HUB;

require_once __DIR__ . '/../../config/mainConfig.php';
require_once __DIR__ . '/../../model/PickupHub.php';

class PickupHubController
{
    private $conn;
    private $hubModel;

    public function __construct()
    {
        if (!is_login()) {
            header("Location: login");
            exit;
        }
        $this->conn = getDbConnection();
        $this->hubModel = new \PickupHub($this->conn);
    }

    public function dashboard()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;

        $pageName = "Pickup Hub Dashboard";

        $allHubCount = $this->hubModel->countAll();
        $allHubStaffCount = $this->hubModel->countActiveStaff();

        $result = $this->hubModel->getHubPickupOrders();
        $pickupOrderCount = count($result);

        $successfulPickupCount = $this->hubModel->countSuccessPickup();
        $failedPickupCount = $this->hubModel->countFailedPickup();
        $totalPickupsValue = $this->hubModel->pickupsValue();

        require_once __DIR__ . '/../../view/Admin/pickup_hub_dashboard.php';
    }

    public function allPickupHubs()
    {
        $domainURL = getMainUrl();
        $mainDomain = mainDomain();
        $conn = $this->conn;

        $pageName = "All Pickup Hubs";

        $listPickupHub = $this->hubModel->getAllOrdered();

        require_once __DIR__ . '/../../view/Admin/all_pickup_hubs.php';
    }
}
