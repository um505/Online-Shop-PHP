<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/online-shop/core/init.php';
if (!is_logged_in()) {
  login_error();
}
   include 'includes/head.php';
   include 'includes/navigation.php';
   $sql = "select * from products where deleted = '1'";
   $p_result = $db->query($sql);
?>

<h2 class="text-center mt-2">Archived Products</h2>

<table class="table table-bordered table-condensed table-striped">
    <thead>
        <th></th>
        <th>Product</th>
        <th>Price</th>
        <th>Category</th>
        <th>Sold</th>
    </thead>
    <tbody>
        <?php while($product=mysqli_fetch_assoc($p_result)):
   		//for the category in the table
   		  $childId = $product['categories'];
   		  $catsql ="select * from categories where id='$childId'";
        $catresult = $db->query($catsql);
        $cat = mysqli_fetch_assoc($catresult);
        $parentId = $cat['parent'];
        $psql = "select * from categories where id='$parentId'";
        $presult = $db->query($psql);
        $parent = mysqli_fetch_assoc($presult);
        $category = $parent['category'].'->'.$cat['category'];

        if (isset($_GET['deleted'])) {
            $id = (int)$_GET['id'];
            $deleted = (int)$_GET['deleted'];
            $D_sql = "update products set deleted = '$deleted' where id='$id'";
            $db->query($D_sql);
            header('Location:archived.php');
       }
   		 ?>
        <tr>
            <td><a href="archived.php?deleted=<?=(($product['deleted'] == 0)?'1':'0');?>&id=<?=$product['id'];?>"
                    class="btn btn-secondary">
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-repeat" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z" />
                        <path fill-rule="evenodd"
                            d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z" />
                    </svg>
                </a> </td>

            <td><?=$product['title']; ?></td>
            <td><?=money($product['price']); ?></td>
            <td><?=$category; ?></td>
            <td>0</td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php
   include 'includes/footer.php';
?>