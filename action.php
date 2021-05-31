<?php

session_start();
include "db.php";



if(isset($_POST['category'])){

    $stmt = $conn->prepare('select * from category');
    $stmt->execute();
    

    echo "<ul class='nav nav-pills flex-column'>
              <li class='nav-item'>
                <a class='nav-link active' href='#'><h4>Categories</h4></a>
              </li>";
    foreach ($stmt as $row){

       $cat_id=$row['id'];
       $cat_name=$row['name'];

       echo   "<li class='nav-item'>
                 <a class='nav-link category' href='#' cid='$cat_id'>$cat_name</a>
              </li>";
    }   
        
    
    echo "</ul>";

}


//-------------pagination starts here-------------------
if(isset($_POST['page'])){
    $stmt = $conn->prepare('select * from product');
    $stmt->execute();

    //follow line will give num of rows of products table.
    $count=$stmt->rowCount();
 

    //we want to show 9 products on a page so we will devide it by 9.so we will get no of pages we required to show our all productts.
    //ceil function will convert float  value into integer
    $pageno=ceil($count/9);
    echo '<nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">';

    for($i=1;$i<=$pageno;$i++){
        echo
            "<li class='page-item' >
            <a class='page-link' id='page' page='$i' href='#'>$i</a>
            </li>";
    }
    echo '</ul>
    </nav>';
}//end of isset page-----------it just will give no of pages 

if(isset($_POST['products'])){

    $limit=9;

    if(isset($_POST['setpage'])){
        $pageno=$_POST['pageno'];
        $start=($pageno*$limit) - $limit;
    }else{
        $start=0;
    }
    $stmt = $conn->prepare("select * from product  limit ".$start.",".$limit) ;
    $stmt->execute();

    echo "<div class='row'>";
    if($stmt->rowCount()>0){
        foreach ($stmt as $row)
        {
            echo '<div class="col-md-6 col-lg-4" style="padding: 1%;">
                <div class="card ">
                    <div class="card-header">'.$row['name'].'</div>
                    <div class="card-body" >
                        <img src="'.$row['image'].'" class="card-img img-fluid" style="width:auto; height:40vh;" >
                    </div>
                    <div class="card-footer">$'.$row['price'].' 
                        <button class="btn btn-danger btn-xs"pid='.$row['ref'].' id="product" style="float: right;">Add to cart</button>
                    </div>
                </div>
                </div>';
        }
    }

    echo "</div>";
}//end of isset($_POST['products'])



if(isset($_POST['get_selected_category'])){
    $cid=$_POST['cat_id'];

    $stmt = $conn->prepare('select product from category_product  where category="'.$cid.'"') ;
    $stmt->execute();
    $products=$stmt->fetchAll();
    echo "<div class='row'>";
    if(count($products)){
        foreach ($products as $product){
            $stmt2 = $conn->prepare("select * from product  where ref=:pid") ;
            $stmt2->bindValue('pid', $product[0]);
            $stmt2->execute();
            foreach ($stmt2->fetchAll() as $row)
            {
                $product_id=$row['ref'];
                $product_name=$row['name'];
                $product_price=$row['price'];
                $product_desc=$row['description'];
                $product_image=$row['image'];
                $product_manufacturer=$row['manufacturer'];
                $product_shipping=$row['shipping'];

                echo "
                <div class='col-md-6 col-lg-4' style='padding: 1%;'>
                                    <div class='card'>
                                        <div class='card-header'>$product_name</div>
                                        <div class='card-body'>
                                            <img src='$product_image' class='card-img img-fluid' style='width:auto; height:40vh;'
                                                alt='$product_name'>
                                        </div>
                                        <div class='card-footer'>$ $product_price/-
                                            <button class='btn btn-danger btn-sm' pid='$product_id' id='product' style='float: right;'>Add to cart</button>
                                        </div>
                                    </div>
                                </div>
                ";
            }

        

        }
    }

}//end of isset($_POST['get_selected_category'])



//-----------------query for search functionality---------------------------------------

