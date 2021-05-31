<?php

include "db.php";

$login=$_POST['login'];
$email=$_POST['email'];
$pass=$_POST['pass'];



if(empty($login) || empty($email) || empty($pass)){

    echo "<div class='alert alert-warning' role='alert'>
            All fields are required
            </div>";
    exit();
}else{


    //check if email is already existed in our db or not
    $stmt = $conn->prepare('select id from client where email = :email limit 1');
    $stmt->bindValue('email', $email);
    $stmt->execute();
    if($stmt->rowCount()>0){
        echo "<div class='alert alert-danger' role='alert'>
                This Email Id is already Registered. Please Sign In to Continue.
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                </button>
                </div>";
    }else{
        $stmt = $conn->prepare('insert into client (login, email, password) values(:login, :email, :password)');
        $stmt->bindValue('login', $login);
        $stmt->bindValue('email', $email);
        $stmt->bindValue('password', $pass);
        $stmt->execute();
        echo "<div class='alert alert-success' role='alert'>
            You Have Registered Successfully
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
        </button>
        </div>";

    }


    
}//end of else (form validation)
?>