<footer class="footer pt-3  ">
  <div class="container-fluid">
    <div class="row align-items-center justify-content-lg-between">
      <div class="col-lg-6 mb-lg-0 mb-4">
        <div class="copyright text-center text-sm text-muted text-lg-start">
          © <script>
            document.write(new Date().getFullYear())
          </script>,
          <a href="<?= $domainURL; ?>">Rozeyana.com</a>
        </div>
      </div>

    </div>
  </div>
</footer>
</div>
</main>
<div class="fixed-plugin">
  <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
    <i class="fa fa-cog py-2"> </i>
  </a>
  <div class="card shadow-lg ">
    <div class="card-header pb-0 pt-3 ">
      <div class="float-start">
        <h5 class="mt-3 mb-0">Soft UI Configurator</h5>
        <p>See our dashboard options.</p>
      </div>
      <div class="float-end mt-4">
        <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
          <i class="fa fa-close"></i>
        </button>
      </div>
      <!-- End Toggle Button -->
    </div>
    <hr class="horizontal dark my-1">
    <div class="card-body pt-sm-3 pt-0">
      <!-- Sidebar Backgrounds -->
      <div>
        <h6 class="mb-0">Sidebar Colors</h6>
      </div>
      <a href="javascript:void(0)" class="switch-trigger background-color">
        <div class="badge-colors my-2 text-start">
          <span class="badge filter bg-primary active" data-color="primary" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
        </div>
      </a>
      <!-- Sidenav Type -->
      <div class="mt-3">
        <h6 class="mb-0">Sidenav Type</h6>
        <p class="text-sm">Choose between 2 different sidenav types.</p>
      </div>
      <div class="d-flex">
        <button class="btn btn-primary w-100 px-3 mb-2 active" data-class="bg-transparent" onclick="sidebarType(this)">Transparent</button>
        <button class="btn btn-primary w-100 px-3 mb-2 ms-2" data-class="bg-white" onclick="sidebarType(this)">White</button>
      </div>
      <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
      <!-- Navbar Fixed -->
      <div class="mt-3">
        <h6 class="mb-0">Navbar Fixed</h6>
      </div>
      <div class="form-check form-switch ps-0">
        <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
      </div>
      <hr class="horizontal dark my-sm-4">
      <a class="btn bg-gradient-dark w-100" href="https://www.creative-tim.com/product/soft-ui-dashboard">Free Download</a>
      <a class="btn btn-outline-dark w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/license/soft-ui-dashboard">View documentation</a>
      <div class="w-100 text-center">
        <a class="github-button" href="https://github.com/creativetimofficial/soft-ui-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star creativetimofficial/soft-ui-dashboard on GitHub">Star</a>
        <h6 class="mt-3">Thank you for sharing!</h6>
        <a href="https://twitter.com/intent/tweet?text=Check%20Soft%20UI%20Dashboard%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fsoft-ui-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
          <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
        </a>
        <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/soft-ui-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
          <i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const currentUrl = window.location.pathname;

    document.querySelectorAll("#sidenav-main .nav-link").forEach(link => {
      const linkHref = new URL(link.href, window.location.origin).pathname;

      // Skip links like href="#" or empty hrefs (usually for toggles)
      if (linkHref === "#" || !linkHref || linkHref === window.location.origin + "/") return;

      // Exact match or nested match (excluding dummy links)
      if (currentUrl === linkHref || currentUrl.startsWith(linkHref + "/")) {
        // Add active class to the matched link
        link.classList.add("active");

        // Open immediate submenu if it exists
        const submenu = link.nextElementSibling;
        if (submenu && submenu.tagName === "UL") {
          submenu.classList.add("open");
        }

        // Open all parent submenus (recursive)
        let parentUL = link.closest("ul");
        while (parentUL && parentUL.id !== "sidenav-main") {
          const parentLink = parentUL.previousElementSibling;
          if (parentLink && parentLink.classList.contains("nav-link")) {
            parentLink.classList.add("active");
          }
          parentUL.classList.add("open");
          parentUL = parentUL.parentElement.closest("ul");
        }
      }
    });
  });

  function toggleSubMenu(element) {
    // Skip dummy links like href="#" (prevent jump to top)
    if (element.getAttribute("href") === "#") {
      event.preventDefault();
    }

    element.classList.toggle("active");

    let submenu = element.nextElementSibling;
    if (submenu && submenu.tagName === "UL") {
      submenu.classList.toggle("open");
    }

    // Accordion behavior (optional)
    document.querySelectorAll("#sidenav-main .nav-link").forEach(link => {
      if (link !== element && link.nextElementSibling && link.nextElementSibling.tagName === "UL") {
        link.classList.remove("active");
        link.nextElementSibling.classList.remove("open");
      }
    });
  }
