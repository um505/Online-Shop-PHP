<?php 
require_once $_SERVER['DOCUMENT_ROOT'].'/online-shop/core/init.php';
if(!is_logged_in()){
  login_error_redirect();
}
   include 'includes/head.php';
   include 'includes/navigation.php';

   //use session start and all session

   //deleing product from the database
   if (isset($_GET['delete'])) {
    $delete = (int)$_GET['delete'];
    $delete = sanitize($delete);
     $db->query("update products set deleted=1 where id='$delete'");
     header('Location: products.php');
   }
   $dbpath = '';
   //sql query to select everything from the product table get_child
   $sql = "select * from products where deleted = '0'";
   $p_result = $db->query($sql);

   //adding products first adding brands
   if (isset($_GET['add']) || isset($_GET['edit'])) {
   	$brandQuery = $db->query("select * from brand order by brand");
    $parentQuery = $db->query("select * from categories where parent=0 order by category");
    $title = ((isset($_POST['title']) && $_POST['title']!= '')?sanitize($_POST['title']):'');
    $brand = ((isset($_POST['brand']) && $_POST['brand']!= '')?sanitize($_POST['brand']):'');
    $parent = ((isset($_POST['parent']) && $_POST['parent']!= '')?sanitize($_POST['parent']):'');
    $category = ((isset($_POST['child']) && $_POST['child']!= '')?sanitize($_POST['child']):'');
    $price = ((isset($_POST['price']) && $_POST['price']!= '')?sanitize($_POST['price']):'');
    $list_price = ((isset($_POST['list_price']) && $_POST['list_price']!= '')?sanitize($_POST['list_price']):'');
    $description = ((isset($_POST['description']) && $_POST['description']!= '')?sanitize($_POST['description']):'');
    $sizes = ((isset($_POST['Sizes']) && $_POST['Sizes']!= '')?sanitize($_POST['Sizes']):'');
    $sizes = rtrim($sizes,',');
    $saved_image = '';

    if (isset($_GET['edit'])) {
      $edit_id = (int)$_GET['edit'];
      $productsQuery = $db->query("SELECT * FROM products WHERE id = '$edit_id'");
      $product = mysqli_fetch_assoc($productsQuery);
      if (isset($_GET['delete_image'])) {
        $imgi = (int)$_GET['imgi'] - 1;
        $images = explode(',', $product['image']);
        $image_url = $_SERVER['DOCUMENT_ROOT'].$images[$imgi]; 
        unlink($image_url);
        unset($images[$imgi]);
        $imageString = implode(',', $images);
        $db->query("update products set image = '{$imageString}' where id='$edit_id'");
        header('Location: products.php?edit='.$edit_id);
      }
      $category = ((isset($_POST['child']) && $_POST['child'])?sanitize($_POST['child']):$product['categories']);
      $title = ((isset($_POST['title']) && $_POST['title']!= '')?sanitize($_POST['title']):$product['title']);
      $brand = ((isset($_POST['brand']) && $_POST['brand']!= '')?sanitize($_POST['brand']):$product['brand']);
      $pQuery = $db->query("select * from categories where id= '$category'");
      $pRsult = mysqli_fetch_assoc($pQuery);
      $parent = ((isset($_POST['parent']) && $_POST['parent']!= '')?sanitize($_POST['parent']):$pRsult['parent']);
      $price = ((isset($_POST['price']) && $_POST['price']!= '')?sanitize($_POST['price']):$product['price']);
      $list_price = ((isset($_POST['list_price']))?sanitize($_POST['list_price']):$product['list_price']);
      $description = ((isset($_POST['description']))?sanitize($_POST['description']):$product['description']);
      $sizes = ((isset($_POST['sizes']) && $_POST['sizes']!= '')?sanitize($_POST['sizes']):$product['sizes']);
      $sizes = rtrim($sizes,',');
      $saved_image = ((isset($product['image']) != '')?$product['image']:'');
      $dbpath = $saved_image;

    }

    //this is the sizes and quantity validating for uploading to the database
    if (!empty($sizes)) {
        $sizesString = sanitize($sizes);
        $sizesString = rtrim($sizesString,',');
        $sizesArray = explode(',',$sizesString);
        $sArray = array();
        $qArray = array();
        $tArray = array();
        foreach ($sizesArray as $ss ) {
          $s = explode(':',$ss);
          $sArray[] = $s[0];
          $qArray[] = $s[1];
          $tArray[] = $s[2];
        }
      } else{
        $sizesArray = array();
      }

    if ($_POST) {
      
      
      $errors = array();
        $required = array('title', 'brand', 'price', 'parent', 'child', 'sizes');
        $allowed = array('jpg','jpeg','png', 'gif');

        
        $tmpLoc = array();
        $uploadPath = array();
        foreach ($required as $field) {
          if ($_POST[$field] == '') {
            $errors[] = 'All Field should be filled.';
            break;
          }
        }
      
//validating file  and after multiple file is uploaded configuring in similar way
        
        $PhotoCount = count($_FILES['photo']['name']);
        if($PhotoCount > 0) {
          for($i = 0; $i < $PhotoCount; $i++) {
        //  $photo = $_FILES['photo'];
          $name = $_FILES['photo']['name'][$i];
          $nameArray = explode('.', $name);
          $fileName = $nameArray[0];
          $filExt = $nameArray[1];
          $mime = explode('/', $_FILES['photo']['type'][$i]);
          $mimeType = $mime[0];
          $mimeExt = $mime[1];
          $tmpLoc[] = $_FILES['photo']['tmp_name'][$i];
          $fileSize = $_FILES['photo']['size'][$i];
          
          $uploadName = md5(microtime().$i).'.'.$filExt;
          $uploadPath[] = BASEURL.'images/products/'.$uploadName;
          if ($i != 0) {
            $dbpath .= ',';
          }
          $dbpath .= '/online-shop/images/products/'.$uploadName;

          if ($mimeType != 'image') {
            $errors[] = 'The file must be image.';
          }
          if (!in_array($filExt, $allowed)) {
            $errors[] = 'The file extension must be png, jpg, jpeg, gif and no other type.';
          }
          if($fileSize > 15000000) {
            $errors[] = 'The file size should be under 15MB';
          }
          if ($filExt != $mimeExt && ($mimeExt == 'jpeg' && $filExt != 'jpg')) {
            $errors[]  = 'The file extension does not match the file';
          }
         }
        }
        if(!empty($errors)) {
          echo display_errors($errors);
        } else {
          if($PhotoCount > 0) {
            //upload file and insert into database
            for($i = 0; $i < $PhotoCount; $i++){
                 move_uploaded_file($tmpLoc[$i], $uploadPath[$i]);
            }
          }
          $insertSql = "insert into products(title,price,list_price,brand,image,sizes,description,categories) 
          values('$title','$price','$list_price','$brand','$dbpath','$sizes','$description','$category')";//query to insert all files

          if(isset($_GET['edit'])) {
            $insertSql = "update products set title = '$title', price = '$price', list_price = '$list_price', brand = '$brand', categories = '$category',
            image = '$dbpath', description = '$description', sizes = '$sizes' where id = '$edit_id'";
          }
          $db->query($insertSql);
          //echo "Error message = ".mysql_error();
         header('Location: products.php');
        }
      }else{}//not post
    
   ?>

<!--html start for the start of adding products-->
<h2 class="text-center"><?=((isset($_GET['edit']))?'Edit':'Add A New'); ?> Product</h2>
<form accept="products.php?<?=((isset($_GET['edit']))?'edit='.$edit_id:'add=1'); ?>" method="POST" class=""
    enctype="multipart/form-data">
    <div class="row">
        <div class="form-group col-md-3">
            <label for="title">Title</label>
            <input type="title" name="title" id="title" class="form-control" value="<?=$title; ?>" />
        </div>
        <div class="form-group col-md-3">
            <label for="brand">Brand:</label>
            <select class="form-control" id="brand" name="brand">
                <option value="" <?=(($brand =='')?' selected':''); ?>></option>
                <?php while($b = mysqli_fetch_assoc($brandQuery)): ?>
                <option value="<?=$b['id']; ?>" <?=(($brand ==$b['id'])?' selected':'');?>>
                    <?=$b['brand']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="parent">Parent Category*:</label>
            <select name="parent" id="parent" class="form-control">
                <option value="" <?=(($parent == '')?' selected':''); ?>></option>
                <?php while ($p=mysqli_fetch_assoc($parentQuery)): ?>
                <option value="<?=$p['id']; ?>" <?=(($parent ==$p['id'])?' selected':''); ?>>
                    <?=$p['category']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group col-md-3">
            <label class="child">Child Category*:</label>
            <select class="form-control" name="child" id="child">

            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="price">Price*:</label>
            <input type="text" name="price" id="price" class="form-control" value="<?=$price; ?>" />
        </div>
        <div class="form-group col-md-3">
            <label for="list_price">List Price*:</label>
            <input type="text" name="list_price" id="list_price" class="form-control" value="<?=$list_price; ?>" />
        </div>
        <div class="form-group col-md-3">
            <label for="">Quantity & Sizes</label>
            <button class="btn btn-secondary form-control"
                onclick="$('#Qmodal').modal('toggle'); return false;">Sizes</button>
        </div>
        <div class="form-group col-md-3">
            <label for="sizes">Sizes and Qty Preview</label>
            <input type="text" class="form-control" name="sizes" id="sizes" value="<?=$sizes; ?>" readonly />
        </div>
        <div class="form-group col-md-6">
            <?php if($saved_image != ''): ?>
            <?php 
          $imgi = 1;
          $images = explode(',', $saved_image); 
          foreach($images as $image): ?>
            <div class="saved-image col-md-4">
                <img src="<?=$image; ?>" alt="saved image" class="image-responsive">
                <a href="products.php?delete_image=1&edit=<?=$edit_id;?>&imgi=<?=$imgi;?>" class="btn btn-danger">Delete
                    Image</a>
            </div>
            <?php
          $imgi++;
          endforeach; ?>
            <?php else: ?>
            <label for="photo">Product Photo*:</label>
            <input type="file" name="photo[]" id="photo" class="form-control p-1" multiple>
            <?php endif; ?>
        </div>
        <div class="col-md-6 form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" class="form-control" rows="6"><?=$description; ?></textarea>
        </div>
    </div>
    <div class="row float-right mr-3">
        <div class="form-group">
            <a href="products.php" class="btn btn-secondary">Cancel</a>
            <input type="submit" value="<?=((isset($_GET['edit']))?'Edit':'Add'); ?> Product" class=" btn btn-success">

        </div>

    </div>
</form>

<!-- Start Modal for sizes and quantity-->
<div class="modal fade" id="Qmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
    data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Sizes & Quantity</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <!-- Modal heading-->

            </div>
            <!-- Modal body-->
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <?php for($i = 1; $i<=12;$i++):?>
                        <div class="col-md-2 form-group">
                            <label for="size<?=$i;?>">Size</label>
                            <input type="text" name="size<?=$i;?>" id="size<?=$i;?>"
                                value="<?=((!empty($sArray[$i-1]))?$sArray[$i-1]:''); ?>" class="form-control" />
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="qty<?=$i;?>">Quantity:</label>
                            <input type="number" name="qty<?=$i;?>" id="qty<?=$i;?>"
                                value="<?=((!empty($qArray[$i-1]))?$qArray[$i-1]:''); ?>" min="0"
                                class="form-control" />
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="threshold<?=$i;?>">Threshold:</label>
                            <input type="number" name="threshold<?=$i;?>" id="threshold<?=$i;?>"
                                value="<?=((!empty($tArray[$i-1]))?$tArray[$i-1]:''); ?>" min="0"
                                class="form-control" />
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            <!-- Modal footer-->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary"
                    onclick="updateSizes();$('#Qmodal').modal('toggle'); return false;">Save changes</button>
            </div>
        </div>
    </div>
</div><!-- End Modal -->
<?php }else{



   //Featured product id and all and 
   if (isset($_GET['featured'])) {
   	 $id = (int)$_GET['id'];
   	 $featured = (int)$_GET['featured'];
   	 $F_sql = "update products set featured = '$featured' where id='$id'";
   	 $db->query($F_sql);
   	 header('Location:products.php');
   }
?>

<h2 class="text-center mt-2">Products</h2>
<a href="products.php?add=1" class="btn btn-success pull-right" id="btn-addproduct">Add Product</a>
<div class=""></div>
<hr>
<table class="table table-bordered table-condensed table-striped">
    <thead>
        <th></th>
        <th>Product</th>
        <th>Price</th>
        <th>Category</th>
        <th>Featured</th>
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
   		 ?>
        <tr>
            <td>
                <a href="products.php?edit=<?=$product['id']; ?>" class="btn btn-secondary "><svg width="1em"
                        height="1em" viewBox="0 0 16 16" class="bi bi-pencil-fill" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z" />
                    </svg></a>
                <a href="products.php?delete=<?=$product['id']; ?>" class="btn btn-secondary btn-xs"><svg width="1em"
                        height="1em" viewBox="0 0 16 16" class="bi bi-trash-fill" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0v-7z" />
                    </svg></a>
            </td>
            <td><?=$product['title']; ?></td>
            <td><?=money($product['price']); ?></td>
            <td><?=$category; ?></td>
            <td><a href="products.php?featured=<?=(($product['featured'] == 0)?'1':'0');?>&id=<?=$product['id'];?>"
                    class="btn btn-secondary">
                    <?=(($product['featured'] == 1)?'<svg width="1.3em" height="1.3em" viewBox="0 0 16 16" class="bi bi-dash-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm2.5 7.5a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1h-7z"/>
</svg>':'<svg width="1.3em" height="1.3em" viewBox="0 0 16 16" class="bi bi-plus-square-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm6.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
</svg>');?>
                </a> &nbsp <?=(($product['featured'] == 1)?'Featured Product':'');?></td>
            <td><?=$product['id']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php }
   include 'includes/footer.php';
?>

<script type="text/javascript">
$('document').ready(function() {
    get_child_options('<?=$category;?>');
});
</script>