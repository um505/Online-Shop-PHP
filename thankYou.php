<?php 

require_once 'core/init.php';

   $full_name = sanitize($_POST['full_name']);
   $email = sanitize($_POST['email']);
   $street = sanitize($_POST['street']);
   $street2 = sanitize($_POST['street2']);
   $city = sanitize($_POST['city']);
   $state = sanitize($_POST['state']);
   $zip_code = sanitize($_POST['zip_code']);
   $country = sanitize($_POST['$country']);  

   $tax = sanitize($_POST['tax']);
   $sub_total = sanitize($_POST['sub_total']);
   $grand_total = sanitize($_POST['grand_total']);
   $cart_id = sanitize($_POST['cart_id']);
   $description = sanitize($_POST['description']);
   $metadata = array(
   	'cart_id'    =>  $cart_id,
   	'tax'        =>  $tax,
   	'sub_total'  =>  $sub_total,
   );

//update inventory
$itemQ = $db->query("select * from cart where id='{$cart_id}'");
$iresults = mysqli_fetch_assoc($itemQ);
$items = json_decode($iresults['items'], true);
foreach ($items as $item) {
   $newSizes = array();
   $item_id = $item['id'];
   $productQ = $db->query("select sizes from products where id = '{$item_id}'");
   $product = mysqli_fetch_assoc($productQ);
   $sizes = sizesToArray($product['sizes']);
   foreach ($sizes as $size) {
      if ($size['size'] == $item['size']) {
         $q = $size['quantity'] - $item['quantity'];
         $newSizes[] = array('size' => $size['size'],'quantity' => $q);
      } else {
         $newSizes[] = array('size' => $size['size'],'quantity' => $size['quantity']);
      }
   }
   $sizeString = sizesToString($newSizes);
   $db->query("update products set sizes = '{$sizeString}' where id = '{$item_id}'");
}
//update cart
$db->query("UPDATE cart SET paid = 1 WHERE id = '{$cart_id}'");
if(!$db->query("INSERT INTO transactions (cart_id,full_name,email,street,street2,city,state,zip_code,country,sub_total,tax,grand_total,description) VALUES
('$cart_id','$full_name','$email','$street','$street2','$city','$state','$zip_code','$country','$sub_total','$tax','$grand_total','$description')")){
     echo("Error description: " . $db -> error);
}

   
$domain = ($_SERVER['HTTP_HOST'] != 'localhost')? '.'.$_SERVER['HTTP_HOST']:false;
setcookie(CART_COOKIE,'',1,'/',$domain,false);

include 'includes/head.php';
include 'includes/navigation.php';
include 'includes/headerpartial.php';
?>
<div class="container border rounded shadow mt-5 text-center">
    <h1 class="text-center text-success mt-2">Thank You!</h1>
    <p> Your cart has been successfully charged <?=money($grand_total);?>. You have been emailed a receipt. Please check
        your spam folder if it is not on your inbox. <br> Additianally you can print this page as a receipt. </p>
    <p>Your receipent number is: <strong><?=$cart_id;?></strong> </p>
    <p>Your order will be shipped to the address below.</p>
    <address>
        <?=$full_name;?><br>
        <?=$street;?><br>
        <?=(($street2 != '')?$street2.'<br>':'');?><br>
        <?=$city. ', '.$state.' '.$zip_code;?><br>
        <?=$country;?><br>
    </address>
</div>
<?php include 'includes/footer.php';
   ?>