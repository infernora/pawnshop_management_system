<?php
session_start();


    include("admin-connection.php");
    include("admin-functions.php");

    $user_data = check_login($con);


?>









<?php
require_once('databasepdo.php');
@session_start();

if(isset($_POST["Clearbutton"])){
  unset($_SESSION["filter"]);
}

//retrieve all item list
$strsql = "SELECT * FROM item";
$parameters = array(); //empty initially

//filter added to query
if(isset($_POST['filter'])){
  $filter = $_POST['filter'];
  $strsql .= " WHERE name LIKE ? OR item_id LIKE ? OR customer_id LIKE ? OR brand LIKE ? OR type LIKE ? OR
            model LIKE ? OR status LIKE ? OR expert_id LIKE ?";
  $parameters = ["%{$filter}%", "%{$filter}%","%{$filter}%", "%{$filter}%","%{$filter}%","%{$filter}%","%{$filter}%","%{$filter}%"];
  $_SESSION['filter'] = $filter;  
}else{
  if(isset($_SESSION['filter']) && strlen($_SESSION['filter'])>0 ){
    //reapply old filter if user is just sorting
    $filter = $_SESSION['filter'];
    $strsql .= " WHERE name LIKE ? OR item_id LIKE ? OR customer_id LIKE ? OR brand LIKE ? OR type LIKE ? OR
      model LIKE ? OR status LIKE ? OR expert_id LIKE ?";
      $parameters = ["%{$filter}%", "%{$filter}%","%{$filter}%", "%{$filter}%","%{$filter}%","%{$filter}%","%{$filter}%","%{$filter}%"];
  }
}

//sorting
if(isset($_GET['sort'])){
    $sort = $_GET['sort'];
    $strsql .= " ORDER BY $sort";
  }

  $prepared = $conn->prepare($strsql);
  $prepared->execute($parameters);

