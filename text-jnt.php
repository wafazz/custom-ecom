<?php
require_once("config/mainConfig.php");
require_once("config/function.php");
require 'vendor/autoload.php';

$noID = $_GET["id"];

$jnt = createJTShipping($noID);