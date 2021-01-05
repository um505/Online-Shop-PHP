<?php
    require_once '../core/init.php';
    if(!is_logged_in()){
        login_error_redirect();
    }
    include_once 'includes/head.php';
    include_once 'includes/navigation.php';
    
    $sql = "SELECT * FROM brand ORDER BY brand ";
    $results = $db->query($sql);
    $errors = array();

    //Edit Brand
    if(isset($_GET['edit']) && !empty($_GET['edit'])){
        $edit_id = (int)$_GET['edit'];
        $edit_id = sanitize($edit_id);
        $sql2 = "SELECT * FROM brand WHERE id = '$edit_id'";
        $edit_result = $db->query($sql2);
        $eBRand = mysqli_fetch_assoc($edit_result);

    }

    //DELETE brand
    if(isset($_GET['delete']) && !empty($_GET['delete'])){
        $delete_id = (int)$_GET['delete'];
        $delete_id = sanitize($delete_id);
        $sql = "DELETE FROM brand WHERE id = '$delete_id'";
        $db->query($sql);
        header('Location: brands.php');
    }
    
    // If add form submitted
    if(isset($_POST['add_submit'])){
        $brand = sanitize( $_POST['brand']);
        if($_POST['brand'] == ''){
            $errors[] .= 'You must eneter a brand!';
        }
        $sql = "SELECT * FROM brand WHERE brand = '$brand'";
        if(isset($_GET['edit'])){
            $sql = "SELECT * FROM brand WHERE brand = '$brand' AND id != '$edit_id'";

        }
        $result = $db->query($sql);
        $count = mysqli_num_rows($result);
        if($count > 0){
            $errors[] .= $brand.' alredy exists. Please choose another brand name.';


        }
//Display errros
        if(!empty($errors)){
            echo display_errors($errors);
    
        } else{
            $sql = "INSERT INTO brand (brand) VALUES ('$brand')";
            if(isset($_GET['edit'])){
                $sql = "UPDATE brand SET brand = '$brand' WHERE id ='$edit_id' ";
            }
            $db->query($sql);
            header('Location: brands.php');
        }

    }
?>
<h2 class="text-center">Brands</h2>
<hr>
<!-- Brand form -->
<div class="m-3" id="add-brand">
    <form class="form-inline" action="brands.php<?php echo ((isset($_GET['edit']))?'?edit='.$edit_id :'');?>"
        method="post">
        <div class="form-group">
            <?php 
            $brand_value = '';
            if(isset($_GET['edit'])){
                $brand_value = $eBRand['brand'];

                }
                else{
                        if(isset($_POST['brand'])){
                            $brand_value = sanitize($_POST['brand']);
                        }
                }?>
            <label class="mr-3" for="brand"> <?php echo ((isset($_GET['edit']))?'Edit' : 'Add a');?> Brand</label>
            <input type="text" name="brand" id="brand" class="form-control mr-3" value="<?php echo $brand_value; ?>">
            <?php if (isset($_GET['edit'])) :?>
            <a href="brands.php" class="btn btn-secondary mr-2">Cancel</a>
            <?php endif; ?>


            <input type="submit" name="add_submit" value="<?php echo ((isset($_GET['edit']))?'Edit' : 'Add');?> Brand"
                class="btn btn-success">
        </div>
        <hr>

    </form>
</div>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th></th>
            <th>Brand</th>
            <th></th>

        </tr>
    </thead>
    <tbody>
        <?php while ($brand = mysqli_fetch_assoc($results)) : ?>
        <tr>
            <td><a href="brands.php?edit=<?php echo $brand['id']; ?>" class="btn btn-xs btn-default"><svg width="1em"
                        height="1em" viewBox="0 0 16 16" class="bi bi-pen" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M13.498.795l.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001zm-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708l-1.585-1.585z" />
                    </svg></a></td>
            <td><?php echo $brand['brand']; ?></td>
            <td><a href="brands.php?delete=<?php echo $brand['id']; ?>" class="btn btn-xs btn-default"><svg width="1em"
                        height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                        <path fill-rule="evenodd"
                            d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                    </svg></a></td>

        </tr>
        <?php endwhile; ?>
    </tbody>
</table>


<?php
include 'includes/footer.php';
?>