</script>

<!--   Core JS Files   -->
<script src="<?= $domainURL; ?>assets/admin/assets/js/core/popper.min.js"></script>
<script src="<?= $domainURL; ?>assets/admin/assets/js/core/bootstrap.min.js"></script>
<script src="<?= $domainURL; ?>assets/admin/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="<?= $domainURL; ?>assets/admin/assets/js/plugins/smooth-scrollbar.min.js"></script>
<script src="<?= $domainURL; ?>assets/admin/assets/js/plugins/chartjs.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function confirmDelete(element) {
    Swal.fire({
      title: 'Are you sure?',
      text: "This action cannot be undone!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        // Option 1: Redirect to delete URL
        // window.location.href = 'http://localhost:8000/delete-product/8';

        // Option 2: Get product ID dynamically
        const row = element.closest('tr');
        const productId = row.querySelector('td').innerText.trim();

        // Replace below with your actual delete route
        <?php
        if ($pageName == "Stock Control") {
        ?>
          window.location.href = `<?= $domainURL ?>delete-product/${productId}`;
        <?php
        } else if ($pageName == "Category - Add & Update") {
        ?>
          window.location.href = `<?= $domainURL ?>delete-category/${productId}`;
        <?php
        } else if ($pageName == "Brand - Add & Update") {
        ?>
          window.location.href = `<?= $domainURL ?>delete-brand/${productId}`;
        <?php
        }
        ?>

      }
    });
  }
</script>
<script>
  var ctx = document.getElementById("chart-bars").getContext("2d");

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [{
        label: "Sales",
        tension: 0.4,
        borderWidth: 0,
        borderRadius: 4,
        borderSkipped: false,
        backgroundColor: "#fff",
        data: [450, 200, 100, 220, 500, 100, 400, 230, 500],
        maxBarThickness: 6
      }, ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        }
      },
      interaction: {
        intersect: false,
        mode: 'index',
      },
      scales: {
        y: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
          },
          ticks: {
            suggestedMin: 0,
            suggestedMax: 500,
            beginAtZero: true,
            padding: 15,
            font: {
              size: 14,
              family: "Inter",
              style: 'normal',
              lineHeight: 2
            },
            color: "#fff"
          },
        },
        x: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false
          },
          ticks: {
            display: false
          },
        },
      },
    },
  });


  var ctx2 = document.getElementById("chart-line").getContext("2d");

  var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

  gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
  gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
  gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

  var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

  gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
  gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
  gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

  new Chart(ctx2, {
    type: "line",
    data: {
      labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [{
          label: "Mobile apps",
          tension: 0.4,
          borderWidth: 0,
          pointRadius: 0,
          borderColor: "#cb0c9f",
          borderWidth: 3,
          backgroundColor: gradientStroke1,
          fill: true,
          data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
          maxBarThickness: 6

        },
        {
          label: "Websites",
          tension: 0.4,
          borderWidth: 0,
          pointRadius: 0,
          borderColor: "#3A416F",
          borderWidth: 3,
          backgroundColor: gradientStroke2,
          fill: true,
          data: [30, 90, 40, 140, 290, 290, 340, 230, 400],
          maxBarThickness: 6
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        }
      },
      interaction: {
        intersect: false,
        mode: 'index',
      },
      scales: {
        y: {
          grid: {
            drawBorder: false,
            display: true,
            drawOnChartArea: true,
            drawTicks: false,
            borderDash: [5, 5]
          },
          ticks: {
            display: true,
            padding: 10,
            color: '#b2b9bf',
            font: {
              size: 11,
              family: "Inter",
              style: 'normal',
              lineHeight: 2
            },
          }
        },
        x: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
            borderDash: [5, 5]
          },
          ticks: {
            display: true,
            color: '#b2b9bf',
            padding: 20,
            font: {
              size: 11,
              family: "Inter",
              style: 'normal',
              lineHeight: 2
            },
          }
        },
      },
    },
  });
