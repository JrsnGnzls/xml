<?php
require_once("../config.php");


if(isset($_POST['saveUser'])) {
    $username = validate($_POST['username']);
    $password = validate($_POST['password']);
    $role = validate($_POST['role']) == true ? 1:0;

    if($username != '' || $password != '') {
        $query = "INSERT INTO users (username,password,role) VALUES ('$username','$pasword','$role')";
        $result = mysqli_query($conn, $query);

        if($result){
            redirect('user.php','User added successfully');
        }
        else{
            redirect('user-create.php','Something went wrong');
        }
    }
    else{
        redirect('user-create.php','Please fill all the input fields');
    }
}

?>