<?php 
   
   require_once $_SERVER['DOCUMENT_ROOT'].'/online-shop/core/init.php';
   $parentId = (int)$_POST['parentId'];
   $selected = sanitize($_POST['selected']);
   $childQuery = $db->query("select * from categories where parent = '$parentId' order by category");
   
   ob_start(); //predefined php function to start buffering
?>
<option value=""></option>
<?php while($child = mysqli_fetch_assoc($childQuery)): ?>
<option value="<?=$child['id']; ?>" <?=(($selected == $child['id'])?' selected':''); ?>> <?=$child['category']; ?>
</option>
<?php endwhile; ?>
<?php
   echo ob_get_clean();  //
?>