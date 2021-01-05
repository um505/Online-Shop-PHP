<?php
require_once '../core/init.php';
$id = $_POST['id'];
$id = (int)$id;
$sql = "SELECT * FROM products WHERE id = '$id'";
$result = $db->query($sql);
$product = mysqli_fetch_assoc($result);
$brand_id = $product['brand'];
$sql = "SELECT brand FROM brand WHERE id = '$brand_id'";
$brand_query = $db->query($sql);
$brand = mysqli_fetch_assoc($brand_query);
$sizestring = $product['sizes'];
$sizestring = rtrim($sizestring, ',');
$size_array = explode(',', $sizestring);
?>

<!-- Details Modal -->

<?php ob_start();?>
<div class="modal fade" id="details-modal" data-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel"
    aria-hidden="true" role="dialog" aria-labelledy="details-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title tex-center" id="staticBackdropLabel"><?php echo $product['titel']; ?></h5>
                <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <span id="modal_errors"></span>
            <div class="modal-body">

                <div class="container-fluid">
                    <div class="row">

                        <div class="col-sm-6 fotorama">

                            <?php $photos = explode(',',$product['image']);
                            foreach($photos as $photo): ?>

                            <img src="<?php echo $photo; ?>" alt=" <?php echo $product['titel']; ?>" style="width: 70%;"
                                class="details img-responsive">

                            <?php endforeach; ?>
                        </div>
                        <div class="col-sm-6">
                            <h4>Details</h4>
                            <p><?php echo nl2br($product['description']); ?></p>
                            <hr>
                            <p>Price: $<?php echo $product['price']; ?></p>
                            <p>Brand: <?php echo $brand['brand']; ?></p>
                            <form action="add_cart.php" method="post" id="add_product_form">
                                <input type="hidden" name="product_id" value="<?=$id; ?>">
                                <input type="hidden" name="available" id="available" value="">
                                <div class="form-group">

                                    <label for="quantity">Quantity:</label>
                                    <input type="number" class="form-control col-5" id="quantity" min="0"
                                        name="quantity" placeholder="1">
                                </div>
                                <div class="form-group">
                                    <label for="size">Size:</label>
                                    <select class="form-control col-6" name="size" id="size">
                                        <option value=""></option>
                                        <?php foreach($size_array as $string) {
                                                $string_array = explode(':', $string);
                                                $size = $string_array[0];
                                                $available = $string_array[1];
                                                if($available > 0){
                                                echo'<option value="'.$size.'" data-available="'.$available.'">'.$size.' ('.$available.' Available)</option>';}

                                        }
                                        
                                        ?>


                                    </select>
                                </div>
                            </form>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeModal()">Close</button>
                    <button class="btn btn-warning" onclick="add_to_cart();return false;"><span><svg width="1em"
                                height="1em" viewBox="0 0 16 16" class="bi bi-cart3" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                            </svg></span>Add
                        to Cart</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    $('#size').change(function() {
        var available = $('#size option:selected').data("available");
        $('#available').val(available);
    });

    $(function() {
        $('.fotorama').fotorama({
            'loop': true,
            'autoplay': true
        });
    });

    function closeModal() {
        $('#details-modal').modal('hide')
        setTimeout(function() {
            $('#details-modal').remove();
            $('.modal-backdrop').remove();
        }, 500);
    }
    </script>
    <?php echo ob_get_clean();?>