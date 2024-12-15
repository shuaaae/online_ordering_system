<?php
include("../connection/connect.php");
error_reporting(E_ALL);  // Enable error reporting to catch issues
ini_set('display_errors', 1);

session_start();

if (isset($_POST['submit'])) {
    if (empty($_POST['d_name']) || empty($_POST['about']) || $_POST['price'] == '') {
        $error = '<div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>All fields Must be filled!</strong>
                  </div>';
    } else {
        $dish_count = count($_FILES['file']['name']);  // Get the number of files uploaded

        for ($i = 0; $i < $dish_count; $i++) {
            $fname = $_FILES['file']['name'][$i];
            $temp = $_FILES['file']['tmp_name'][$i];
            $fsize = $_FILES['file']['size'][$i];
            $extension = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
            $fnew = uniqid() . '.' . $extension;

            $store = "Res_img/dishes/" . basename($fnew);

            // Escape input values to avoid SQL injection
            $d_name = mysqli_real_escape_string($db, $_POST['d_name'][$i]);
            $about = mysqli_real_escape_string($db, $_POST['about'][$i]);
            $price = mysqli_real_escape_string($db, $_POST['price'][$i]);

            // Validate file extension and size
            if ($extension == 'jpg' || $extension == 'png' || $extension == 'gif') {
                if ($fsize >= 1000000) {
                    $error = '<div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <strong>Max Image Size is 1024kb!</strong> Try a different Image.
                              </div>';
                } else {
                    // Insert dish into database
                    $sql = "INSERT INTO dishes (title, slogan, price, img) 
                            VALUES ('$d_name', '$about', '$price', '$fnew')";
                    $result = mysqli_query($db, $sql);

                    if ($result) {
                        move_uploaded_file($temp, $store);

                        $success = '<div class="alert alert-success alert-dismissible fade show">
                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                      New Dish Added Successfully.
                                    </div>';
                    } else {
                        $error = '<div class="alert alert-danger alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <strong>SQL Error!</strong> ' . mysqli_error($db) . '
                                  </div>';
                    }
                }
            } elseif ($extension == '') {
                $error = '<div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <strong>Select an image!</strong>
                          </div>';
            } else {
                $error = '<div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <strong>Invalid extension!</strong> Only PNG, JPG, and GIF are accepted.
                          </div>';
            }
        }
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Menu</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="fix-header">
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>
    <div id="main-wrapper">
        <div class="header">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto mt-md-0"></ul>
                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="../images/logotrans.png" alt="user" class="profile-pic" />
                            </a>
                            <div class="dropdown-menu dropdown-menu-right animated zoomIn">
                                <ul class="dropdown-user">
                                    <li><a href="logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="left-sidebar">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-devider"></li>
                        <li class="nav-label">Home</li>
                        <li><a href="dashboard.php"><i class="fa fa-tachometer"></i><span>Dashboard</span></a></li>
                        <li class="nav-label">Log</li>
                        <li><a href="all_users.php"><span><i class="fa fa-user f-s-20 "></i></span><span>Users</span></a></li>
                        <li><a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-cutlery" aria-hidden="true"></i><span class="hide-menu">Menu</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_menu.php">All Menues</a></li>
                                <li><a href="add_menu.php">Add Menu</a></li>
                            </ul>
                        </li>
                        <li><a href="all_orders.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Orders</span></a></li>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="page-wrapper">
            <div class="container-fluid">
                <!-- Start Page Content -->
    
                <div class="col-lg-12">
                    <div class="card card-outline-primary">
                        <div class="card-header">
                            <h4 class="m-b-0 text-white">Add Menu</h4>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="form-body">
                                    <hr>
                                    <div id="dish-form">
                                        <div class="row p-t-20">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Dish Name</label>
                                                    <input type="text" name="d_name[]" class="form-control" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Description</label>
                                                    <input type="text" name="about[]" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row p-t-20">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Price</label>
                                                    <input type="text" name="price[]" class="form-control" placeholder="₱" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">Image</label>
                                                    <input type="file" name="file[]" class="form-control" accept="image/*" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-actions">
                                        <button type="button" id="add-dish-btn" class="btn btn-success">Add Another Dish</button>
                                        <input type="submit" name="submit" class="btn btn-primary" value="Save">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS for dynamically adding new dish form -->
    <script>
        document.getElementById('add-dish-btn').addEventListener('click', function() {
    var dishForm = document.createElement('div');
    dishForm.classList.add('row', 'p-t-20');
    dishForm.innerHTML = 
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Dish Name</label>
                <input type="text" name="d_name[]" class="form-control" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Description</label>
                <input type="text" name="about[]" class="form-control" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Price</label>
                <input type="text" name="price[]" class="form-control" placeholder="₱" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Image</label>
                <input type="file" name="file[]" class="form-control" accept="image/*" required>
            </div>
        </div>
    ;
    document.getElementById('dish-form').appendChild(dishForm);
});

    </script>

    <script src="js/lib/jquery/jquery.min.js"></script>
    <script src="js/lib/bootstrap/js/popper.min.js"></script>
    <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/sidebarmenu.js"></script>
    <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
    <script src="js/custom.min.js"></script>
</body>
</html>
