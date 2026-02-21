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
                                foreach ($postageCosts as $row) {
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

    <!-- COD Charges Section -->
    <div class="row my-4">
        <div class="col-lg-12 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>COD Charges (Benchmark)</h6>
                            <p class="text-xs text-secondary mb-0">If sales &lt; benchmark amount → charge COD Fee 1 | If sales &gt;= benchmark amount → charge COD Fee 2</p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <button class="open-btn" onclick="openCodModal()">Add / Update COD Charge</button>

                    <div style="display:block; margin:0 20px 20px; overflow-x:auto;">
                        <table id="codTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center">Country</th>
                                    <th class="text-center">Shipping Zone</th>
                                    <th class="text-center">Benchmark Amount</th>
                                    <th class="text-center">COD Fee (Below)</th>
                                    <th class="text-center">COD Fee (Above/Equal)</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($codCharges as $codRow) {
                                    $codCountry = getCountryP($codRow["country_id"]);
                                    $zoneName = '';
                                    if ($codCountry["name"] == "Malaysia" && $codRow["shipping_zone"] == "1") {
                                        $zoneName = "West Malaysia";
                                    } else if ($codCountry["name"] == "Malaysia" && $codRow["shipping_zone"] == "2") {
                                        $zoneName = "East Malaysia";
                                    } else {
                                        $zoneName = "All " . $codCountry["name"];
                                    }
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $codCountry["name"] ?></td>
                                        <td class="text-center"><?= $zoneName ?></td>
                                        <td class="text-center"><?= $codCountry["sign"] ?> <?= number_format($codRow["benchmark_amount"], 2) ?></td>
                                        <td class="text-center"><?= $codCountry["sign"] ?> <?= number_format($codRow["cod_fee_below"], 2) ?></td>
                                        <td class="text-center"><?= $codCountry["sign"] ?> <?= number_format($codRow["cod_fee_above"], 2) ?></td>
                                        <td class="text-center">
                                            <a href="<?= $domainURL ?>delete-cod-charge/<?= $codRow["id"] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this COD charge?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>

                        <script>
                            $(document).ready(function () {
                                $('#codTable').DataTable();
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- COD Modal -->
    <div id="codModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-btn" onclick="document.getElementById('codModal').style.display='none'">&times;</span>
            <h4>Add / Update COD Charge</h4>
            <form class="popup-form" action="<?= $domainURL ?>save-cod-charge" method="post">
                Country
                <select name="country_id" class="form-control" required style="margin-bottom:15px;" id="codCountrySelect">
                    <option value="" selected disabled>Select country</option>
                    <?php
                    foreach ($countries as $rc) {
                    ?>
                        <option value="<?= $rc["id"] ?>" data-sign="<?= $rc["sign"] ?>"><?= $rc["name"] ?></option>
                    <?php
                    }
                    ?>
                </select>

                <div id="codZoneDiv" style="display:none;">
                    Shipping Zone
                    <select name="shipping_zone" id="codShippingZone" class="form-control" style="margin-bottom:15px;">
                        <option value="1">West Malaysia</option>
                        <option value="2">East Malaysia</option>
                    </select>
                </div>

                Benchmark Amount (<span id="codCurs1">RM</span>)
                <input type="number" class="form-control" name="benchmark_amount" min="0" step="0.01" placeholder="e.g. 100.00" required style="margin-bottom:15px;">

                COD Fee if sales &lt; benchmark (<span id="codCurs2">RM</span>)
                <input type="number" class="form-control" name="cod_fee_below" min="0" step="0.01" placeholder="e.g. 10.00" required style="margin-bottom:15px;">

                COD Fee if sales &gt;= benchmark (<span id="codCurs3">RM</span>)
                <input type="number" class="form-control" name="cod_fee_above" min="0" step="0.01" placeholder="e.g. 8.00" required style="margin-bottom:15px;">

                <button class="btn btn-primary" type="submit">Save</button>
            </form>
        </div>
    </div>

    <script>
        function openCodModal() {
            document.getElementById('codModal').style.display = 'block';
        }
        document.getElementById('codCountrySelect').addEventListener('change', function() {
            var sign = this.options[this.selectedIndex].getAttribute('data-sign');
            document.getElementById('codCurs1').innerText = sign;
            document.getElementById('codCurs2').innerText = sign;
            document.getElementById('codCurs3').innerText = sign;
            // Show zone dropdown only for Malaysia (country_id=1)
            if (this.value == '1') {
                document.getElementById('codZoneDiv').style.display = 'block';
            } else {
                document.getElementById('codZoneDiv').style.display = 'none';
                document.getElementById('codShippingZone').value = '1';
            }
        });
        window.addEventListener('click', function(e) {
            var m = document.getElementById('codModal');
            if (e.target == m) m.style.display = 'none';
        });
    </script>

    <?php
    include "01-footer.php";
    ?>