</script>
<script>
  var win = navigator.platform.indexOf('Win') > -1;
  if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
      damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
  }
</script>
<script>
  $(document).ready(function() {
    $('.select2').select2({
      placeholder: "Select an option",
      allowClear: true
    });
  });
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
<script src="<?= $domainURL; ?>assets/admin/assets/js/soft-ui-dashboard.min.js?v=1.1.0"></script>

<script>
    <?php if (!empty($successTicket)): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?= addslashes($successTicket) ?>',
            confirmButtonColor: '#28a745'
        });
    <?php endif; ?>

    <?php if (!empty($errorTicket)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?= addslashes($errorTicket) ?>',
            confirmButtonColor: '#d33'
        });
    <?php endif; ?>
</script>

<?php
if ($pageName == "New Product" || $pageName == "Update Product" || $pageName == "Stock Control" || $pageName == "Category - Update" || $pageName == "Category - Add & Update" || $pageName == "Brand - Update" || $pageName == "Brand - Add & Update" || $pageName == "Order - New" || $pageName == "DHL - Setting" || $pageName == "Order - Process" || $pageName == "Policy - Setting" || $pageName == 'Terms & Conditions - Setting' || $pageName == 'About Us - Setting' || $pageName == "Add New Country" || $pageName == "List Country" || $pageName == "Shipping Cost" || $pageName == "Staff List" || $pageName == "Announcement & Blog" || $pageName == "Announcement & Blog (Update)" || $pageName == "Profile" || $pageName == "J&T Setting" || $pageName == "Password" || $pageName == "Setting Image") {
  $uploadSuccess = $_SESSION['upload_success'] ?? null;
  $uploadError = $_SESSION['upload_error'] ?? null;
  unset($_SESSION['upload_success'], $_SESSION['upload_error']);

?>
  <style>
    .popup-status {
      position: fixed;
      max-width: 400px;
      width: calc(100% - 10px);
      top: 20px;
      right: 20px;
      background-color: #f0f0f0;
      color: #333;
      padding: 15px 20px;
      border-radius: 12px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
      z-index: 9999;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 16px;
      animation: slideIn 0.5s ease-out;
    }

    .popup-status.success {
      background-color: #d4edda;
      color: #155724;
    }

    .popup-status.error {
      background-color: #f8d7da;
      color: #721c24;
    }

    .popup-icon {
      font-size: 24px;
    }

    .preview-modal {
      display: none;
      position: fixed;
      z-index: 9999;
      padding-top: 60px;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.9);
      text-align: center;
    }

    .preview-content {
      margin: auto;
      display: block;
      max-width: 80%;
      max-height: 80%;
      border-radius: 10px;
      animation: zoom 0.3s ease-in-out;
    }

    @keyframes zoom {
      from {
        transform: scale(0.5);
        opacity: 0;
      }

      to {
        transform: scale(1);
        opacity: 1;
      }
    }

    .preview-modal .close {
      position: absolute;
      top: 15px;
      right: 35px;
      color: white;
      font-size: 40px;
      font-weight: bold;
      cursor: pointer;
    }

    .nav-btn {
      position: absolute;
      top: 50%;
      color: white;
      background-color: rgba(0, 0, 0, 0.5);
      font-size: 30px;
      padding: 10px;
      cursor: pointer;
      border: none;
      border-radius: 5px;
      user-select: none;
    }

    .prev {
      left: 10px;
    }

    .next {
      right: 10px;
    }

    .nav-btn:hover {
      background-color: rgba(255, 255, 255, 0.2);
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
  <div id="popup-status" class="popup-status <?= $uploadSuccess ? 'success' : ($uploadError ? 'error' : '') ?>"
    style="display: none;">
    <div class="popup-icon">
      <?= $uploadSuccess ? '<i class="fa-solid fa-circle-check"></i>' : ($uploadError ? '<i class="fa-solid fa-circle-xmark"></i>' : '') ?>
    </div>
    <div class="popup-message">
      <?= $uploadSuccess ?: $uploadError ?>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const popup = document.getElementById('popup-status');
      if (popup && popup.textContent.trim() !== "") {
        popup.style.display = 'flex';
        setTimeout(() => {
          popup.style.display = 'none';
        }, 5000); // Auto hide after 3 seconds
      }
    });
  </script>

  <div id="imagePreviewModal" class="preview-modal">
    <span class="close" onclick="closeImageModal()">&times;</span>
    <img class="preview-content" id="modalImage">

    <?php
    if ($pageName == "Category - Add & Update" || $pageName == "Brand - Add & Update") {
    } else {
    ?>
      <button class="nav-btn prev" onclick="showPrevImage(event)">&#10094;</button>
      <button class="nav-btn next" onclick="showNextImage(event)">&#10095;</button>
    <?php
    }
    ?>

  </div>
  <script>
    let imageList = [];
    let currentIndex = 0;

    function previewImage(src) {
      imageList = Array.from(document.querySelectorAll('.existing-image img')).map(img => img.src);
      currentIndex = imageList.indexOf(src);
      showImage(currentIndex);
    }

    function showImage(index) {
      const modal = document.getElementById("imagePreviewModal");
      const modalImg = document.getElementById("modalImage");

      if (index < 0) index = imageList.length - 1;
      if (index >= imageList.length) index = 0;

      currentIndex = index;
      modal.style.display = "block";
      modalImg.src = imageList[currentIndex];
    }

    function closeImageModal() {
      document.getElementById("imagePreviewModal").style.display = "none";
    }

    function showNextImage(event) {
      event.stopPropagation();
      showImage(currentIndex + 1);
    }

    function showPrevImage(event) {
      event.stopPropagation();
      showImage(currentIndex - 1);
    }
  </script>

  <?php
}

