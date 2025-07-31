<?php
require_once 'DBconn.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$perPage = 7;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM users";
$countSql = "SELECT COUNT(*) FROM users";
$params = [];
$searchParams = [];

if (!empty($search)) {
    $sql .= " WHERE name LIKE :search OR email LIKE :search";
    $countSql .= " WHERE name LIKE :search OR email LIKE :search";
    $searchParams = [':search' => "%$search%"];
}


$stmt = $dbh->prepare($countSql);
$stmt->execute($searchParams);
$totalUsers = $stmt->fetchColumn();
$totalPages = max(ceil($totalUsers / $perPage), 1);
$page = min($page, $totalPages);
$sql .= " LIMIT :limit OFFSET :offset";
$params = array_merge($searchParams, [
    ':limit' => $perPage,
    ':offset' => $offset
]);


$stmt = $dbh->prepare("SELECT name FROM users WHERE id = ?");
$stmt->execute([$_SESSION['id']]);
$loggedInUser = $stmt->fetch(PDO::FETCH_ASSOC);
$loggedInName = $loggedInUser ? htmlspecialchars($loggedInUser['name']) : "User";
$stmt = $dbh->prepare($sql);


foreach ($params as $key => &$val) {
    $stmt->bindParam($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link href="bootstrap-icons-1.11.3\package\font\bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .ig-nav {
            background-color: #000000, 0.2;
            border-bottom: 1px solid #333;
            padding: 12px 0;
        }

        .ig-table {
            background: #000;
            border: 1px solid #333;
            border-radius: 12px;
            overflow: hidden;
        }

        .ig-table thead {
            background: #161616;
            border-bottom: 2px solid #333;
        }

        .ig-table th {
            padding: 16px 24px;
            border: none;
            font-weight: 600;
            vertical-align: middle;
        }

        .ig-table td {
            padding: 12px 24px;
            border-top: 1px solid #333;
            vertical-align: middle;
        }

        .ig-btn-primary {
            background: #0095f6;
            border: none;
            border-radius: 8px;
            color: #fff !important;
            padding: 6px 16px;
        }

        .ig-search {
            background: #000 !important;
            border: 1px solid #333 !important;
            color: #fff !important;
            border-radius: 8px;
        }

        .table-container {
            min-height: calc(5 * 50px);
            overflow-y: auto;
        }

        .table-container table thead {
            position: sticky;
            top: 0;
            background: #161616;
            z-index: 1;
        }

        .empty-row {
            height: 53px;
            padding: 12px 24px !important;
        }
    </style>
</head>

<body>
    <nav class="ig-nav fixed-top">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="bi bi-people"></i>
                    Login System
                </h3>
                <div class="d-flex align-items-center"> 
                    <span class="me-3">@<?= $loggedInName ?></span>
                    <a href="logout.php" class="btn ig-btn-primary btn-sm">logout <i class="bi bi-box-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container" style="padding-top: 80px;">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-dark alert-dismissible fade show" role="alert">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="create.php" class="btn ig-btn-primary"><i class="bi bi-plus-lg"></i> Add User</a>
            <form method="GET" class="d-flex" style="width: 300px;">
                <div class="input-group">
                    <input type="text" name="search" class="form-control ig-search" placeholder="Search..."
                        value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn ig-btn-primary"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>

        <!-------------------------Table------------------------------------>
        <div class="ig-table">
            <table class="table table-dark mb-0">
                <thead class="fs-4">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="p-3">
                    <?php
                    $rowCount = 0;
                    foreach ($users as $user): $rowCount++; ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= $user['created_at'] ?></td>
                            <td>
                                <!-----------UPDATE BUTTON----------->
                                <div class="d-flex gap-2">
                                    <a href="update.php?id=<?= $user['id'] ?>" class="btn btn-sm ig-btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <!-----------DELETE BUTTON----------->
                                    <form method="POST" action="delete.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!--------------------------pagination controll--------------------------->
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-dark flex-wrap">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

    <script src="icon.js"></script>
    <script src="bootstrap-5.0.2-dist/js/bootstrap.bundle.js"></script>
</body>

</html>
