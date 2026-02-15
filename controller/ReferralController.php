<?php
// controllers/ReferralController.php

class ReferralController {
    public function index($id) {
        $domainURL = getMainUrl();
        $conn = getDbConnection();
    
        if (ctype_digit($id)) {

            $sql = "SELECT * FROM `member` WHERE `id`='$id' AND `status`='1'";
            $result = $conn->query($sql);

            if($result->num_rows < 1){
                echo $result->num_rows;
                ob_start(); // Start output buffering
                header("Location: ".$domainURL."referby/1");
                exit;
            }else{
                $userData = userData($id);
                $_SESSION["referby"] = $id;
                $_SESSION["referName"] = $userData["m_name"];
                header("Location: ".$domainURL);
            }

        } else {
            ob_start(); // Start output buffering
            header("Location: ".$domainURL."referby/1");
            exit;
        }
    }
}