<?php
include "01-header.php";
include "01-menu.php";
?>



<!-- End Navbar -->
<div class="container-fluid py-4">
    <div class="row">

    </div>
    <div class="row my-4">
        <div class="col-lg-12 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>Shipping Cost</h6>
                        </div>

                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                <button class="open-btn" onclick="openModal()">Add New / Update</button>

                    <div style="display: block;
    margin-left: 20px;
    margin-right: 20px;
    margin-bottom: 20px;overflow-x: auto;">
                        <style>
                            thead th {
                                text-align: center !important;
                            }

                            #categoryTable td {
                                text-align: center;
                                vertical-align: top;
                            }

                            /* EXCEPTION: left-align the "name" column (2nd column) */
                            #categoryTable td:nth-child(2) {
                                text-align: left;
                                vertical-align: top;
                            }
                        </style>
                        <table id="categoryTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Country</th>
                                    <th class="text-center">Shipping Zone</th>
                                    <th class="text-center">First Kilo</th>
                                    <th class="text-center">Next Kilo's</th>
                                    <th class="text-center">Last Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_array($result)) {
                                    $dataCountry = getCountryP($row["country_id"]);
                                    ?>
                                    <tr>
                                        <td>#<?= $row["country_id"] ?> - <?= $dataCountry["name"] ?></td>
                                        <td>
                                            <?php 
                                            if($dataCountry["name"] == "Malaysia" AND $row["shipping_zone"] == "1"){
                                                echo "West Malaysia";
                                            }else if($dataCountry["name"] == "Malaysia" AND $row["shipping_zone"] == "2"){
                                                echo "East Malaysia";
                                            }else{
                                                echo "No zone, apply to all in ".$dataCountry["name"];
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center"><?= $dataCountry["sign"] ?> <?= $row["first_kilo"] ?></td>
                                        <td class="text-center"><?= $dataCountry["sign"] ?> <?= $row["next_kilo"] ?></td>
                                        <td class="text-center"><?= $row["updated_at"] ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>

                        <script>
                            $(document).ready(function () {
                                $('#categoryTable').DataTable();
                            });
                        </script>


                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php
    include "01-footer.php";
    ?>