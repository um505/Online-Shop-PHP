<?php 
$sql= "SELECT *FROM categories WHERE parent = 0";
$pquery = $db->query($sql);
?>

<!-- Top navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Online Shop</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <?php while($parent = mysqli_fetch_assoc($pquery)) : ?>
            <?php $parent_id = $parent['id']; 
            $sql2 = "SELECT * FROM categories WHERE parent = '$parent_id'";
            $cquery = $db->query($sql2);
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <?php echo $parent['category']; ?>
                </a>

                <div class="dropdown-menu" aria-labelledby="navbarDropdown">


                    <?php while($child = mysqli_fetch_assoc($cquery)) : ?>
                    <a class="dropdown-item"
                        href="category.php?cat=<?=$child['id']; ?>"><?php echo $child['category']; ?></a>
                    <?php endwhile; ?>
                </div>
            </li>
            <?php endwhile; ?>

            <!-- <li class="nav-item"><a class="nav-link" href="cart.php"><span><svg width="1em" height="1em"
                            viewBox="0 0 16 16" class="bi bi-cart3" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                        </svg></span>My Cart</a></li> -->
        </ul>

        <form class="form-inline my-2 my-lg-0">
            <a class="btn btn-outline-dark mr-1" href="cart.php"><span><svg width="1em" height="1em" viewBox="0 0 16 16"
                        class="bi bi-cart3" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                    </svg></span>My Cart</a>
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>