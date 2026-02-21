<?php
// controllers/ReferralController.php

require_once __DIR__ . '/../config/mainConfig.php';
require_once __DIR__ . '/../model/Member.php';

class ReferralController {

    private $domainURL;
    private $memberModel;

    public function __construct()
    {
        $this->domainURL = getMainUrl();
        $conn = getDbConnection();
        $this->memberModel = new Member($conn);
    }

    public function index($id) {
        $domainURL = $this->domainURL;

        if (ctype_digit($id)) {

            $member = $this->memberModel->findActiveById((int) $id);

            if (!$member) {
                ob_start();
                header("Location: ".$domainURL."referby/1");
                exit;
            } else {
                $userData = userData($id);
                $_SESSION["referby"] = $id;
                $_SESSION["referName"] = $userData["m_name"];
                header("Location: ".$domainURL);
            }

        } else {
            ob_start();
            header("Location: ".$domainURL."referby/1");
            exit;
        }
    }
}
