</div>
</div>
</div>
<footer class="footer border shadow text-center" id="footer">
    <h4 class="m-1">&copy; Copyright 2020 <br> Maxim Ulyanov</h4>
</footer>
<script>
function updateSizes() {
    var sizeString = '';
    for (var i = 1; i <= 12; i++) {
        if ($('#size' + i).val() != '') {
            sizeString += $('#size' + i).val() + ':' + $('#qty' + i).val() + ':' + $('#threshold' + i).val() + ',';
        }
    }
    $('#sizes').val(sizeString);
}

function get_child_options(selected) {
    if (typeof selected == 'undefined') {
        var selected = '';
    }

    var parentId = $('#parent').val();
    $.ajax({ //jQuery.ajax
        url: '/online-shop/admin/parsers/child_categories.php',
        type: 'POST',
        data: {
            parentId: parentId,
            selected: selected
        },
        success: function(data) {
            $('#child').html(data);
        },
        errors: function() {
            alert('Something went wrong with child option.')
        },
    });
}
$('select[name="parent"]').change(function() {
    get_child_options();
});
</script>
</body>

</html>