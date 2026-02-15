<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="<?= $domainURL; ?>assets/images/r-web-logo.png">
  <link rel="icon" type="image/png" href="<?= $domainURL; ?>assets/images/r-web-logo.png">
  <title>
    <?= $pageName; ?> | Rozeyana.com
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="https://demos.creative-tim.com/soft-ui-dashboard/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/soft-ui-dashboard/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <!-- <script src="https://kit.fontawesome.com/c274b4e380.js" crossorigin="anonymous"></script> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- CSS Files -->
  <link id="pagestyle" href="<?= $domainURL; ?>assets/admin/assets/css/soft-ui-dashboard.css?v=1.1.0"
    rel="stylesheet" />
  <link href="<?= $domainURL; ?>assets/admin/assets/css/custom.css" rel="stylesheet" />
  <!-- Nepcha Analytics (nepcha.com) -->
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="<?= $domainURL; ?>" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
  <script src="https://kit.fontawesome.com/c274b4e380.js" crossorigin="anonymous"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.tiny.cloud/1/2tb749nzn8qcg1ydrn594gj3i466jk4tfrigtofjhgw7z6k7/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



  <style>
    /* Button to Open the Modal */
    .open-btn {
      display: block;
      margin-left: auto;
      margin-right: auto;
      padding: 12px 25px;
      background-color: #007BFF;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }

    /* The Modal Background */
    .modal {
      display: none;
      /* Hidden by default */
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.6);
      padding-top: 60px;
    }

    /* Modal Content Box */
    .modal-content {
      background-color: #fff;
      margin: 5% auto;
      padding: 20px;
      border-radius: 8px;
      width: 90%;
      max-width: 400px;
      position: relative;
    }

    /* Close Button */
    .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 22px;
      font-weight: bold;
      color: #333;
      cursor: pointer;
    }

    /* Form Styles */
    .popup-form input[type="text"],
    .popup-form input[type="email"],
    .popup-form input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .popup-form input[type="submit"] {
      background-color: #28a745;
      color: white;
      padding: 10px;
      width: 100%;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
    }

    .popup-form input[type="submit"]:hover {
      background-color: #218838;
    }

    /*order details modal*/
    .bg-modal {
      display: none;
      position: fixed;
      z-index: 9999;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      top: 0;
      left: 0;
      justify-content: center;
      /* horizontal center */
      align-items: center;
      /* vertical center */
    }

    .modal-details {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      max-width: 750px;
      width: calc(100% - 50px);
      max-height: 90%;
      overflow: auto;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
      display: block;
      margin-left: auto;
      margin-right: auto;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    #details-buyer {
      overflow-y: auto;
      max-height: calc(80vh - 60px);
      /* adjust based on padding/header */
    }

    .headline-modal {
      padding: 5px 10px;
      background: #f0f0f0;
      font-weight: 200 !important;
    }

    .p_details {
      font-size: 14px;
      margin: 3px;
    }

    .mention {
      font-weight: bold;
    }

    .dmb-10{
      margin-bottom: 10px !important;
    }
    .dmb-15{
      margin-bottom: 15px !important;
    }
    .dmb-20{
      margin-bottom: 20px !important;
    }

    .conts ul li{
      margin-left: 25px !important;
    }

    @media (max-width: 480px) {
      .modal-content {
        margin: 10% auto;
      }
    }
  </style>


</head>