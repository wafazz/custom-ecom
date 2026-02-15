<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Square Product Image</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 2rem;
        }

        .product__item {
            border: 1px solid #eee;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            transition: 0.3s ease-in-out;
        }

        .product__item:hover {
            transform: translateY(-5px);
        }

        .product__item__pic {
            position: relative;
            width: 100%;
            padding-top: 100%; /* 1:1 aspect ratio */
            background-size: cover;
            background-position: center;
        }

        .product__item__text {
            padding: 15px;
            text-align: center;
        }

        .product__price {
            font-weight: bold;
            color: #e44d26;
        }

        .label.new {
            position: absolute;
            top: 10px;
            left: 10px;
            background: red;
            color: #fff;
            padding: 3px 8px;
            font-size: 12px;
            border-radius: 3px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="product__item">
                <div class="product__item__pic set-bg" data-setbg="https://via.placeholder.com/400">
                    <div class="label new">New</div>
                </div>
                <div class="product__item__text">
                    <h6><a href="#">Product Name</a></h6>
                    <div class="product__price">$59.00</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Set background images
    $('.set-bg').each(function () {
        var bg = $(this).data('setbg');
        $(this).css('background-image', 'url(' + bg + ')');
    });
</script>

</body>
</html>
