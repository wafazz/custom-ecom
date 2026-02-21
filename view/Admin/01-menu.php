<?php
$sqlLogo = "SELECT * FROM `image_setting` WHERE `use_type`='logo' AND sorting='1'";
$queryLogo = $conn->query($sqlLogo);
$rowLogo = $queryLogo->fetch_assoc();
?>

<body class="g-sidenav-show  bg-gray-100">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 "
    id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
        aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="<?= $domainURL; ?>dashboard" style="margin-top: 0px !important;">
        <img src="<?= $domainURL; ?><?= $rowLogo["image_path"] ?>" class="navbar-brand-img h-100" alt="main_logo">
      </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <?php

      $menuOrder = menuOrderCount();
      $currentPath = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
      $segments = explode('/', $currentPath);
      $firstSegment = $segments[0];

      $salesMenu   = ['new-order', 'process-order', 'indelivery-order', 'completed-order', 'returned-order', 'cancelled-order', 'database-order'];
      $productMenu = ['category-product', 'brand-product', 'new-product', 'stock-control'];
      $settingMenu = ['fiuu-setting', 'dhl-setting', 'jt-express', 'ninjavan-setting', 'poslaju-setting', 'setting-policy', 'setting-terms', 'setting-about-us', 'logo-setting'];
      $deliveryMenu = ['my-west', 'my-east', 'del-internatinal'];
      $countryMenu = ['list-country', 'add-new-country'];
      $searchMenu = ['search-order'];

      function hasAnyAccess(array $menuGroup, $userId)
      {
        foreach ($menuGroup as $menu) {
          if (roleVerify($menu, $userId) == 1) {
            return true;
          }
        }
        return false;
      }

      // Access flags
      $hasSalesAccess   = hasAnyAccess($salesMenu, $_SESSION['user']->id);
      $hasProductAccess = hasAnyAccess($productMenu, $_SESSION['user']->id);
      $hasSettingAccess = hasAnyAccess($settingMenu, $_SESSION['user']->id);
      $hasCountryAccess = hasAnyAccess($countryMenu, $_SESSION['user']->id);
      $hasDeliveryAccess = roleVerify('delivery-charge', $_SESSION['user']->id) == 1;
      $hasDashboardAccess = roleVerify('dashboard', $_SESSION['user']->id) == 1;
      $hasSupportAccess = roleVerify('support/tickets', $_SESSION['user']->id) == 1;
      $hasSearchAccess = hasAnyAccess($searchMenu, $_SESSION['user']->id) == 1;
      ?>
      <style>
        .nav-link.active,
        .bagde-menu a.active {
          background-color: #d5d2d2;
          color: #000 !important;
          font-weight: bold;
        }

        ul li ul {
          display: none;
          margin-left: 10px;
        }

        ul li ul[style*="display:block"] {
          display: block !important;
        }
      </style>
      <ul class="navbar-nav">

        <?php if ($hasSearchAccess): ?>
          <!-- Dashboard -->
          <li class="nav-item" style="padding: 10px;
    border-bottom: 1px solid #ccc;
    margin-bottom: 10px;">
            <a class="nav-link">
              <i class="fa-solid fa-magnifying-glass shadow border-radius-md icon-menu"></i>
              <span class="nav-link-text ms-1">Search Order</span>
            </a>
            <form action="<?= $domainURL ?>search-order" method="get">
              <input type="text" name="search" class="form-control">
              <small>Seach by: Order ID, Name, Email, Phone</small>
            </form>
          </li>
        <?php endif; ?>

        <?php if ($hasDashboardAccess): ?>
          <!-- Dashboard -->
          <li class="nav-item">
            <a class="nav-link <?= ($currentPath == 'dashboard') ? 'active' : '' ?>" href="<?= $domainURL; ?>dashboard">
              <i class="fa-solid fa-house shadow border-radius-md icon-menu"></i>
              <span class="nav-link-text ms-1">Dashboard</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if ($hasSupportAccess): ?>

          <li class="nav-item">
            <a class="nav-link <?= ($currentPath == 'sales-stats') ? 'active' : '' ?>" href="<?= $domainURL; ?>sales-stats">
              <i class="fa-solid fa-chart-area shadow border-radius-md icon-menu"></i>
              <span class="nav-link-text ms-1">Sales Statistic</span>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?= ($currentPath == 'sales-report') ? 'active' : '' ?>" href="<?= $domainURL; ?>sales-report">
              <i class="fa-solid fa-chart-area shadow border-radius-md icon-menu"></i>
              <span class="nav-link-text ms-1">Sales Report</span>
            </a>
          </li>

        <?php endif; ?>

        <?php if ($hasSalesAccess): ?>
          <!-- Sales / Order -->
          <li class="nav-item">
            <a class="nav-link <?= in_array($currentPath, $salesMenu) ? 'active' : '' ?>" onclick="toggleSubMenu(this)">
              <i class="fa-solid fa-cart-shopping shadow border-radius-md icon-menu"></i>
              <span class="nav-link-text ms-1">Sales/Order</span>
              <i class="fa-solid fa-chevron-left menu-chevron-down"></i>
            </a>
            <ul style="<?= in_array($currentPath, $salesMenu) ? 'display:block;' : '' ?>">
              <?php
              $xmenu = 0;
              foreach ($salesMenu as $slug):
                if (roleVerify($slug, $_SESSION['user']->id) == 1):
              ?>
                  <li class="bagde-menu">
                    <a class="<?= $currentPath == $slug ? 'active' : '' ?>" href="<?= $domainURL . $slug ?>">
                      <?= ucwords(str_replace('-', ' ', $slug)) ?>
                    </a>
                    <?php $bgb = ($menuOrder[$xmenu] >= 1) ? "bg-danger f-w-b" : ""; ?>
                    <span class="menu-badge <?= $bgb ?>"><?= $menuOrder[$xmenu] ?></span>
                  </li>
              <?php endif;
                $xmenu++;
              endforeach; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if ($hasProductAccess): ?>
          <!-- Manage Product -->
          <li class="nav-item">
            <a class="nav-link <?= in_array($currentPath, $productMenu) ? 'active' : '' ?>" onclick="toggleSubMenu(this)">
              <i class="fa-brands fa-product-hunt shadow border-radius-md icon-menu"></i>
              <span class="nav-link-text ms-1">Manage Product</span>
              <i class="fa-solid fa-chevron-left menu-chevron-down"></i>
            </a>
            <ul style="<?= in_array($currentPath, $productMenu) ? 'display:block;' : '' ?>">
              <?php foreach ($productMenu as $slug):
                if (roleVerify($slug, $_SESSION['user']->id) == 1): ?>
                  <li class="bagde-menu">
                    <a class="<?= $currentPath == $slug ? 'active' : '' ?>" href="<?= $domainURL . $slug ?>">
                      <?= ucwords(str_replace('-', ' ', $slug)) ?>
                    </a>
                    <span class="menu-badge">0</span>
                  </li>
              <?php endif;
              endforeach; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if ($hasSettingAccess): ?>
          <!-- Settings -->
          <li class="nav-item mt-3">
            <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Settings</h6>
          </li>

          <!-- Mandatory Setting -->
          <li class="nav-item">
            <a class="nav-link <?= in_array($currentPath, $settingMenu) ? 'active' : '' ?>" onclick="toggleSubMenu(this)">
              <i class="fa-solid fa-screwdriver-wrench shadow border-radius-md icon-menu"></i>
              <span class="nav-link-text ms-1">Mandatory Setting</span>
              <i class="fa-solid fa-chevron-left menu-chevron-down"></i>
            </a>
            <ul style="<?= in_array($currentPath, $settingMenu) ? 'display:block;' : '' ?>">
              <?php foreach ($settingMenu as $slug):
                if (roleVerify($slug, $_SESSION['user']->id) == 1): ?>
                  <li class="bagde-menu">
                    <a class="<?= $currentPath == $slug ? 'active' : '' ?>" href="<?= $domainURL . $slug ?>">
                      <?= ucwords(str_replace('-', ' ', str_replace('setting', 'Setting', $slug))) ?>
                    </a>
                  </li>
              <?php endif;
              endforeach; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if ($hasDeliveryAccess): ?>
          <!-- Delivery Cost -->
          <li class="nav-item">
            <a class="nav-link <?= ($currentPath == 'delivery-charge') ? 'active' : '' ?>" href="<?= $domainURL; ?>delivery-charge">
              <i class="fa-solid fa-truck shadow border-radius-md icon-menu"></i>
              <span class="nav-link-text ms-1">Shipping Cost</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if ($hasCountryAccess): ?>
          <!-- Multi Country -->
          <li class="nav-item">
            <a class="nav-link <?= in_array($currentPath, $countryMenu) ? 'active' : '' ?>" onclick="toggleSubMenu(this)">
              <i class="fa-solid fa-house-flag shadow border-radius-md icon-menu"></i>
              <span class="nav-link-text ms-1">Multi Country</span>
              <i class="fa-solid fa-chevron-left menu-chevron-down"></i>
            </a>
            <ul style="<?= in_array($currentPath, $countryMenu) ? 'display:block;' : '' ?>">
              <?php foreach ($countryMenu as $slug):
                if (roleVerify($slug, $_SESSION['user']->id) == 1): ?>
                  <li class="bagde-menu">
                    <a class="<?= $currentPath == $slug ? 'active' : '' ?>" href="<?= $domainURL . $slug ?>">
                      <?= ucwords(str_replace('-', ' ', $slug)) ?>
                    </a>
                    <span class="menu-badge">0</span>
                  </li>
              <?php endif;
              endforeach; ?>
            </ul>
          </li>
        <?php endif; ?>

        <?php if ($hasSupportAccess): ?>

          <li class="nav-item mt-3">
            <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Support</h6>
          </li>

          <li class="nav-item">
            <a class="nav-link <?= $currentPath == 'support/tickets' ? 'active' : '' ?>" href="<?= $domainURL; ?>support/tickets">
              <i class="fa-solid fa-users shadow border-radius-md icon-menu"></i>
              <span class="nav-link-text ms-1">Support Tickets</span>
            </a>
          </li>

        <?php endif; ?>

        <!-- Account Section (always shown) -->
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account</h6>
        </li>

        <?php if (roleVerify('hq-staff', $_SESSION['user']->id) == 1): ?>
          <li class="nav-item">
            <a class="nav-link <?= $currentPath == 'hq-staff' ? 'active' : '' ?>" href="<?= $domainURL; ?>hq-staff">
              <i class="fa-solid fa-users shadow border-radius-md icon-menu"></i>
              <span class="nav-link-text ms-1">HQ Staff</span>
            </a>
          </li>
        <?php endif; ?>

        <?php if (roleVerify('announcement-blog', $_SESSION['user']->id) == 1): ?>
          <li class="nav-item">
            <a class="nav-link <?= $currentPath == 'announcement-blog' ? 'active' : '' ?>" href="<?= $domainURL; ?>announcement-blog">
              <i class="fa-solid fa-bullhorn shadow border-radius-md icon-menu"></i>
              <span class="nav-link-text ms-1">Announcement & Blog</span>
            </a>
          </li>
        <?php endif; ?>

        <li class="nav-item">
          <a class="nav-link <?= ($currentPath == 'profile') ? 'active' : '' ?>" href="<?= $domainURL; ?>profile">
            <i class="fa-solid fa-user shadow border-radius-md icon-menu"></i>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($currentPath == 'password') ? 'active' : '' ?>" href="<?= $domainURL; ?>password">
            <i class="fa-solid fa-key shadow border-radius-md icon-menu"></i>
            <span class="nav-link-text ms-1">Password</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= ($currentPath == 'logout') ? 'active cactive' : '' ?>" href="<?= $domainURL; ?>logout">
            <i class="fa-solid fa-arrow-right-from-bracket shadow border-radius-md icon-menu"></i>
            <span class="nav-link-text ms-1">Logout</span>
          </a>
        </li>

      </ul>
      <!-- <script>
        function toggleSubMenu(element) {
          const navItem = element.closest('.nav-item');
          navItem.classList.toggle('open');
        }
      </script> -->
    </div>
    <!-- <div class="sidenav-footer mx-3 ">
      <div class="card card-background shadow-none card-background-mask-secondary" id="sidenavCard">
        <div class="full-background" style="background-image: url('../assets/img/curved-images/white-curved.jpg')"></div>
        <div class="card-body text-start p-3 w-100">
          <div class="icon icon-shape icon-sm bg-white shadow text-center mb-3 d-flex align-items-center justify-content-center border-radius-md">
            <i class="ni ni-diamond text-dark text-gradient text-lg top-0" aria-hidden="true" id="sidenavCardIcon"></i>
          </div>
          <div class="docs-info">
            <h6 class="text-white up mb-0">Need help?</h6>
            <p class="text-xs font-weight-bold">Please check our docs</p>
            <a href="https://www.creative-tim.com/learning-lab/bootstrap/license/soft-ui-dashboard" target="_blank" class="btn btn-white btn-sm w-100 mb-0">Documentation</a>
          </div>
        </div>
      </div>
      <a class="btn btn-primary mt-3 w-100" href="https://www.creative-tim.com/product/soft-ui-dashboard-pro?ref=sidebarfree">Upgrade to pro</a>
    </div> -->
  </aside>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
      navbar-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <h6 class="font-weight-bolder mb-0">
            <?= $pageName; ?>
          </h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group">
              <!-- <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
              <input type="text" class="form-control" placeholder="Type here..."> -->
            </div>
          </div>
          <ul class="navbar-nav  justify-content-end">
            <li class="nav-item d-flex align-items-center">
              <a class="btn btn-outline-danger btn-sm mb-0 me-3 cactive" href="<?= $domainURL; ?>logout"><i
                  class="fa-solid fa-arrow-right-from-bracket" style="font-size:12px; margin-right:10px;"></i>
                Logout</a>
            </li>
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>

          </ul>
        </div>
      </div>
    </nav>