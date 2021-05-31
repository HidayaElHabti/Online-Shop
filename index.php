<?php
include "db.php";

session_start();

if(isset($_SESSION['userid'])){
  header("Location:profile.php");
}
$stmt=$conn->prepare("select * from client where id=-1");
$stmt->execute();
if($stmt->rowCount()<1)
{
    //user with id -1 to present unregisted client 
    $stmt = $conn->prepare('insert into client(id) values(-1)');
    $stmt->execute();
    $stmt = $conn->prepare('insert into `order`(client) values (-1)');
    $stmt->execute();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1a40f3df8b.js" crossorigin="anonymous"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark" >
    <a class="navbar-brand" href="#" >ONLINE SHOP</a>
  
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">

        <li class="nav-item active">
          <a class="nav-link" href="#" >
            <i class="fas fa-home " style="font-size: larger;"></i>  Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="#" >
            <i class="fab fa-algolia" style="font-size: larger;"></i>  Products</a>
        </li>
      </ul>

      <form class="form-inline my-2 my-lg-0">
        <input class="form-control mr-sm-2" type="text" placeholder="Search" id="search" aria-label="Search">
        <button class="btn btn-primary my-2 my-sm-0" id="search_btn" name="search_btn">Search</button>
      </form>

      <ul class="navbar-nav mr-auto"  style="float: right;">

        <li class="nav-item dropdown active" >
          <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profilecartdropdown">
            <i class="fas fa-cart-plus" style="font-size: larger;"></i>  Cart <span class="k1 badge badge-primary">0</span> </a>
            <div class="dropdown-menu" aria-labelledby="profilecartdropdown">
                <div class="card text-blue text-center border-primary" style="width: 400px;">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-3">No.</div>
                            <div class="col-md-3">Product Name</div>
                            <div class="col-md-3">Product Image</div>
                            <div class="col-md-3">Price in $</div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="getcartproducts"></div>
                    </div>
                </div>
            </div>
        </li>

        <li class="nav-item dropdown active" >
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" 
          id="signindropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user"  ></i>  Sign In </a>
           <div class="dropdown-menu" aria-labelledby="signindropdown">
               <div class="card text-left text-white bg-info" style="width: 300px;">
                   <div class="card-header ">Login</div>
                   <div class="card-body">
                       <form  onsubmit="return false" id="login">
                           <label for="loginemail">Email</label>
                           <input type="email" class="form-control" id="loginemail" name="loginemail" placeholder="Email">
                           <label for="Password">Password</label>
                           <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                           
                           <input type="submit" class="btn btn-success btn-xs" value="Login" style="display:block; margin-top:1%; margin-bottom:1%;">

                           <a href="customer_register_form.php"  style="border:none; display:block;color:white;">Create New Account</a>
                           
                       </form>
                   </div>
                   <div class="card-footer"><div id="login_msg"></div></div>    
               </div>
           </div> 
        </li>

        <li class="nav-item active">
            <a class="nav-link" href="customer_register_form.php" >
              <i class="fas fa-user"></i>  Sign Up</a>
          </li>
      </ul>

    </div>
</nav>
<br/><br/><br/>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">

            <div id="get_category"></div>
        </div>

        <div class="col-md-8">
            <div id="addtoproduct_msg"></div>
            <div class="card">
                <div class="card-header">Products</div>
                <div class="card-body" >
                
                    <div id="get_products"></div>

                </div>
                <div class="card-footer">
                    <div id="pageno"></div>
                </div>
            </div>
        </div>

        <div class="col-md-1"></div>
    </div>
</div>
<br/>

<script src="jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

<script src="index.js"></script>
</body>
</html>