if(isset($_POST['search'])){
    $searchword=$_POST['searchword'];

    $stmt = $conn->prepare('select * from product where name like "%'.$searchword.'%"') ;
    $stmt->execute();

    echo "<div class='row'>";
    if($stmt->rowCount()>0){
        foreach ($stmt as $row){

        $product_id=$row['ref'];
        $product_name=$row['name'];
        $product_price=$row['price'];
        $product_desc=$row['description'];
        $product_image=$row['image'];
        $product_manufacturer=$row['manufacturer'];
        $product_shipping=$row['shipping'];

        echo "
        <div class='col-md-6 col-lg-4' style='padding: 1%;'>
                            <div class='card'>
                                <div class='card-header'>$product_name</div>
                                <div class='card-body'>
                                    <img src='$product_image' class='card-img img-fluid' style='width:auto; height:40vh;'
                                        alt='$product_name'>
                                </div>
                                <div class='card-footer'>$ $product_price/-
                                    <button class='btn btn-danger btn-sm' pid='$product_id' id='product' style='float: right;'>Add to cart</button>
                                </div>
                            </div>
                        </div>
        ";

        }
    }

}//end of isset($_POST['search'])

//----------------------------------add to cart code starts here-------------------------------
if(isset($_POST['addtoproduct'])){

    if(isset($_SESSION['userid'])){
    /*====================if user is logged in then we will add product into cart with user_id =====================*/
    $p_id = $_POST['productid'];
    $user_id=$_SESSION['userid'];
    
    $stmt = $conn->prepare("select * from order_line where product_id=:p_id AND order_id=(select id from `order` where client=:user_id)") ;
    $stmt->bindValue('p_id', $p_id);
    $stmt->bindValue('user_id', $user_id);
    $stmt->execute();
    $order=$stmt->fetchAll()[0];
    $order_id=$order['order_id'];
    $pro_id=$order['product_id'];

    if(count($order)){
        
        echo "<div class='alert alert-danger' role='alert'>
        product is already added into the cart!...Continue Shopping.
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
            </button>
        </div>";

    }else{

    $stmt = $conn->prepare("select * from products where ref=:pro_id");
    $stmt->bindValue('pro_id', $pro_id);
    $stmt->execute();
    
    if($stmt->rowCount()>0){

        $stmt = $conn->prepare("insert into order_line(order_id,product_id,quantity) values(:order_id,:pro_id,:qty)") ;
        $stmt->bindValue('order', $order_id);
        $stmt->bindValue('pro_id', $pro_id);
        $stmt->bindValue('qty', 1);
        $stmt->execute();
            
        echo "<div class='alert alert-success' role='alert'>
            Product Successfully added to the cart
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
            </button>
        </div>";
        }
    }
  }//if (isset($_SESSION['userid'])) ends
  else{
 /*====================if user is not logged in then we will add product into cart with user_id=-1=====================*/
    $p_id = $_POST['productid'];
    

    $stmt = $conn->prepare("select * from order_line where product_id=:p_id AND order_id=(select id from `order` where client=-1)") ;
    $stmt->bindValue('p_id', $p_id);
    $stmt->execute();
    if($stmt->rowCount()>0){
    
        echo "<div class='alert alert-danger' role='alert'>
        product is already added into the cart!...Continue Shopping(index page)
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
            </button>
        </div>";
        exit();

    }else{
        $stmt = $conn->prepare("select * from product where ref=:pro_id");
        $stmt->bindValue('pro_id', $p_id);
        $stmt->execute();
        if($stmt->rowCount()>0){
            $product = ($stmt->fetchAll())[0];
            $pro_id = $product['ref'];
            $pro_title= $product['name'];
            $pro_image= $product['image'];
            $pro_price= $product['price'];

            $stmt = $conn->prepare("select id from `order` where client=-1") ;
            $stmt->execute();
            $order_id=($stmt->fetchAll())[0]['id'];
            $stmt = $conn->prepare("insert into order_line(order_id,product_id,quantity) values(".$order_id.",".$pro_id.",1)") ;
            $stmt->execute();


                
            echo "<div class='alert alert-success' role='alert'>
                Product Successfully added to the cart.(index pge)
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>";
            
        }
            
    }
   
   }//end of else
}//if(isset($_POST['addtoproduct'])) ends


//----------------------------------add to cart code ends here-------------------------------

