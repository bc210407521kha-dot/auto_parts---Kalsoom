<!-- navbar.php -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm py-3">
    <div class="container">

        <!-- Logo -->
        <a class="navbar-brand fw-bold fs-3 text-danger" href="index.php">PartsLo.pk <br>
            <span class="text-dark fs-6">Auto Parts Online Store</span>
        </a>

        <!-- Search + Category Filter -->
        <form class="d-flex ms-auto me-4 search-box" style="width:55%" method="GET" action="search.php">
            <input class="form-control me-2" type="search" name="query" placeholder="Search auto parts...">
            <select class="form-select me-2" name="category">
                <option value="">All Categories</option>
                <?php
                $categories_result->data_seek(0);
                while ($cat = $categories_result->fetch_assoc()):
                ?>
                    <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                <?php endwhile; ?>
            </select>
            <button class="btn btn-danger" type="submit">🔍</button>
        </form>

        <!-- Login / Register / Cart -->
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_type'] == 'admin'): ?>
                    <a href="admin_dashboard.php" class="btn btn-outline-dark me-2">Admin Panel</a>
                <?php else: ?>
                    <a href="buyer_dashboard.php" class="btn btn-outline-dark me-2">My Account</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-dark me-3">Logout</a>
                <a href="cart.php" class="position-relative">
                    <span class="fs-4">🛒</span>
                    <?php if ($cart_count > 0): ?>
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle"><?= $cart_count ?></span>
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-dark me-2">Login</a>
                <a href="register.php" class="btn btn-dark me-3">Sign Up</a>
                <a href="login.php" class="position-relative">
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>