if(isset($_GET['view'])){
  $deets  = $_GET['view'];
  $info = explode('-',$deets);
  $item_id = $info[0];
  $status = $info[1];

  if($status == 'pawn'){
    header("Location: viewitemdetailspawn.php?details=".$item_id.'-'.$status);  
  }elseif($status == 'for sale' || $status == 'sold'){
    header("Location: viewitemdetailsinvoice.php?details=".$item_id.'-'.$status); 
  }else{
    echo "Item sent to expert for evaluation";
  } 
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>
    Admin Dashboard
  </title>

  <!-- Bootstrap CSS library -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet"
    crossorigin="anonymous">
  <link href="./assets/css/dashboard.css" rel="stylesheet">
</head>

<body id="saltuiHtml">

  <nav class="navbar navbar-expand-lg bg-white sticky-top border-bottom navbar-top p-2 m-0 shadow-sm">
    <div class="container-fluid">
      <a class="navbar-brand" href="home.php">
        <img src="assets/images/logo.jpg" width="60" title="SaltUI" class="brand">
      </a>
      <div class="collapse navbar-collapse" id="mainNav"> </div>
      <div class="nav-item mx-1 mx-md-2 position-relative"> <i class="bi bi-search position-absolute sr-icon"></i>
        <input type="search" class="ps-4-5 input-search form-control rounded-5 form-control-sm mt-1">
      </div>
      <div class="nav-item dropdown mx-1 mx-md-2">
        <a href="#" class="nav-link position-relative mt-1" role="button" data-bs-toggle="dropdown"
          aria-expanded="false"> <i class="bi bi-bell-fill fs-5"></i> </a>
        <!-- Badge -->
        <div class="badge badge-circle bg-primary badge-msg-n fw-normal"> <span>2</span> </div>
        <ul class="dropdown-menu dropdown-menu-right shadow-sm">
          <li class="border-bottom fs-6 py-2 px-3"> Notifications </li>
          <li class="border-bottom"> <a class="dropdown-item d-flex py-2" href="#"> <img src="assets/images/m5.jpg"
                width="35" alt="AV" class="rounded-circle me-3">
              <div class="me-3">
                <p class="m-0"> The future uploaded: How to get what you want?</p> <small class="text-muted">1 hours
                  ago</small>
              </div> <img src="assets/images/nature-1.jpg" width="50" alt="AV" class="rounded-1 ms-auto">
            </a> </li>
          <li class="border-bottom"> <a class="dropdown-item d-flex py-2" href="#"> <img src="assets/images/m1.jpg"
                width="35" alt="AV" class="rounded-circle me-3">
              <div class="me-3">
                <p class="m-0">Layout component #1</p> <small class="text-muted">3 hours ago</small>
              </div> <img src="assets/images/css-banner.jpg" width="50" alt="AV" class="rounded-1 ms-auto">
            </a> </li>
          <li class="bg-light text-center py-2"> <a class="fs-7" href="#"> View all </a> </li>
        </ul>
      </div>
      <div class="dropdown mx-1 mx-md-2"> <a href="#" class="nav-link position-relative" role="button"
          data-bs-toggle="dropdown" aria-expanded="false"> <img src="assets/images/m1.jpg" width="30" alt="AV"
            class="rounded-circle border"> </a>
        <ul class="dropdown-menu shadow-sm">
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><a class="dropdown-item" href="#">Settings</a></li>
          <li><a class="dropdown-item" href="admin-logout.php">Sign out</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <section>
    <div class="sidebar h-100vn">
      <ul class="nav nav-pills mb-0 align-items-center w-100">
        <li class="nav-item"> <a href="itemlist.php" class="nav-link align-middle px-0 active" title="Item List"> <i
              class="fs-6 bi-speedometer2"></i> </a> </li>
        <li> <a href="customerlist.php" class="nav-link px-0 align-middle" title="Customer List"> <i
              class="fs-6 bi bi-people-fill"></i> </a> </li>
        <li> <a href="employeelist.php" class="nav-link px-0 align-middle" title="Employee List"> <i
              class="fs-6 bi bi-calendar-event-fill"></i> </a> </li>
        <li> <a href="invoicelist.php" class="nav-link px-0 align-middle" title="Invoice List"> <i
              class="fs-6 bi bi-journal-bookmark-fill"></i> </a> </li>
        <li> <a href="pawnticketlist.php" class="nav-link px-0 align-middle" title="Pawn ticket List"> <i
              class="fs-6 bi bi-file-earmark-bar-graph"></i> </a> </li>
        <li> <a href="expertlist.php" class="nav-link px-0 align-middle" title="Expert List"> <i class="fs-6 bi-people"></i></a> </li>
      </ul>
    </div>
    
    <div class="row g-0 justify-content-right">
      <div class="col-2 col-md-2">
        <div class="sidenav h-100vn border-end bg-light">
          <ul class="nav nav-pills">
            <li class="nav-item">
              <a href="home.php" class="nav-link px-0 align-middle active">
                <i class="fs-7 bi-people"></i>
                <span class="ms-2 d-none d-sm-inline">Dashboard</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="addcustomer.php" class="nav-link px-0 align-middle">
                <i class="fs-7 bi-people"></i>
                <span class="ms-2 d-none d-sm-inline">Add Customer</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="addemployee.php" class="nav-link px-0 align-middle">
                <i class="fs-7 bi-people"></i>
                <span class="ms-2 d-none d-sm-inline">Add Employee</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="addexpert.php" class="nav-link px-0 align-middle">
                <i class="fs-7 bi-people"></i>
                <span class="ms-2 d-none d-sm-inline">Add expert</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="addinvoice.php" class="nav-link px-0 align-middle">
                <i class="fs-7 bi-people"></i>
                <span class="ms-2 d-none d-sm-inline">Add Invoice</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="additem.php" class="nav-link px-0 align-middle">
                <i class="fs-7 bi-people"></i>
                <span class="ms-2 d-none d-sm-inline">Add items</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="addpawnticket.php" class="nav-link px-0 align-middle">
                <i class="fs-7 bi-people"></i>
                <span class="ms-2 d-none d-sm-inline">Add pawntickets</span>
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div class="col-10 col-md-10">
      <link rel = "stylesheet" href = "./assets/css/table.css">
      <h2>List of Items
      <form class="filterForm" method="POST" action="itemlist.php">
        <input type="text" id="filter" name="filter" placeholder="filter keyword" tabindex="0" value="<?= $filter ?? '' ?>"/>
        <input type="submit" name="Filterbutton" id="Filterbutton" value="Go"/>
        <input type="submit" name="Clearbutton" id="Clearbutton" value="Clear Filters"/>
      </form>
    </h2>

  <div class="table-wrapper">
  <table class="fl-table">
  <?php
    //list of product names with links
    //echo $prepared->debugDumpParams();
    if($prepared->rowCount() > 0){
      //table headers with links for sorting
      echo '<thead>';
      echo '<tr>';
      echo '<th class="name"><a href="itemlist.php?sort=name">Name</a></th>';
      echo '<th class="item_id"><a href="itemlist.php?sort=item_id">Item ID</a></th>';
      echo '<th class="customer_id"><a href="itemlist.php?sort=customer_id">Customer ID</a></th>';
      echo '<th class="brand"><a href="itemlist.php?sort=brand">Brand</a></th>';
      echo '<th class="type"><a href="itemlist.php?sort=type">Type</a></th>';
      echo '<th class="model"><a href="itemlist.php?sort=model">Model</a></th>';
      echo '<th class="status"><a href="itemlist.php?sort=status">Status</a></th>';
      echo '<th class="expert_id"><a href="itemlist.php?sort=expert_id">Expert ID</a></th>';
      echo '</tr>';
      echo '</thead>';
      echo '<tbody>';
      $prepared->setFetchMode(PDO::FETCH_ASSOC);
      while($row= $prepared->fetch()){
        echo '<tr>';
        echo '<td>' . $row['name'] . '</td>';
        echo '<td>' . $row['item_id'] . '</td>';
        echo '<td>' . $row['customer_id'] . '</td>';
        echo '<td>' . $row['brand'] . '</td>';
        echo '<td>' . $row['type'] . '</td>';
        echo '<td>' . $row['model'] . '</td>';
        echo '<td>' . $row['status'] . '</td>';
        echo '<td>' . $row['expert_id'] . '</td>';
        echo '<td><a href="edititem.php?edit='. $row['item_id'].'" class="btn btn-success">Edit</a></td>';
        echo '<td><a href="itemlist.php?view='. $row['item_id'].'-'.$row['status'].'
              " class="btn btn-success">View Details</a></td>';
        echo '<td><a href="delete.php?delete='.$row['item_id'].'-item"class="btn btn-danger">Delete</a></td>';
        echo '</tr>';
      }
      echo '<tbody>';
    }else{
      //no products
      echo '<p>No products currently available.</p>';
    }
  ?>


    <script>
        document.getElementById('Clearbutton').addEventListener('click', (ev)=>{
        document.getElementById('filter').value = '';
        })
    </script>
      </div>
      
          <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
          <script>
            const ctx = document.getElementById('myChart');
            const ctx2 = document.getElementById('revenueChart');

            new Chart(ctx, {
              type: 'bar',
              data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                  label: '# of Sales Report',
                  borderColor: '#727cf5',
                  backgroundColor: '#dcdefc',
                  data: [9, 7, 3, 5, 2, 3, 8, 5, 3, 5, 2, 3],
                  borderWidth: 0
                }]
              },
              options: {
                scales: {
                  y: {
                    beginAtZero: true
                  }
                },
                layout: {
                  padding: 10
                }
              }
            });

            new Chart(ctx2, {
              type: 'bar',
              data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                  label: '# of Revenue Report',
                  borderColor: '#efefef',
                  backgroundColor: '#f7e6eb',
                  data: [5, 7, 8, 9, 8, 7, 8, 5, 3, 2, 8, 9],
                  borderWidth: 0
                }]
              },
              options: {
                scales: {
                  y: {
                    beginAtZero: true
                  }
                },
                layout: {
                  padding: 10
                }
              }
            });
          </script>
          <!-- Content end -->
        </div>
      </div>
    </div>
  </section>


  <!-- Bootstrap JavaScript library -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous">
  </script>

    <script>
      document.getElementById('Clearbutton').addEventListener('click', (ev)=>{
      document.getElementById('filter').value = '';
      });
    </script>
</body>

</html>