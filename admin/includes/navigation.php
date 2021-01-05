<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/online-shop/admin/index.php">Online Shop Admin</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">My Dashboard</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="brands.php">Brands</a>
            </li>
            <li lass="nav-item active">
                <a class="nav-link" href="categories.php">Categories</a>
            </li>
            <li lass="nav-item active">
                <a class="nav-link" href="products.php">Products</a>
            </li>
            <li lass="nav-item active">
                <a class="nav-link" href="archived.php">Archived</a>
            </li>
            <?php if(has_permission('admin')):?>
            <li lass="nav-item active">
                <a class="nav-link" href="users.php">Users</a>
            </li>
            <?php endif;?>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"> Hello
                    <?=$user_data['first'];?>!</a>
                <ul class="dropdown-menu" role="menu">
                    <li><a class="nav-link" href="change_password.php">Change Password</a></li>
                    <li><a class="nav-link" href="logout.php">Log Out</a></li>
                </ul>
                <span class="caret"></span>
            </li>
            <!-- <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false"> </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                 <a class="dropdown-item" href="#"></a>
            </div>
            </li> -->
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>