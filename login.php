<?php
include "db.php";
session_start();

#Login script is begin here
#If user given credential matches successfully with the data available in database then we will echo string login_success

if(isset($_POST['loginemail']) && isset($_POST['password'])){
    $email=$_POST['loginemail'];
    $password=$_POST['password'];

    $stmt=$conn->prepare('select * from client where email="'.$email.'" and password="'.$password.'"');
    $stmt->execute();
    $row=($stmt->fetchAll())[0];
    if(!empty($row)){

        $_SESSION['userid']=$row['id'];
        $_SESSION['login']=$row['login'];
       

        //we have created cookie at login_form.php page,so if cookie is available that means user is not logged in
        //so will check if cookie is set or not
        if(isset($_COOKIE["product_list"])){

            $p_list = stripcslashes($_COOKIE["product_list"]);
			//here we are decoding stored json product list cookie to normal array
            $product_list = json_decode($p_list,true);


    for($i=0; $i<count($product_list);$i++){
             
          $j=$product_list[$i]; //it will return result with / character. eg if pid is 14 then it will retur 14/.so we need to remove / from the string
          $k=trim($j,"/");//so we will use trim() function to remove / from the string.
        // echo $k;

        //After getting user id from database here we are checking user cart item if there is already product is listed or not
        #$verify_cart = "SELECT id FROM cart WHERE user_id = $_SESSION[uid] AND p_id = ".$product_list[$i];
          $verify_cart="select * from order_line where product_id =$k and order_id=(select id from `order` where client=$_SESSION[userid] ) ";
        //  echo $verify_cart;
          $stmt=$conn->prepare($verify_cart);
          $stmt->execute();
       
           if($stmt->rowCount()<1){
                   
                //if user is adding first time product into cart we will update user_id into database table with valid id
                 $update_cart_query = "update `order` set client = '$_SESSION[userid]' where client = -1";
                 $stmt2=$conn->prepare($update_cart_query);
                 $stmt2->execute();
               
           }else{

                    //if already that product is available into database table we will delete that record
                    #$delete_existing_product = "DELETE FROM cart WHERE user_id = -1 AND ip_add = '$ip_add' AND p_id = ".$product_list[$i];
                    $delete_existing_item="delete from `order` where client= -1 ";
                    $stmt2=$conn->prepare($delete_existing_item);
                    $stmt2->execute();

                
            }
      
    }//end of for loop


            //here we are destroying user cookie
            setcookie("product_list","",strtotime("-1 day"),"/");
            //if user is logging from after cart page we will send cart_login
            exit();            


        }else{
            //if cookie is not set means user is login from the index.php page's dropdown login form so we will send login_success
			exit();
        }
        $stmt=$conn->prepare("delete from client where id=-1");
        $stmt->execute();
        header("location:profile.php");
    }else{
        echo "<span style='color:red;'>Sorry, Credentials Don't Match...</span>";
        exit();
    }
}

?>