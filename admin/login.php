<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/online-shop/core/init.php';
// if (!is_logged_in()) {
//   login_error();
// }
   include 'includes/head.php';
   $email = ((isset($_POST['email']))?sanitize($_POST['email']):'');
   $email = rtrim($email);
   $password = ((isset($_POST['password']))?sanitize($_POST['password']):'');
   $password = rtrim($password);

   $errors = array();
?>
<style>
body {
    background-image: url("/online-shop/images/hero-header2.jpg");
    background-size: 100vw 100%;
    background-attachment: fixed;
}
</style>

<div id="login-form">
    <div>
        <?php
        if($_POST){

            
            //form validation
            if(empty($_POST['email']) || empty($_POST['password'])){
                $errors[]= 'You must provide email and password';
            }
            //validate email
            if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
                $errors[] = 'You must enter a valid email!';

            }
            
            //password validation
            if(strlen($password) < 6){
                $errors[]='Password must be at least 6 characters!';
            } 
           

            // check if email exists
            $query = $db->query("SELECT * FROM users WHERE email = '$email'");
            $user = mysqli_fetch_assoc($query);
            $userCount = mysqli_num_rows($query); 
            if($userCount < 1){
                $errors[] = 'That email doesn\'t exist in our database';
            }
            if (!password_verify($password, $user['password'])) {
                $errors[] = 'The password doesnot match our records. Please try again.';
            }
           
            //check for errors
            if(!empty($errors)){
                echo display_errors($errors);
            }else{
                //log user in 
                $user_id = $user['id'];
                login($user_id); 
            }
        }
    ?>
    </div>
    <h2 class="text-center">Login</h2>
    <form action="login.php" method="POST">

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?=$email;?>">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" value="<?=$password;?>">
        </div>
        <div class="form-group">
            <input type="submit" name="login" value="Log in" class="btn btn-primary">
        </div>

    </form>
    <p class="text-right"> <a href="/online-shop/index.php" alt="home">Visit Site</a> </p>
</div>
<?php 
   include 'includes/footer.php';
?>