//----------------------------------get cart products on profile page & index page cart container-dropdown starts-------------------------------
if(isset($_POST['get_cart_products'])){
  if(isset($_SESSION['userid'])){
    $user_id=$_SESSION['userid'];

    $stmt = $conn->prepare("select product_id from order_line where order_id=(select id from `order` where client=:user_id)");
    $stmt->bindValue('user_id', $user_id);
    $stmt->execute();
    $products=$stmt->fetchAll()[0];
    if(count($products)>0){
      $no=1;
      foreach ($products as $product){
        
        $stmt2=$stmt = $conn->prepare("select * from product where ref=:pro_id");
        $stmt2->bindValue('pro_id', $product);
        $stmt2->execute();
        foreach($stmt2 as $row)
        {
            $pro_id=$row['ref'];
            $pro_title=$row['name'];
            $pro_image=$row['image'];
            $pro_price=$row['price'];
            echo "<div class='row'>
                  <div class='col-md-3'>$no</div>
                  <div class='col-md-3'><img src='$pro_image' style='width:60px; height:70px;'></div>
                  <div class='col-md-3'>$pro_title</div>
                  <div class='col-md-3'>$pro_price</div>
             </div><hr/>";
             $no=$no+1;

        }
        
      
      }
      echo "<a href='cart.php' style:'float:right' class='btn btn-warning'>Edit</a>";
    }else{
      echo "<div class='alert alert-danger' role='alert'>
      Your Cart is Empty
      </div>";
    }
  }else{
//if user is not logged in then we will do it with of id -1

    $stmt = $conn->prepare("select id from `order` where client=-1") ;
    $stmt->execute();
    $order_id=($stmt->fetchAll())[0]['id'];
    $stmt = $conn->prepare("select product_id from order_line where order_id=:order_id") ;
    $stmt->bindValue('order_id', $order_id);
    $stmt->execute();
    $products=$stmt->fetchAll();
    print_r($stmt->fetchAll());
    if(count($products)>0){
        $no=1;
        foreach($products as $product)
        {
        $stmt=$conn->prepare("select * from product where ref=".$product[0]);
        $stmt->execute();
        foreach($stmt->fetchAll() as $row)
        {
            $pro_id=$row['ref'];
            $pro_title=$row['name'];
            $pro_image=$row['image'];
            $pro_price=$row['price'];


            echo "<div class='row'>
                        <div class='col-md-3'>$no</div>
                        <div class='col-md-3'><img src='$pro_image' style='width:60px; height:70px;'></div>
                        <div class='col-md-3'>$pro_title</div>
                        <div class='col-md-3'>$pro_price</div>
                </div><hr/>";
        }
        
        $no=$no+1;    
       }
    echo "<a href='cart.php' style:'float:right' class='btn btn-warning'>Edit</a>";

    }else{
      echo "<div class='alert alert-danger' role='alert'>
      Your Cart is Empty
      </div>";
    }
  }

}
//------------------------------------get cart products on profile page & index page cart container-dropdown ends----------------------------------

//-------------------get cart count on profile page &index page container starts here--------------------------

if(isset($_POST['cart_count'])){
 if(isset($_SESSION['userid'])){
        $user_id=$_SESSION['userid'];

        $stmt = $conn->prepare("select * from order_line where `order_id`=(select id from `order` where client=:user_id)") ;
        $stmt->bindValue('user_id', $user_id);
        $stmt->execute();
        $count=$stmt->rowCount();
        echo $count;
    }else{
        $stmt = $conn->prepare("select * from order_line where `order_id`=(select id from `order` where client=-1)") ;
        $stmt->execute();
        $count=$stmt->rowCount();
        echo $count;
    }
}


//-------------------get cart count on profile page &index page container ends here--------------------------
//-----------------------------------|| cart.php-cart page starts here-----------------------------------------

