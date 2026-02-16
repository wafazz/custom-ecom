<?php
include "01-header.php";
include "01-menu.php";
?>

<style>
  .order-page-header {
    color: #344767;
    font-weight: 700;
    margin-bottom: 0.25rem;
  }
  .order-page-sub {
    color: #8392ab;
    font-size: 0.85rem;
  }
  .order-stats {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
  }
  .order-stat-pill {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 0.75rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .order-stat-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
  }
  .order-stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
  }
  .order-stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #344767;
    line-height: 1.2;
  }
  .order-stat-label {
    font-size: 0.7rem;
    color: #8392ab;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  /* Filter bar */
  .filter-bar {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 14px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
  }
  .filter-bar label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #344767;
    margin: 0;
  }
  .filter-bar input[type="text"],
  .filter-bar input[type="number"],
  .filter-bar select {
    border: 1px solid #e0e3e7;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.85rem;
    color: #344767;
    outline: none;
    transition: border-color 0.2s;
    height: 36px;
  }
  .filter-bar input:focus,
  .filter-bar select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
  }
  .filter-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 6px 16px;
    font-size: 0.85rem;
    cursor: pointer;
    height: 36px;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: opacity 0.2s;
  }
  .filter-btn:hover { opacity: 0.9; }

  /* Bulk actions */
  .bulk-bar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
  }
  .selected-badge {
    background: #344767;
    color: #fff;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
  }

  /* Order cards */
  .order-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 14px;
    margin-bottom: 1rem;
    overflow: hidden;
    transition: box-shadow 0.2s ease;
  }
  .order-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
  }
  .order-card-header {
    background: #f8f9fa;
    padding: 0.75rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #e9ecef;
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  .order-card-header .customer-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #344767;
  }
  .order-card-header .customer-info .country-badge {
    background: #e3f2fd;
    color: #1565c0;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
  }
  .order-card-header .order-id-badge {
    font-size: 0.8rem;
    color: #667eea;
    font-weight: 700;
  }
  .order-card-body {
    padding: 1rem 1.25rem;
  }
  .order-card-body .order-grid {
    display: grid;
    grid-template-columns: auto 1fr auto auto auto;
    gap: 0 1.5rem;
    align-items: start;
  }
  @media (max-width: 991px) {
    .order-card-body .order-grid {
      grid-template-columns: 1fr;
      gap: 1rem;
    }
  }

  /* Product items */
  .product-list {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
  }
  .product-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 10px;
  }
  .product-item img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e9ecef;
  }
  .product-item .product-info {
    flex: 1;
    min-width: 0;
  }
  .product-item .product-name {
    font-size: 0.82rem;
    font-weight: 600;
    color: #344767;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 220px;
  }
  .product-item .product-sku {
    font-size: 0.7rem;
    color: #8392ab;
  }
  .product-item .product-qty {
    font-size: 0.8rem;
    font-weight: 600;
    color: #344767;
    white-space: nowrap;
  }
  .product-item .product-price {
    font-size: 0.8rem;
    color: #344767;
    text-align: right;
    white-space: nowrap;
  }
  .product-item .product-price b {
    display: block;
    font-size: 0.85rem;
  }

  /* Order meta */
  .order-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  .order-meta-item {
    text-align: center;
  }
  .order-meta-label {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #8392ab;
    margin-bottom: 2px;
  }
  .order-meta-value {
    font-size: 0.9rem;
    font-weight: 700;
    color: #344767;
  }
  .order-total {
    font-size: 1.1rem;
    font-weight: 700;
    color: #344767;
  }
  .payment-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
    margin-top: 2px;
  }
  .payment-badge.senangpay { background: #e8f5e9; color: #2e7d32; }
  .payment-badge.bayarcash { background: #e3f2fd; color: #1565c0; }
  .payment-badge.cod { background: #fff3e0; color: #e65100; }
  .payment-badge.other { background: #f3e5f5; color: #7b1fa2; }

  .status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
  }
  .status-1 { background: #e3f2fd; color: #1565c0; }
  .status-2 { background: #fff3e0; color: #e65100; }
  .status-3 { background: #e8f5e9; color: #2e7d32; }
  .status-4 { background: #e0f2f1; color: #00695c; }
  .status-5 { background: #fce4ec; color: #c62828; }
  .status-6 { background: #efebe9; color: #4e342e; }

  .shipping-info {
    font-size: 0.8rem;
    color: #344767;
  }
  .shipping-info small {
    color: #8392ab;
    display: block;
    margin-top: 2px;
  }

  /* Action buttons */
  .order-actions {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
  }
  .order-actions .btn {
    font-size: 0.78rem;
    padding: 5px 12px;
    border-radius: 8px;
    white-space: nowrap;
    width: 170px;
  }

  /* Pagination */
  .pagination-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
  }
  .pagination-bar .btn {
    border-radius: 8px;
    font-size: 0.8rem;
  }
  .page-indicator {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.8rem;
    color: #344767;
    font-weight: 600;
  }

  /* No data */
  .no-data-card {
    text-align: center;
    padding: 3rem;
    color: #8392ab;
  }
  .no-data-card img {
    width: 80px;
    opacity: 0.6;
    margin-bottom: 1rem;
  }
  .no-data-card p {
    font-size: 0.9rem;
  }

  /* Checkbox style */
  .order-checkbox {
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 0.25rem;
  }
  .order-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #667eea;
  }

  /* Modal overrides */
  .bg-modal .modal-details {
    border-radius: 16px;
    padding: 1.5rem;
    max-width: 800px;
  }

  .proof-link {
    font-size: 0.7rem;
    color: #667eea;
    text-decoration: none;
  }
  .proof-link:hover {
    text-decoration: underline;
    color: #764ba2;
  }
</style>

<div class="container-fluid py-4">
  <!-- Page Header -->
  <div class="row mb-3">
    <div class="col-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div>
        <h5 class="order-page-header"><?= $pageName ?></h5>
        <p class="order-page-sub"><?= $totalOrders ?> order<?= $totalOrders != 1 ? 's' : '' ?> found</p>
      </div>
    </div>
  </div>

  <!-- Filter Bar -->
  <div class="filter-bar">
    <label><i class="fa-solid fa-filter me-1"></i> Filter:</label>
    <input type="text" id="filter-product" placeholder="Product name...">
    <input type="number" id="qty-product" placeholder="Qty">
    <select id="sort-product">
      <option value="">Sort By</option>
      <option value="asc">Ascending</option>
      <option value="desc">Descending</option>
    </select>
    <button class="filter-btn" id="filter"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
  </div>

  <script>
    window.addEventListener("DOMContentLoaded", function() {
      const urlParams = new URLSearchParams(window.location.search);
      const existingSearch = urlParams.get("filter");
      const existingQty = urlParams.get("qty");
      const existingSort = urlParams.get("sort");
      if (existingSearch) document.getElementById("filter-product").value = existingSearch;
      if (existingQty) document.getElementById("qty-product").value = existingQty;
      if (existingSort) document.getElementById("sort-product").value = existingSort;

      const input = document.getElementById("filter-product");
      input.focus();
      const length = input.value.length;
      input.setSelectionRange(length, length);
    });

    document.getElementById("filter").addEventListener("click", function() {
      const value = document.getElementById("filter-product").value.trim();
      const qty = document.getElementById("qty-product").value.trim();
      const sort = document.getElementById("sort-product").value.trim();
      const url = new URL(window.location.href);
      url.searchParams.set("filter", value);
      url.searchParams.set("qty", qty);
      url.searchParams.set("sort", sort);
      window.location.href = url.toString();
    });

    document.getElementById("filter-product").addEventListener("keydown", function(e) {
      if (e.key === "Enter") document.getElementById("filter").click();
    });
  </script>

  <!-- Bulk Actions -->
  <form action="" method="post" id="bulkForm">
    <input type="hidden" id="allIDS" name="orderID" value="">
    <div class="bulk-bar">
      <div class="order-checkbox">
        <input type="checkbox" id="checkAll"> <span style="font-size:0.8rem;color:#344767;margin-left:6px;font-weight:600;">Select All</span>
      </div>
      <span class="selected-badge" id="selectedCount">0 selected</span>
      <?php if ($pageName == "Order - New") { ?>
        <button class="btn btn-sm btn-info" id="bulkJNT" type="submit" name="jnt" disabled><i class="fa-solid fa-truck-fast"></i> Bulk Send to J&T</button>
      <?php } else if ($pageName == "Order - Process") { ?>
        <button class="btn btn-sm btn-primary" id="bulkJNT" type="submit" name="printAWB" disabled><i class="fa-solid fa-print"></i> Bulk Print AWB</button>
        <button class="btn btn-sm btn-primary" id="bulkJNTS" type="submit" name="move-indelivery" disabled><i class="fa-solid fa-truck-fast"></i> Bulk Move In Delivery</button>
      <?php } ?>
    </div>
  </form>

  <!-- Order Cards -->
  <?php
  if ($result->num_rows < 1) {
  ?>
    <div class="order-card">
      <div class="no-data-card">
        <img src="<?= $domainURL ?>assets/images/no-data.png" alt="No data">
        <p>No orders found</p>
      </div>
    </div>
  <?php
  } else {
    while ($row = $result->fetch_array()) {
      $session_id = $row["session_id"];

      // Status mapping
      $statusLabels = ['0'=>'To Pay','1'=>'New Order','2'=>'Processing','3'=>'In Delivery','4'=>'Completed','5'=>'Returned','6'=>'Cancelled'];
      $statusText = $statusLabels[$row['status']] ?? 'Unknown';

      // Payment badge class
      $payChannel = strtolower($row['payment_channel'] ?? '');
      $payBadgeClass = 'other';
      if (strpos($payChannel, 'senangpay') !== false) $payBadgeClass = 'senangpay';
      elseif (strpos($payChannel, 'bayarcash') !== false) $payBadgeClass = 'bayarcash';
      elseif (strpos($payChannel, 'cod') !== false) $payBadgeClass = 'cod';
  ?>
    <div class="order-card">
      <div class="order-card-header">
        <div class="customer-info">
          <?php if ($row['status'] == "1" && !empty($row['awb_number'])) { ?>
            <input type="checkbox" disabled style="width:16px;height:16px;accent-color:#667eea;">
          <?php } else { ?>
            <input type="checkbox" name="order_ids[]" value="<?= $row['order_id'] ?>" style="width:16px;height:16px;accent-color:#667eea;">
          <?php } ?>
          <i class="fa-solid fa-user" style="color:#667eea;"></i>
          <?= $row["customer_name"] ?>
          <span class="country-badge"><?= $row["country"] ?></span>
        </div>
        <span class="order-id-badge">#<?= str_pad($row["order_id"], 8, "0", STR_PAD_LEFT) ?>
          <?php if ($pageName == "Order - Process" && isset($row['printed_status'])) echo ' &middot; ' . $row['printed_status']; ?>
        </span>
      </div>
      <div class="order-card-body">
        <div class="order-grid">
          <!-- Products -->
          <div class="product-list" style="grid-column: span 2;">
            <?php
            $string = $row["product_var_id"];
            $parts = explode(",", $string);
            foreach ($parts as $varID) {
              $id = str_replace(['[', ']'], '', $varID);
              $sql1 = "SELECT * FROM product_variants WHERE id='$id'";
              $result1 = $conn->query($sql1);
              while ($row1 = $result1->fetch_assoc()) {
                $product_id = $row1["product_id"];
                $pv_id = $row1["id"];

                $sql11 = "SELECT * FROM `products` WHERE `id`='$product_id'";
                $result11 = $conn->query($sql11);
                $row11 = $result11->fetch_assoc();

                $sql111 = "SELECT * FROM `product_image` WHERE `product_id`='$product_id' ORDER BY id ASC LIMIT 1";
                $result111 = $conn->query($sql111);
                $row111 = $result111->fetch_assoc();

                $sql1111 = "SELECT * FROM cart WHERE `session_id`='$session_id' AND p_id='$product_id' AND pv_id='$id' AND deleted_at IS NULL";
                $result1111 = $conn->query($sql1111);
                $row1111 = $result1111->fetch_assoc();

                $tprices = $row1111['quantity'] * $row1111['price'];
            ?>
              <div class="product-item">
                <img src="<?= $domainURL ?>assets/images/products/<?= $row111['image'] ?>" alt="<?= $row11["name"] ?>">
                <div class="product-info">
                  <div class="product-name"><?= $row11["name"] ?></div>
                  <div class="product-sku"><?= $row1["sku"] ?></div>
                </div>
                <div class="product-qty">x<?= $row1111['quantity'] ?></div>
                <div class="product-price">
                  <span style="font-size:0.7rem;color:#8392ab;"><?= $row1111['quantity'] ?> x RM<?= number_format($row1111['price'], 2) ?></span>
                  <b>RM<?= number_format($tprices, 2) ?></b>
                </div>
              </div>
            <?php
              }
            }
            ?>
          </div>

          <!-- Order Total -->
          <div class="order-meta-item">
            <div class="order-meta-label">Total</div>
            <div class="order-total">RM<?= number_format($row["myr_value_include_postage"], 2) ?></div>
            <span class="payment-badge <?= $payBadgeClass ?>"><?= $row['payment_channel'] ?></span>
            <br>
            <a href="<?= $domainURL ?>check-stripe-status.php?payment_intent=<?= $row['payment_url'] ?>" target="_blank" class="proof-link"><i class="fa-solid fa-receipt"></i> Payment Proof</a>
          </div>

          <!-- Status + Shipping -->
          <div class="order-meta">
            <div class="order-meta-item">
              <div class="order-meta-label">Status</div>
              <span class="status-badge status-<?= $row['status'] ?>"><?= $statusText ?></span>
            </div>
            <div class="order-meta-item">
              <div class="order-meta-label">Shipping</div>
              <div class="shipping-info">
                <?= $row['ship_channel'] ?>
                <small>Courier: <b><?= empty($row['courier_service']) ? 'Not assigned' : $row['courier_service'] ?></b></small>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="order-actions">
            <a class="btn btn-outline-primary open-details" data-id="<?= $row['order_id']; ?>"><i class="fa-solid fa-magnifying-glass"></i> Details</a>

            <?php if (!empty($row['awb_number'])) { ?>
              <span class="btn btn-info" onclick="window.open('<?= $row["tracking_url"] ?>', '_blank')"><i class="fa-solid fa-truck-fast"></i> <?= $row['awb_number'] ?></span>
            <?php } ?>

            <?php if ($row['status'] == "1") { ?>
              <form action="" method="post" style="display:contents;">
                <input type="hidden" name="orderID" value="<?= $row["order_id"] ?>">
                <?php if (!empty($row['awb_number'])) { ?>
                  <a class="btn btn-dark" href="<?= $domainURL ?>set-order-to-processing/<?= $row["order_id"] ?>"><i class="fa-solid fa-right-long"></i> To Processing</a>
                <?php } else { ?>
                  <button class="btn btn-info" type="submit" name="jnt"><i class="fa-solid fa-truck-fast"></i> Send to J&T</button>
                <?php } ?>
                <a class="btn btn-outline-danger" href="<?= $domainURL ?>set-order-status/<?= $row["order_id"] ?>/1/6"><i class="fa-solid fa-ban"></i> Cancel</a>
              </form>
            <?php } else if ($row['status'] == "2") { ?>
              <form action="" method="post" style="display:contents;">
                <input type="hidden" name="orderID" value="<?= $row["order_id"] ?>">
                <button class="btn btn-secondary" type="submit" name="print-awb"><i class="fa-solid fa-print"></i> Print AWB</button>
                <a class="btn btn-dark" href="<?= $domainURL ?>set-order-status/<?= $row["order_id"] ?>/2/3"><i class="fa-solid fa-truck-ramp-box"></i> In Delivery</a>
                <a class="btn btn-outline-danger" href="<?= $domainURL ?>set-order-status/<?= $row["order_id"] ?>/2/6"><i class="fa-solid fa-ban"></i> Cancel</a>
              </form>
            <?php } else if ($row['status'] == "3") { ?>
              <form id="printForm" action="" method="post" target="hiddenIframe" style="display:contents;">
                <input type="hidden" name="orderID" value="<?= $row["order_id"] ?>">
                <a class="btn btn-secondary" onclick="window.open('<?= $domainURL ?>awb-jt.php?id=<?= $row["order_id"] ?>', '_blank')"><i class="fa-solid fa-print"></i> Re-Print AWB</a>
                <a class="btn btn-success" href="<?= $domainURL ?>set-order-status/<?= $row["order_id"] ?>/3/4"><i class="fa-solid fa-check-double"></i> Completed</a>
                <a class="btn btn-warning" href="<?= $domainURL ?>set-order-status/<?= $row["order_id"] ?>/3/5"><i class="fa-solid fa-rotate-left"></i> RTS</a>
                <a class="btn btn-outline-danger" href="<?= $domainURL ?>set-order-status/<?= $row["order_id"] ?>/3/6"><i class="fa-solid fa-ban"></i> Cancel</a>
              </form>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  <?php
    }
  }
  ?>

  <!-- Pagination -->
  <?php if ($totalPages >= 1): ?>
    <div class="pagination-bar">
      <?php
      $baseURL = $domainURL . $firstSegments;
      $queryParams = [];
      if (!empty($search)) $queryParams['filter'] = urlencode($search);
      if (!empty($qty)) $queryParams['qty'] = urlencode($qty);
      if (!empty($printed)) $queryParams['printed'] = urlencode($printed);
      if (!empty($sort)) $queryParams['sort'] = urlencode($sort);

      function buildPageLink($page, $queryParams, $baseURL) {
        $queryParams['page'] = $page;
        return $baseURL . '?' . http_build_query($queryParams);
      }

      $prevPage = $page - 1;
      $nextPage = $page + 1;

      if ($page > 1) {
        echo '<a class="btn btn-sm" style="background:#667eea;color:#fff;" href="' . buildPageLink(1, $queryParams, $baseURL) . '">First</a>';
        echo '<a class="btn btn-sm btn-outline-secondary" href="' . buildPageLink($prevPage, $queryParams, $baseURL) . '"><i class="fa-solid fa-chevron-left"></i> Prev</a>';
      }

      echo '<span class="page-indicator">Page ' . $page . ' of ' . $totalPages . '</span>';

      if ($page < $totalPages) {
        echo '<a class="btn btn-sm btn-outline-secondary" href="' . buildPageLink($nextPage, $queryParams, $baseURL) . '">Next <i class="fa-solid fa-chevron-right"></i></a>';
        echo '<a class="btn btn-sm" style="background:#667eea;color:#fff;" href="' . buildPageLink($totalPages, $queryParams, $baseURL) . '">Last</a>';
      }
      ?>
    </div>
  <?php endif; ?>

  <!-- Order Details Modal -->
  <div class="bg-modal">
    <div class="modal-details">
      <i style="position:absolute;top:10px;right:12px;font-size:24px;color:#f5576c;cursor:pointer;" class="fa-solid fa-xmark end-bg-modal"></i>
      <i style="position:absolute;top:10px;right:45px;font-size:22px;color:#667eea;cursor:pointer;" class="fa-solid fa-pen-to-square edit-order-data"></i>
      <i style="position:absolute;top:10px;right:45px;display:none;font-size:22px;color:#667eea;cursor:pointer;" class="fa-solid fa-arrow-left close-order-data"></i>
      <input type="hidden" id="orderNoID" value="">
      <div id="details-buyer"></div>
    </div>
  </div>

  <iframe name="hiddenIframe" style="display:none;"></iframe>
</div>

<script>
  // Checkbox bulk selection
  const checkAll = document.getElementById('checkAll');
  const allIdsInput = document.getElementById('allIDS');
  const bulkBtn = document.getElementById('bulkJNT');
  const bulksBtn = document.getElementById('bulkJNTS');
  const selectedCount = document.getElementById('selectedCount');

  function updateAllIds() {
    const checkedValues = Array.from(document.querySelectorAll('input[name="order_ids[]"]:checked')).map(cb => cb.value);
    allIdsInput.value = checkedValues.join(',');
    selectedCount.textContent = checkedValues.length > 0 ? checkedValues.length + ' selected' : '0 selected';
    if (bulkBtn) bulkBtn.disabled = checkedValues.length === 0;
    if (bulksBtn) bulksBtn.disabled = checkedValues.length === 0;
  }

  checkAll.addEventListener('change', function() {
    document.querySelectorAll('input[name="order_ids[]"]').forEach(cb => cb.checked = this.checked);
    updateAllIds();
  });

  document.querySelectorAll('input[name="order_ids[]"]').forEach(cb => {
    cb.addEventListener('change', updateAllIds);
  });

  if (bulkBtn) {
    bulkBtn.addEventListener('click', function(e) {
      if (this.disabled) {
        e.preventDefault();
        Swal.fire({icon:'warning', title:'Select Orders', text:'Please select at least one order before proceeding.'});
      }
    });
  }

  updateAllIds();

  // Modal
  $(document).ready(function() {
    $(".end-bg-modal").click(function() {
      $(".bg-modal").hide();
      $('body').css('overflow-y', 'auto');
      $("#details-buyer").text("");
    });
    $(".open-details").click(function() {
      var orderID = $(this).data("id");
      $(".bg-modal").show();
      $('body').css('overflow-y', 'hidden');
      $("#orderNoID").val(orderID);
      $("#details-buyer").load("<?= $domainURL ?>details-buyer?order_id=" + orderID);
    });
    $(".edit-order-data").click(function() {
      var orderIDs = $("#orderNoID").val();
      $(this).hide();
      $(".close-order-data").show();
      $("#details-buyer").load("<?= $domainURL ?>update-buyer?order_id=" + orderIDs);
    });
    $(".close-order-data").click(function() {
      var orderIDs = $("#orderNoID").val();
      $(this).hide();
      $(".edit-order-data").show();
      $("#details-buyer").load("<?= $domainURL ?>details-buyer?order_id=" + orderIDs);
    });
  });

  // Print form redirect
  var printForm = document.getElementById('printForm');
  if (printForm) {
    printForm.addEventListener('submit', function() {
      setTimeout(function() {
        window.location.href = "<?= $domainURL ?>process-order";
      }, 3000);
    });
  }
</script>

<?php
include "01-footer.php";
?>
