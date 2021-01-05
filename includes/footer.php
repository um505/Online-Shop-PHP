</div>
</div>
</div>
<footer class="text-center" id="footer"> &copy; Copyright 2020

</footer>





<script>
jQuery(window).scroll(function() {
    var vscroll = jQuery(this).scrollTop();
    jQuery('#logotext').css({
        "transform": "translate(0px, " + vscroll / 2 + "px)"
    });
    var vscroll = jQuery(this).scrollTop();
    jQuery('#logotext').css({
        "transform": "translate(0px, " + vscroll + "px)"
    });
});

function detailsmodal(id) {
    var data = {
        "id": id
    };
    jQuery.ajax({
        url: '/online-shop/includes/detailsmodal.php',
        method: "post",
        data: data,
        success: function(data) {
            $('body').append(data);
            $('#details-modal').modal('toggle');
        },
        error: function() {
            alert("Something went wrong!")
        }
    });
}

function update_cart(mode, edit_id, edit_size) { //extra pains 
    var data = {
        "mode": mode,
        "edit_id": edit_id,
        "edit_size": edit_size
    };
    $.ajax({
        url: '/online-shop/admin/parsers/update_cart.php',
        method: "post",
        data: data,
        success: function() {
            location.reload();
        },
        error: function() {
            alert("Something went wrong")
        },
    });
}



function add_to_cart() {
    $('#modal_errors').html("");
    var size = $('#size').val();
    var quantity = $('#quantity').val();
    var available = $('#available').val();
    var error = '';
    var data = $('#add_product_form').serialize();
    if (size == '' || quantity == '' || quantity == 0) {
        error += '<p class="text-center text-danger">You must choose a size and quantity.</p>';
        $('#modal_errors').html(error);
        return;
    } else if (quantity > available) {
        error += '<p class="text-center text-danger"> There are only ' + available + ' available.</p>';
        $('#modal_errors').html(error);
        return;
    } else {
        $.ajax({
            url: '/online-shop/admin/parsers/add_cart.php',
            method: 'post',
            data: data,
            success: function() {
                location.reload();
            },
            error: function() {
                alert("Something went wrong");
            }

        });
    }
}
</script>
</body>

</html>