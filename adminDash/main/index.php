<?php
include("../includes/connection2.php");
include("../includes/header.php");


function getNumOfRows($tb_name, $conn)
{
    $sql = "SELECT * FROM $tb_name";
    $result = $conn->query($sql);
    $total = ($result) ? $result->num_rows : 0;
    return $total;
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            position: relative;
            padding: 20px;
            color: white;
        }

        .card-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
            color: white;
        }

        .card-text {
            font-size: 1rem;
            margin-bottom: 0;
        }

        .card-icon {
            position: absolute;
            right: 20px;
            bottom: 20px;
            font-size: 3rem;
            opacity: 0.3;
        }

        .bg-primary {
            background-color: #007bff !important;
        }

        .bg-danger {
            background-color: #dc3545 !important;
        }

        .bg-success {
            background-color: #28a745 !important;
        }

        .bg-info {
            background-color: #17a2b8 !important;
        }

        .bg-warning {
            background-color: #ffc107 !important;
        }

        .bg-secondary {
            background-color: #6c757d !important;
        }

        .bg-dark {
            background-color: #343a40 !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <h1 class="mb-4">Overview</h1>
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo getNumOfRows("products", $conn) ?></h5>
                        <p class="card-text">Products</p>
                        <i class="fas fa-shopping-cart card-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card bg-secondary  text-white">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo getNumOfRows("orders", $conn) ?></h5>
                        <p class="card-text">Orders</p>
                        <i class="fas fa-clipboard-list card-icon"></i>
                    </div>
                </div>
            </div>



            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo getNumOfRows("customers", $conn) ?></h5>
                        <p class="card-text"> Customers</p>
                        <i class="fas fa-users card-icon"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo getNumOfRows("category", $conn) ?></h5>
                        <p class="card-text">categories</p>
                        <i class="fas fa-shipping-fast card-icon"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
include("../includes/footer.php")
?>