if(isset($_POST['get_cart_products_list'])){
  
  if(isset($_SESSION['userid'])){
    $user_id=$_SESSION['userid'];
    $stmt = $conn->prepare("select * from order_line where order_id=(select id from `order` where client=".$user_id.")") ;
  }else{
    $stmt = $conn->prepare("select * from order_line where order_id=(select id from `order` where client=-1)") ;
  }
  $stmt->execute();


  $orders=$stmt->fetchAll();

  if(count($orders)>0){
    $total_amt=0;
    $price_array=array();
    echo "<form method='post' action='login_form.php'>";
    foreach($orders as $orderline)
    {
        $p_id=$orderline['product_id'];
        $o_id=$orderline['order_id'];
        $qty=$orderline['quantity'];

        $stmt=$conn->prepare("select * from product where ref=:product");
        $stmt->bindValue("product",$p_id);
        $stmt->execute();
        foreach($stmt->fetchAll() as $row)
        {
            $pid= $row['ref'];
           $pro_title=$row['name'];
           $pro_image=$row['image'];
           $pro_qty=$qty;
           $pro_price=$row['price'];
           $pro_total=round($pro_qty*$pro_price,2);
           $cart_item_id = $o_id;
           
           $price_array[]=$pro_total;
           $total_sum=array_sum($price_array);
           $total_amt=$total_amt + $total_sum;
           
  //setcookie("ta",$total_amt,strtotime("+1 Day"),"/","","",TRUE);

           echo "<div class='row'>
            <div class='col-md-2'>
                <div class='btn-group' role='group' aria-label='Basic example'>
                   <a href='#'><button type='button' pid_remove_item='$pid' id='remove_item' class='btn btn-danger'><i class='fas fa-trash-alt'></i></button></a>
                   <a href='#'> <button type='button' pid_update_item='$pid' id='update_item' class='btn btn-primary'><i class='fas fa-check-square'></i></button></a>
                </div>
            </div>
            <!--follow two items we will fetch at login_form.php page -->
            <input type='hidden' name='pid[]' value=$pid/>
						<input type='hidden' name='' value=$cart_item_id/>
            <div class='col-md-2'><img src='$pro_image' style='width:60px; height:70px;'></div>
            <div class='col-md-2'>$pro_title</div>
            <div class='col-md-2'><input type='text' class='form-control price' pid='$pid' id='price-$pid' value='$pro_price' disabled></div>
            <div class='col-md-2'><input type='text' class='form-control qty' pid='$pid'  id='qty-$pid' value='$pro_qty' ></div>
            <div class='col-md-2'><input type='text' class='form-control total' pid='$pid'  id='total-$pid' value='$pro_total' disabled></div>  
        </div><hr/>";
        }
           
    }
    echo "
    <div class='row'>
       <div class='col-md-8'></div>
       <div class='col-md-4'>
          <b>Total : $ $total_amt</b> 
       </div>
    </div>";
    
  if (!isset($_SESSION["userid"])) {
  //---------------------------if user is not logged in then show him a ready checkout and redirect to login page
    echo '<input type="submit" style="float:right;" name="login_user_with_product" class="btn btn-info " value="Ready to Checkout" >
        </form>';
  }

}//mysqli_num_rows----which fetches added product list is ends here
else{
 
  echo "<div class='alert alert-danger' role='alert'>
  Your Cart is Empty. Please add some Products into the Cart and then you can continue...
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                  <span aria-hidden='true'>&times;</span>
            </button>
          </div>";
          echo" <a href='index.php' class='btn btn-success'>Add Products into Cart</a>";
}
}//if(isset ) ends here



//----------------------------------------|| cart.php-cart page ends here-----------------------------

//-----------------------------remove item from code starts here-----------------------------------
if(isset($_POST['removeFromCart'])){
  $pid=$_POST['removeId'];

  if(isset($_SESSION['userid'])){
  $user_id=$_SESSION['userid'];
  $stmt = $conn->prepare("delete from order_line where product_id=".$pid." and order_id=(select id from `order` where client=".$user_id.")") ;
    
  }else{
    $stmt = $conn->prepare("delete from order_line where product_id=".$pid." and order_id=(select id from `order` where client=-1)") ;
  }
  $stmt->execute();


    echo "<div class='alert alert-danger' role='alert'>
    Product removed from the Cart. Continue Shopping...
   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
   </button>
  </div>";


}

//-----------------------------remove item from code ends here-----------------------------------
//------------------------------------------code for update items of cart starts here--------------------------
if(isset($_POST['updateToCart'])){

  $pid=$_POST['updateId'];
  
  $qty=$_POST['qty'];

  if(isset($_SESSION['userid'])){
     $user_id=$_SESSION['userid'];
     $stmt = $conn->prepare("update order_line set quantity=".$qty." where product_id=".$pid." and order_id=(select id from `order` where client=".$user_id.")") ;
  }else{
    $stmt = $conn->prepare("update order_line set quantity=".$qty." where product_id=".$pid." and order_id=(select id from `order` where client=-1)") ;
  }
  $stmt->execute();

    echo "<div class='alert alert-success' role='alert'>
    Product is Updated Successfully. Continue Shopping...
   <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
   </button>
  </div>";
}


