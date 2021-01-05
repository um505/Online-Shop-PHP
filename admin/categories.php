<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/online-shop/core/init.php';
    if(!is_logged_in()){
        login_error_redirect();
    }
    include 'includes/head.php';
    include 'includes/navigation.php';

    $sql="SELECT * FROM categories WHERE parent = 0";
    $result = $db->query($sql);
    $errors = array();
    $category='';
    $post_parent = '';

    //Edit Category
    if(isset($_GET['edit']) && !empty($_GET['edit'])){
        $edit_id = (int)$_GET['edit'];
        $edit_id = sanitize($edit_id);
        $edit_sql = "SELECT * FROM categories WHERE id = '$edit_id'";
        $edit_result = $db->query($edit_sql);
        $edit_category = mysqli_fetch_assoc($edit_result);


    }

    //Delete Category
    if(isset($_GET['delete']) && !empty($_GET['delete'])){
        $delete_id = (int)$_GET['delete'];
        $delete_id = sanitize($delete_id);
        $sql ="SELECT * FROM categories WHERE id= '$delete_id' ";
        $result =  $db->query($sql);
        $category = mysqli_fetch_assoc($result);
        if($category['parent'] == 0){
            $sql = "DELETE FROM categories WHERE parent = '$delete_id'";
            $db->query($sql);
        } 
        $dsql = "DELETE FROM categories WHERE id = '$delete_id'";
        $db->query($dsql);
        header('Location: categories.php');

    }

    //Process Form
    if(isset($_POST) && !empty($_POST)){
        $post_parent = sanitize($_POST['parent']);
        $category =sanitize($_POST['category']);
        $sqlform = "SELECT * FROM categories WHERE category = '$category' AND parent ='$post_parent'";
        if(isset($_GET['edit'])){
            $id = $edit_category['id'];
            $sqlform = "SELECT * FROM categories WHERE category = '$category' AND parent ='post_parent' AND id != '$id'"; 

        }
        $fresult = $db->query($sqlform);
        $count = mysqli_num_rows($fresult);

        // if category is blank
        if($category == ''){
            $errors[] .='The category cannot be left blank.';

        }
        //If alredy exist
        if($count > 0){
            $errors[] .= $category .' alredy exists. Please choose a new category.';

        }

        //Display Errors or Update Database
        if(!empty($errors)){
            //display errors
            $display = display_errors($errors); ?>
<script>
jQuery('document').ready(function() {
    jQuery('#errors').html('<?php echo $display; ?>');
});
</script>

<?php }else{
                //Update database
                $updatesql ="INSERT INTO categories (category, parent) VALUES ('$category','$post_parent')";
                if(isset($_GET['edit'])){
                    $updatesql = "UPDATE categories SET category = '$category', parent = '$post_parent' WHERE id ='$edit_id' ";
                }
                $db->query($updatesql);
                header('Location: categories.php');


        }
    }
    $category_value = '';
    $parent_value = 0;
    if(isset($_GET['edit'])){
        $category_value = $edit_category['category'];
        $parent_value = $edit_category['parent'];
    }else{
        if(isset($_POST)){
            $category_value = $category;
            $parent_value = $post_parent;
        }
    }

?>
<h2 class="text-center">Categories</h2>
<hr>
<div class="row">
    <!-- form -->
    <div class="col-md-6">
        <form class="form" action="categories.php<?php echo((isset($_GET['edit']))?'?edit='.$edit_id:'');?>"
            method="post">
            <legend><?php echo((isset($_GET['edit']))?'Edit':'Add A');?> Category</legend>
            <div id="errors"></div>
            <div class="form-group">
                <label for="parent">Parent</label>
                <select class="form-control" name="parent" id="parent">
                    <option value="0" <?php echo (($parent_value == 0)?'selected="selected"':'');?>>Parent</option>
                    <?php while($parent = mysqli_fetch_assoc($result)) :?>
                    <option value="<?php echo $parent['id'];?>"
                        <?php echo (($parent_value == $parent['id'])?'selected="selected"':'');?>>
                        <?php echo $parent['category']?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" class="form-control" id="category" name="category"
                    value="<?php echo $category_value;?>">
            </div>
            <div class="form-group">

                <input class="btn btn-success" type="submit"
                    value="<?php echo((isset($_GET['edit']))?'Edit':'Add');?> Category">
            </div>
        </form>




    </div>

    <!-- category table -->
    <div class="col-md-6">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Categori</th>
                    <th scope="col">Parent</th>
                    <th scope="col"></th>

                </tr>
            </thead>
            <tbody>

                <?php 
                  $sql="SELECT * FROM categories WHERE parent = 0";
                  $result = $db->query($sql);
                while($parent = mysqli_fetch_assoc($result)): 
                $parent_id = (int)$parent['id'];
                $sql2 = "SELECT * FROM categories WHERE parent ='$parent_id'";
                $cresult = $db->query($sql2);
                    ?>

                <tr class="bg-secondary">
                    <th scope="row"><?php echo $parent['category'];?></th>
                    <td>Parent</td>
                    <td>
                        <a href="categories.php?edit=<?php echo $parent['id']; ?>" class="btn btn-secondary"> <svg
                                width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pen" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M13.498.795l.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001zm-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708l-1.585-1.585z" />
                            </svg></a>
                        <a href="categories.php?delete=<?php echo $parent['id']; ?>" class="btn btn-secondary"><svg
                                width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                <path fill-rule="evenodd"
                                    d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                            </svg> </a>
                    </td>

                </tr>
                <?php while($child = mysqli_fetch_assoc($cresult)):?>
                <tr class="bg-light">
                    <th scope="row"><?php echo $child['category'];?></th>
                    <td><?php echo $parent['category'];?></td>
                    <td>
                        <a href="categories.php?edit=<?php echo $child['id']; ?>" class="btn btn-secondary"> <svg
                                width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pen" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M13.498.795l.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001zm-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708l-1.585-1.585z" />
                            </svg></a>
                        <a href="categories.php?delete=<?php echo $child['id']; ?>" class="btn btn-secondary"><svg
                                width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                <path fill-rule="evenodd"
                                    d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                            </svg> </a>
                    </td>

                </tr>

                <?php endwhile; ?>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</div>
<?php
include 'includes/footer.php';
?>