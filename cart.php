<?php 
require_once 'core/init.php';
include 'includes/head.php'; 
include 'includes/navigation.php'; 
include 'includes/headerpartial.php'; 

    if($cart_id != ''){
        $cartQ = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}'");
        $result = mysqli_fetch_assoc($cartQ);
        $items = json_decode($result['items'],true); 
        $i = 1;
        $sub_total = 0;
        $item_count = 0;

    }
?>


<h2 class="text-center mt-1">My Shopping Cart</h2>
<hr>
<div class="row">
    <div class="col-md-12">
        <?php if($cart_id == ''): ?>
        <div>
            <p class="text-center text-danger">Your shopping cart is empty!</p>
        </div>
        <?php else: ?>
        <table class="table table-bordered table-striped">
            <thead>
                <th>#</th>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Size</th>
                <th>Sub Total</th>
            </thead>
            <tbody>
                <?php
        foreach($items as $item){
            $product_id = $item['id'];
            $productQ = $db->query("SELECT * FROM products WHERE id = '{$product_id}'");
            $product = mysqli_fetch_assoc($productQ);
            $sArray = explode(',',$product['sizes']);
            foreach($sArray as $sizeString){
                $s = explode(':',$sizeString);
                if($s[0] == $item['size']){
                    $available = $s[1];

                }
            }
            ?>
                <tr>
                    <td><?=$i; ?></td>
                    <td><?=$product['title'];?></td>
                    <td><?=money($product['price']);?></td>
                    <td>
                        <button class="btn btn-xs btn-secondary"
                            onclick="update_cart('removeone','<?=$product['id'];?>','<?=$item['size'];?>');">-</button>
                        <?=$item['quantity'];?>
                        <?php if($item['quantity'] < $available) :?>
                        <button class="btn btn-xs btn-secondary"
                            onclick="update_cart('addone','<?=$product['id'];?>', '<?=$item['size'];?>');">+</button>
                        <?php else: ?>
                        <span class="text-danger">Maximum</span>
                        <?php endif; ?>
                    </td>



                    <td><?=$item['size'];?></td>
                    <td><?=money($item['quantity'] * $product['price']);?></td>
                </tr>
                <?php
                $i++;
                $item_count += $item['quantity'];
                $sub_total += ($product['price'] * $item['quantity']);

            } 
            $tax = TAXRATE * $sub_total;
            $tax = number_format($tax,2);
            $grand_total = $tax + $sub_total;
            ?>

            </tbody>

        </table>

        <table class="table table-bordered">
            <legend>Totals</legend>
            <thead>
                <th>Total Items</th>
                <th>Sub Total</th>
                <th>Tax (20%)</th>
                <th>Grand Total</th>
            </thead>
            <tbody class="text-right">
                <tr>
                    <td><?=$item_count; ?></td>
                    <td><?=money($sub_total); ?></td>
                    <td><?=money($tax); ?></td>
                    <td class="bg-success"><?=money($grand_total); ?></td>
                </tr>
            </tbody>
        </table>
        <!-- Check Out button -->
        <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#checkoutModal">
            Check Out >> <span><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-cart3" fill="currentColor"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                </svg></span>
        </button>

        <!-- Modal -->
        <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="checkoutModalLabel">Shipping Address</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">

                            <form action="thankYou.php" method="post" id="payment-form">
                                <span class="bg-danger" id="payment-errors"></span>
                                <input type="hidden" name="tax" value="<?=$tax;?>">
                                <input type="hidden" name="sub_total" value="<?=$sub_total;?>">
                                <input type="hidden" name="grand_total" value="<?=$grand_total;?>">
                                <input type="hidden" name="cart_id" value="<?=$cart_id;?>">
                                <input type="hidden" name="description"
                                    value="<?=$item_count.' item'.(($item_count>1)?'s':'').' from Online-Shop.';?>">
                                <div id="step1" style="display:block;">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="full_name">Full Name:</label>
                                            <input type="text" class="form-control" id="full_name" name="full_name">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="email">Email:</label>
                                            <input type="email" class="form-control" id="email" name="email">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="street">Street Address:</label>
                                            <input type="text" class="form-control" id="street" name="street">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="street2">Street Address 2:</label>
                                            <input type="text" class="form-control" id="street2" name="street2">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="city">City:</label>
                                            <input type="text" class="form-control" id="city" name="city">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="state">State:</label>
                                            <input type="text" class="form-control" id="state" name="state">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="zip_code">Zip Code:</label>
                                            <input type="text" class="form-control" id="zip_code" name="zip_code">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="country">Country:</label>
                                            <input type="text" class="form-control" id="country" name="country">
                                        </div>
                                    </div>
                                </div>

                                <div id="step2" style="display:none;">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="name">Name on Card:</label>
                                            <input type="text" id="name" class="form-control" name="">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="number">Card Number:</label>
                                            <input type="text" id="number" class="form-control" name="">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label for="cvc">CVC:</label>
                                            <input type="text" id="cvc" class="form-control" name="">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="exp-month">Expire Month:</label>
                                            <select id="exp-month" class="form-control" name="">
                                                <option value=""></option>
                                                <?php for($i=1; $i<13; $i++) :?>
                                                <option value="<?=$i;?>"><?=$i;?></option>
                                                <?php endfor;?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="exp-year">Expire Year:</label>
                                            <select id="exp-year" class="form-control" name="">
                                                <option value=""></option>
                                                <?php 
                                        $yr = date("Y");
                                        for($i=0; $i<11; $i++) :?>

                                                <option value="<?=$yr + $i;?>"><?=$yr + $i;?></option>

                                                <?php endfor;?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="check_address();" id="next_button">Next
                            >></button>
                        <button type="button" class="btn btn-primary" onclick="back_address();" id="back_button"
                            style="display:none;">
                            << Back</button>
                                <button type="submit" class="btn btn-primary" id="checkout_button"
                                    style="display:none;">Check Out</button>
                                </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<script>
function back_address() {
    $('#payment-errors').html("");
    $('#step1').css("display", "block");
    $('#step2').css("display", "none");
    $('#next_button').css("display", "inline-block");
    $('#back_button').css("display", "none");
    $('#checkout_button').css("display", "none");
    $('#checkoutModalLabel').html("Shipping Address");
}

function check_address() {
    var data = {
        'full_name': $('#full_name').val(),
        'email': $('#email').val(),
        'street': $('#street').val(),
        'street2': $('#street2').val(),
        'city': $('#city').val(),
        'state': $('#state').val(),
        'zip_code': $('#zip_code').val(),
        'country': $('#country').val(),
    };

    $.ajax({
        url: '/online-shop/admin/parsers/check_address.php',
        method: 'POST',
        data: data,
        success: function(data) {
            if (data != 'passed') {
                $('#payment-errors').html(data);

            }
            if (data == 'passed') {
                $('#payment-errors').html("");
                $('#step1').css("display", "none");
                $('#step2').css("display", "block");
                $('#next_button').css("display", "none");
                $('#back_button').css("display", "block");
                $('#checkout_button').css("display", "inline-block");
                $('#checkoutModalLabel').html("Enter Your Card Details");
            }
        },
        error: function() {
            alert("Something Went Wrong");
        },
    });
}
</script>
<?php
include 'includes/footer.php';
?>