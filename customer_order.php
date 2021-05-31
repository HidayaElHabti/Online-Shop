<?php

session_start();

if(!isset($_SESSION['userid'])){
   header("Location:index.php");
}

?>

<!--Project definataion-SHOPPING CART APP using AJAX,JQUERY,PHP,MYSQLI-->
<!--I HAVE USED BOOTSTRAP FOR FRONT END-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ONLINESTORE99</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <script src="https://kit.fontawesome.com/1a40f3df8b.js" crossorigin="anonymous"></script>

</head>
<body>
<!-----------------------------------navbar start here----------------------------------------->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark" >
    <a class="navbar-brand" href="#" >ONLINE SHOP</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" 
    data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">

        <li class="nav-item active">
          <a class="nav-link" href="profile.php" >
            <i class="fas fa-home " style="font-size: larger;"></i>  Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="#" >
            <i class="fab fa-algolia" style="font-size: larger;"></i>  Products</a>
        </li>
      </ul>

    </div>
  </nav>
<br/><br/><br/>
<!-----------------------------------navbar ends here------------------------------------------>

<!-----------------------------customer_order_detail code starts here------------------------------------------>
<div class="container">
  <div class="row">
    <div class="col">
      <div class="card">
        <div class="card-header bg-primary text-white"><h5><?php echo $_SESSION['login'];?>, Your Order Details</h5></div>
         <div class="card-body">
           <?php
            include_once("db.php");
            $user_id=$_SESSION['userid'];

            $order_list="select o.id,o.client,p.product,p.name,p.price,ol.quantity,p.image
             FROM order o,order_line ol, products p WHERE o.client=$user_id AND ol.product=p.ref";

            $stmt=$conn->prepare($order_list);
            if($stmt->rowCount()>0){
                foreach ($stmt as $row){
           ?>
               <div class="row">
                 <div class="col-sm-12 col-md-6 col-xl-6 col-lg-6" style="text-align: center;">
                      <img src="img/<?php echo $row['product_image']; ?>" class="img-thumbnail card-img img-fluid" style="width:50%; height: 85%; display:inline-block;" alt="">
                 </div>
                 <div class="col-sm-12 col-md-6 col-xl-6 col-lg-6">
                
                   <table class="table">
                     <tr><td>Product Name : </td> <td><?php echo $row['name']; ?></td></tr>
                     <tr><td>Product Price :  </td> <td><?php echo $row['price']; ?></td></tr>
                     <tr><td>Product Quantity : </td> <td><?php echo $row['quantity']; ?></td></tr>
                   </table>
                 </div>
               </div>
        <?php
            }//end of while loop
          }//end of if statement

        ?>
         </div>
      </div>
    </div>
  </div>
</div>


<!-----------------------------customer_order_detail code ends here------------------------------------------>

<script src="jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

<script src="index.js"></script>


</body>
</html>