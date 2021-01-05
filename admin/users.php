<?php
    require_once '../core/init.php';
    if(!is_logged_in()){
        login_error_redirect();
    }
    if(!has_permission('admin')){
        permission_error_redirect('index.php');
    }
    include_once 'includes/head.php';
    include_once 'includes/navigation.php';
    if(isset($_GET['delete'])){
        $delete_id = sanitize($_GET['delete']);
        $db->query("DELETE FROM users WHERE id= '$delete_id'");
        $_SESSION['success_flash'] = 'User has been deleted!';
        header('Location: users.php');
    }

    if(isset($_GET['add'])){
        $name = ((isset($_POST['name']))?sanitize($_POST['name']):'');
        $email = ((isset($_POST['email']))?sanitize($_POST['email']):'');
        $password = ((isset($_POST['password']))?sanitize($_POST['password']):'');
        $confirm = ((isset($_POST['confirm']))?sanitize($_POST['confirm']):'');
        $permissions=((isset($_POST['permissions']))?sanitize($_POST['permissions']):'');
        $errors = array();
        
        if($_POST){

            $emailQuery = $db->query("SELECT * FROM users WHERE email = '$email'");
            $emailCount = mysqli_num_rows($emailQuery);
            if($emailCount != 0){
                $errors[] = 'That email alredy exists in our database';
            }

            $required = array('name', 'email', 'password', 'confirm', 'permissions');
            foreach($required as $f){
                if(empty($_POST[$f])){
                    $errors[] = 'You must fill out all fields';
                break;
                }
            }
            // if(strlen($password < 6)){
            //     $errors[] = 'Your password must be at least 6 characters';
            // }

            if($password != $confirm){
                $errors[] = 'Your passwords do not mach.';
            }

            if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
                $errors[] = 'Your must eneter a valid email.';
            }

    
           if(!empty($errors)){
            echo display_errors($errors);
           }else{
               //add user to database
               $hashed = password_hash($password,PASSWORD_DEFAULT);
               $db->query("INSERT INTO users (full_name, email, `password`, `permissions`) VALUES('$name','$email','$hashed','$permissions')");
               $_SESSION['success_flash'] = 'User has been added!';
               header('Location: users.php');
              
           }
        }
        
        ?>
<h2 class="text-center">Add A New User</h2>
<hr>

<form action="users.php?add=1" method="POST">
    <div class="row">
        <div class="form-groupd col-md-6">
            <label for="name">Full Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="<?=$name;?>">
        </div>
        <div class="form-group col-md-6">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="<?=$email;?>">
        </div>
        <div class="form-group col-md-6">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" value="<?=$password;?>">
        </div>
        <div class="form-group col-md-6">
            <label for="confirm">Confirm Password:</label>
            <input type="password" name="confirm" id="confirm" class="form-control" value="<?=$confirm;?>">
        </div>
        <div class="form-group col-md-6">
            <label for="permissions">Permissions:</label>
            <select class="form-control" name="permissions" id="permissions">
                <option value="" <?=(($permissions == '')?' selected' :''); ?>></option>
                <option value="editor" <?=(($permissions == '')?' selected' :''); ?>>Editor</option>
                <option value="admin,editor" <?=(($permissions == '')?' selected' :''); ?>>Admin</option>
            </select>
        </div>
        <div class="form-group col-md-6 text-right mt-4">
            <a href="users.php" class="btn btn-secondary">Cancel</a>
            <input type="submit" value="Add User" class="btn btn-primary">
        </div>

    </div>
</form>
<?php

    }   else{ 
    $userQuery = $db->query("SELECT * FROM users ORDER BY full_name");


?>

<h2 class="text-center mt-2">Users</h2>
<a href="users.php?add=1" class="btn btn-success float-right">Add New User</a>
<div class="p-3"></div>
<hr>
<table class="table table-border table-striped">
    <thead>
        <th></th>
        <th>Name</th>
        <th>Email</th>
        <th>Join Date</th>
        <th>Last Login</th>
        <th>Permissions</th>
    </thead>
    <tbody>
        <?php while($user = mysqli_fetch_assoc($userQuery)): ?>
        <tr>
            <td>
                <?php if($user['id'] != $user_data['id']): ?>
                <a href="users.php?delete=<?=$user['id'];?>" class="btn btn-secondary"><span><svg width="1em"
                            height="1em" viewBox="0 0 16 16" class="bi bi-trash-fill" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0v-7z" />
                        </svg></span></a>
                <?php endif; ?>
            </td>
            <td><?=$user['full_name'];?></td>
            <td><?=$user['email'];?></td>
            <td><?=pretty_date($user['join_date']);?></td>
            <td><?=(($user['last_login'] == '0000-00-00 00:00:00')?'Never':pretty_date($user['last_login']));?></td>
            <td><?=$user['permissions']; ?></td>
        </tr>
        <?php endwhile;?>

    </tbody>
</table>

<?php }
include 'includes/footer.php';
?>