if ($pageName == "Category - Add & Update" || $pageName == "Brand - Add & Update" || $pageName == "Stock Control" || $pageName == "List Country" || $pageName == "Shipping Cost") {
  if ($pageName == "Category - Add & Update" || $pageName == "Brand - Add & Update") {
  ?>
    <div id="popupModal" class="modal">
      <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Add New <?= $nameBtn ?></h2>
        <form class="popup-form" action="<?= getFullUrl(); ?>" method="post" enctype="multipart/form-data">
          <input class="form-control" type="text" name="name" placeholder="<?= $nameBtn ?> Name" required>
          <input class="form-control" type="file" name="files[]" placeholder="<?= $nameBtn ?> Image" required style="margin-bottom:20px;">
          <input type="submit" value="Add & Save">
        </form>
      </div>
    </div>
  <?php
  } else if ($pageName == "Stock Control") {
  ?>
    <div id="popupModal" class="modal">
      <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h4>Add or Deduct stock?<br>Product: <span id="modalProductName"></span></h4>
        <form class="popup-form" action="<?= $domainURL ?>stock-control" method="post" enctype="multipart/form-data">
          <input type="hidden" id="product_id_input" name="product_id">
          Select one (Add or Deduct)
          <select class="form-control" name="type" required style="margin-bottom:15px;">
            <option readonly disabled selected value="">select one</option>
            <option value="1">Add</option>
            <option value="2">Deduct</option>
          </select>
          Quantity
          <input type="number" min="1" step="1" class="form-control" name="qty" placeholder="Quantity" required style="margin-bottom:15px;">
          <button class="btn btn-primary" type="submit">Process</button>
        </form>
      </div>
    </div>
  <?php
  } else if ($pageName == "List Country") {
  ?>
    <div id="popupModal" class="modal">
      <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h4>Update <span id="cname"></span></h4>
        <form class="popup-form" action="" method="post" enctype="multipart/form-data">
          <input type="hidden" id="cid" name="cid">
          <input type="hidden" id="cnames" name="cnames">
          Currency Code
          <input type="text" class="form-control" id="ccode" name="ccode" placeholder="Currency Code" required style="margin-bottom:15px;">
          Currency Rate
          <input type="number" min="0.01" step="0.01" class="form-control" id="crate" name="crate" placeholder="Currency Rate" required style="margin-bottom:15px;">
          Status
          <select class="form-control" id="cstatus" name="cstatus" required style="margin-bottom:15px;">
            <option value="0">Inactive</option>
            <option value="1">Active</option>
          </select>
          <button class="btn btn-primary" type="submit">Process</button>
        </form>
      </div>
    </div>
  <?php
  } else if ($pageName == "Shipping Cost") {
  ?>
    <div id="popupModal" class="modal">
      <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h4>Add/Update Shipping Cost</h4>
        <form class="popup-form" action="" method="post" enctype="multipart/form-data">
          Country
          <select name="country" id="scountry" class="form-control" style="margin-bottom:15px;">
            <option value="" selected readonly disabled>select country</option>
            <?php
            while ($rows = $results->fetch_array()) {
            ?>
              <option value="<?= $rows["id"]; ?>" data-sign="<?= $rows["sign"]; ?>"><?= $rows["name"]; ?></option>
            <?php
            }
            ?>
          </select>
          <div id="szones" style="display:none;">
            Shipping Zone
            <select name="szone" id="szone" class="form-control" style="margin-bottom:15px;">
              <option value="" selected readonly disabled>select zone</option>
              <option value="1">West Malaysia</option>
              <option value="2">East Malaysia</option>
            </select>
          </div>
          First Kilo (<span id="curs"></span>)
          <input type="number" class="form-control" id="fkilo" name="fkilo" min="0.00" step="0.01" placeholder="First Kilo" required style="margin-bottom:15px;">
          Next Kilo (<span id="curs2"></span>)
          <input type="number" class="form-control" id="nkilo" name="nkilo" min="0.00" step="0.01" placeholder="First Kilo" required style="margin-bottom:15px;">


          <button class="btn btn-primary" type="submit">Process</button>
        </form>
      </div>
    </div>
  <?php
  }
  ?>


  <script>
    // Open modal
    <?php
    if ($pageName == "Stock Control") {
    ?>

      function openModal(button) {


        const productId = button.getAttribute("data-id");
        const productName = button.getAttribute("data-name");

        document.getElementById("product_id_input").value = productId;
        document.getElementById("modalProductName").innerText = productName;


        document.getElementById("popupModal").style.display = "block";


      }
    <?php
    } else if ($pageName == "List Country") {
    ?>

      function openModal(button) {

        // <h4>Update <span id="cname"></span></h4>
        //   <form class="popup-form" action="" method="post" enctype="multipart/form-data">
        //     <input type="hidden" id="cid" name="cid">
        //     Currency Code
        //     <input type="text" class="form-control" id="ccode" name="ccode" placeholder="Currency Code" required style="margin-bottom:15px;">
        //     Currency Rate
        //     <input type="number" min="0.01" step="0.01" class="form-control" id="crate" name="crate" placeholder="Currency Rate" required style="margin-bottom:15px;">
        //     <button class="btn btn-primary" type="submit">Process</button>
        //   </form>


        const cid = button.getAttribute("data-id");
        const cname = button.getAttribute("data-name");
        const ccode = button.getAttribute("data-code");
        const crate = button.getAttribute("data-rate");
        const cstatus = button.getAttribute("data-status");

        document.getElementById("cid").value = cid;
        document.getElementById("cnames").value = cname;
        document.getElementById("cname").innerText = cname;
        document.getElementById("ccode").value = ccode;
        document.getElementById("crate").value = crate;
        document.getElementById("cstatus").value = cstatus;


        document.getElementById("popupModal").style.display = "block";


      }
    <?php
    } else if ($pageName == "Shipping Cost") {
    ?>

      document.addEventListener("DOMContentLoaded", function() {
        const countrySelect = document.getElementById("scountry");
        if (countrySelect) {
          countrySelect.addEventListener("change", function() {
            const selectedOption = this.options[this.selectedIndex];
            const selectedValue = this.value;
            const currencySign = selectedOption.getAttribute("data-sign");
            if (selectedValue == 1) {
              document.getElementById("szones").style.display = "block";
              document.getElementById("szone").setAttribute("required", "required");
            } else {
              document.getElementById("szones").style.display = "none";
              document.getElementById("szone").removeAttribute("required");
            }

            document.getElementById("curs").innerText = currencySign;

            document.getElementById("curs2").innerText = currencySign;
          });
        }
      });

      function openModal(button) {

        //const csign = button.getAttribute("data-sign");

        document.getElementById("popupModal").style.display = "block";






      }
    <?php
    } else {
    ?>

      function openModal() {





        document.getElementById("popupModal").style.display = "block";


      }
    <?php
    }
    ?>


    // Close modal
    function closeModal() {
      document.getElementById("popupModal").style.display = "none";
    }

    // Close when clicking outside the modal content
    window.onclick = function(event) {
      const modal = document.getElementById("popupModal");
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
<?php
}

if($pageName == "Dashboard")
{
  ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    function fetchLiveData() {
        fetch("/live_orders.json?" + Date.now())
            .then(r => r.json())
            .then(data => {
                const totalOrdersEl = document.getElementById('totalOrders');
                const totalSalesEl = document.getElementById('totalSales');
                const totalReturnsEl = document.getElementById('totalReturns');
                const totalOrdersElT = document.getElementById('totalOrdersToday');
                const totalSalesElT = document.getElementById('totalSalesToday');
                const totalSalesThisMonth = document.getElementById('totalSalesThisMonth');
                const totalSalesLastMonth = document.getElementById('totalSalesLastMonth');
                const liveViewL = document.getElementById('liveView');
                const list = document.getElementById('orderList');

                if (totalOrdersEl) totalOrdersEl.innerHTML = data.totals.orders;
                if (totalSalesEl) totalSalesEl.innerHTML = data.totals.sales;
                if (totalReturnsEl) totalReturnsEl.innerHTML = data.totals.returns;
                if (totalOrdersElT) totalOrdersElT.innerHTML = data.totals.orders_today;
                if (totalSalesThisMonth) totalSalesThisMonth.innerHTML = data.totals.sales_this_month;
                if (totalSalesLastMonth) totalSalesLastMonth.innerHTML = data.totals.sales_last_month;
                if (totalSalesElT) totalSalesElT.innerHTML = data.totals.sales_today;
                if (liveViewL) liveViewL.innerHTML = "<i class=\"fa-solid fa-eye\"></i> " + data.totals.live_visitors + " <span class=\"blink\">LIVE</span>";

                if (list) {
                    list.innerHTML = '';
                    data.orders.forEach(o => {
                        list.innerHTML += `<tr>
                            <td class="text-secondary text-xxs font-weight-bolder opacity-7">${o.id}</td>
                            <td class="text-secondary text-xxs font-weight-bolder opacity-7">${o.customer_name}</td>
                            <td class="text-secondary text-xxs font-weight-bolder opacity-7" style="text-align:center;">${o.total_qty}</td>
                            <td class="text-secondary text-xxs font-weight-bolder opacity-7" style="text-align:center;">${o.sign} ${o.amount}</td>
                            <td class="text-secondary text-xxs font-weight-bolder opacity-7" style="text-align:center;">${o.country}</td>
                            <td class="text-secondary text-xxs font-weight-bolder opacity-7">${o.status_html}</td>
                        </tr>`;
                    });
                }
            })
            .catch(err => console.error("Error fetching live data:", err));
    }

    fetchLiveData();
    setInterval(fetchLiveData, 2000);
});
</script>
  <?php
}
?>


</body>

</html>