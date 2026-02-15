<?php
include "01-header.php";
include "01-menu.php";
?>



<div class="container-fluid py-4">
    
    <div class="row">
        <div class="col-lg-12 col-12">
            <div class="row">

            <style>
                .card-link {
                    color:#ffffff;
                    text-decoration: none;
                    display: block;
                }

                .card-link:hover {
                    color:#ccc;
                    font-weight: bold;
                    text-decoration: underline;
                    display: block;
                }
            </style>

                <div class="col-lg-4 col-md-6 col-12 mt-4 mt-md-0 mb-4">
                    <div class="card">
                        <span class="mask bg-info opacity-10 border-radius-lg"></span>
                        <div class="card-body p-3 position-relative">
                            <div class="row">
                                <div class="col-8 text-start">
                                    <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                                        <i class="fa-solid fa-store text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                    <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                        <?= $allHubCount ?>
                                    </h5>
                                    <span class="text-white text-sm">Total Pickup Hub</span>
                                    <a href="<?= $domainURL ?>hub/all-pickup-hubs" class="text-white text-sm card-link">View all <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                                <div class="col-4">
                                    <div class="dropstart text-end mb-6">
                                        <a href="javascript:;" class="cursor-pointer" id="dropdownUsers2" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-h text-white"></i>
                                        </a>
                                        <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers2">
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mt-4 mt-md-0">
                    <div class="card">
                        <span class="mask bg-info opacity-10 border-radius-lg"></span>
                        <div class="card-body p-3 position-relative">
                            <div class="row">
                                <div class="col-8 text-start">
                                    <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                                        <i class="fa-solid fa-users text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                    <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                        <?= $allHubStaffCount ?>
                                    </h5>
                                    <span class="text-white text-sm">Total Hub Staff</span>
                                    <a href="<?= $domainURL ?>hub/staff" class="text-white text-sm card-link">View all <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                                <div class="col-4">
                                    <div class="dropdown text-end mb-6">
                                        <a href="javascript:;" class="cursor-pointer" id="dropdownUsers1" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-h text-white"></i>
                                        </a>
                                        <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers1">
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mt-4 mt-md-0">
                    <div class="card">
                        <span class="mask bg-info opacity-10 border-radius-lg"></span>
                        <div class="card-body p-3 position-relative">
                            <div class="row">
                                <div class="col-8 text-start">
                                    <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                                        <i class="fa-solid fa-book text-dark text-gradient text-lg opacity-10"
                                            aria-hidden="true"></i>
                                    </div>
                                    <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                        <?= $pickupOrderCount ?>
                                    </h5>
                                    <span class="text-white text-sm">Total Orders to Pickup</span>
                                    <a href="<?= $domainURL ?>hub/all-orders" class="text-white text-sm card-link">View all <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                                <div class="col-4">
                                    <div class="dropdown text-end mb-6">
                                        <a href="javascript:;" class="cursor-pointer" id="dropdownUsers3" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-h text-white"></i>
                                        </a>
                                        <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers3">
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-12 mt-4 mt-md-0">
                    <div class="card">
                        <span class="mask bg-secondary opacity-10 border-radius-lg"></span>
                        <div class="card-body p-3 position-relative">
                            <div class="row">
                                <div class="col-8 text-start">
                                    <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                                        <i class="fa-solid fa-money-check-dollar text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                    <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                        RM<?= number_format($totalPickupsValue, 2) ?>
                                    </h5>
                                    <span class="text-white text-sm">Pickups Value</span>
                                    <a href="<?= $domainURL ?>hub/all-pickup-hubs" class="text-white text-sm card-link">View all <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                                <div class="col-4">
                                    <div class="dropstart text-end mb-6">
                                        <a href="javascript:;" class="cursor-pointer" id="dropdownUsers4" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-h text-white"></i>
                                        </a>
                                        <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers4">
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 col-12 mt-4 mt-md-0">
                    <div class="card">
                        <span class="mask bg-success opacity-10 border-radius-lg"></span>
                        <div class="card-body p-3 position-relative">
                            <div class="row">
                                <div class="col-8 text-start">
                                    <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                                        <i class="fa-solid fa-person-circle-check text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                    <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                        <?= $successfulPickupCount ?>
                                    </h5>
                                    <span class="text-white text-sm">Successful Pickups</span>
                                    <a href="<?= $domainURL ?>hub/all-orders?status=success" class="text-white text-sm card-link">View all <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                                <div class="col-4">
                                    <div class="dropstart text-end mb-6">
                                        <a href="javascript:;" class="cursor-pointer" id="dropdownUsers4" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-h text-white"></i>
                                        </a>
                                        <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers4">
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mt-4 mt-md-0">
                    <div class="card">
                        <span class="mask bg-danger opacity-10 border-radius-lg"></span>
                        <div class="card-body p-3 position-relative">
                            <div class="row">
                                <div class="col-8 text-start">
                                    <div class="icon icon-shape bg-white shadow text-center border-radius-2xl">
                                        <i class="fa-solid fa-business-time text-dark text-gradient text-lg opacity-10" aria-hidden="true"></i>
                                    </div>
                                    <h5 class="text-white font-weight-bolder mb-0 mt-3">
                                        <?= $failedPickupCount ?>
                                    </h5>
                                    <span class="text-white text-sm">Failed/Cancelled Pickups</span>
                                    <a href="<?= $domainURL ?>hub/all-orders?status=failed" class="text-white text-sm card-link">View all <i class="fa-solid fa-arrow-right"></i></a>
                                </div>
                                <div class="col-4">
                                    <div class="dropstart text-end mb-6">
                                        <a href="javascript:;" class="cursor-pointer" id="dropdownUsers4" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fa fa-ellipsis-h text-white"></i>
                                        </a>
                                        <ul class="dropdown-menu px-2 py-3" aria-labelledby="dropdownUsers4">
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Another action</a></li>
                                            <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>



    <?php
    include "01-footer.php";
    ?>