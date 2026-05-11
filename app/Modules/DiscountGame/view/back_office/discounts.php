<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../controller/AdminDiscountController.php';

$controller = new AdminDiscountController();
$errors = [];
$flash = $_SESSION['admin_discount_flash'] ?? null;
unset($_SESSION['admin_discount_flash']);

$editingDiscount = null;

if (isset($_GET['edit'])) {
    $editingDiscount = $controller->show((int) $_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';
    $payload = [
        'discount_identifier' => trim($_POST['discount_identifier'] ?? ''),
        'discount_type' => trim($_POST['discount_type'] ?? ''),
        'discount_percentage' => (int) ($_POST['discount_percentage'] ?? 0),
        'release_date' => $_POST['release_date'] ?? '',
        'expiration_date' => $_POST['expiration_date'] ?? '',
    ];

    if ($action === 'delete') {
        $controller->delete((int) ($_POST['id'] ?? 0));
        $_SESSION['admin_discount_flash'] = 'Discount deleted successfully.';
        header('Location: discounts.php');
        exit;
    }

    if ($action === 'update') {
        $result = $controller->update((int) ($_POST['id'] ?? 0), $payload);

        if ($result['success']) {
            $_SESSION['admin_discount_flash'] = 'Discount updated successfully.';
            header('Location: discounts.php');
            exit;
        }

        $errors = $result['errors'] ?? [];
        $editingDiscount = array_merge(['id' => (int) ($_POST['id'] ?? 0)], $payload);
    } else {
        $result = $controller->store($payload);

        if ($result['success']) {
            $_SESSION['admin_discount_flash'] = 'Discount added successfully.';
            header('Location: discounts.php');
            exit;
        }

        $errors = $result['errors'] ?? [];
        $editingDiscount = $payload;
    }
}

$discounts = $controller->index();
$formData = $editingDiscount ?? [
    'id' => '',
    'discount_identifier' => '',
    'discount_type' => '',
    'discount_percentage' => '',
    'release_date' => '',
    'expiration_date' => '',
];
$isEditing = isset($formData['id']) && $formData['id'] !== '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voyagio | Admin Discounts</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<link rel="stylesheet" href="../../../../../public/assets/css/return-dashboard.css">
<a class="voyagio-return-dashboard" href="../../../../../back.php">&larr; Back Dashboard</a>

    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">VO</div>
                <div>
                    <div class="brand-title">Voyagio</div>
                    <div class="brand-subtitle">Back Office</div>
                </div>
            </div>
            <nav class="menu">
                <a href="index.php" class="menu-item">Dashboard</a>
                <a href="games.php" class="menu-item">Games</a>
                <a href="discounts.php" class="menu-item active">Discounts</a>
            </nav>
        </aside>

        <main class="content">
            <header class="topbar">
                <div>
                    <div class="page-title">Discounts Management</div>
                    <div class="page-subtitle">Add, update, and review Voyagio discount campaigns from the back office.</div>
                </div>
                <div class="user-card">
                    <span>Admin</span>
                    <div class="avatar">AD</div>
                </div>
            </header>

            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h2><?= $isEditing ? 'Update Discount' : 'Add Discount' ?></h2>
                        <p>Manage discount id, type, release date, and expiration date.</p>
                    </div>
                    <a href="index.php" class="btn btn-primary">Back Office</a>
                </div>

                <?php if ($flash): ?>
                    <div class="success-banner"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <?php if ($errors): ?>
                    <div class="error-banner">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form class="panel-form" method="post">
                    <input type="hidden" name="action" value="<?= $isEditing ? 'update' : 'create' ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($formData['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

                    <label for="discount_identifier">
                        Discount id
                        <input id="discount_identifier" name="discount_identifier" type="text" value="<?= htmlspecialchars((string) $formData['discount_identifier'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label for="discount_type">
                        Discount type
                        <input id="discount_type" name="discount_type" type="text" value="<?= htmlspecialchars((string) $formData['discount_type'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label for="discount_percentage">
                        Discount percentage
                        <input id="discount_percentage" name="discount_percentage" type="number" min="1" max="100" value="<?= htmlspecialchars((string) $formData['discount_percentage'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label for="release_date">
                        Date of release
                        <input id="release_date" name="release_date" type="date" value="<?= htmlspecialchars((string) $formData['release_date'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <label for="expiration_date">
                        Date expired
                        <input id="expiration_date" name="expiration_date" type="date" value="<?= htmlspecialchars((string) $formData['expiration_date'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </label>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><?= $isEditing ? 'Update discount' : 'Add discount' ?></button>
                        <?php if ($isEditing): ?>
                            <a href="discounts.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>

                <div class="table-card">
                    <table class="records-table">
                        <thead>
                            <tr>
                                <th>Discount id</th>
                                <th>Type</th>
                                <th>Percentage</th>
                                <th>Release</th>
                                <th>Expiration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!$discounts): ?>
                                <tr>
                                    <td colspan="6">No discounts added yet.</td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($discounts as $discount): ?>
                                <tr>
                                    <td><?= htmlspecialchars($discount['discount_identifier'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($discount['discount_type'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= (int) $discount['discount_percentage'] ?>%</td>
                                    <td><?= htmlspecialchars($discount['release_date'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($discount['expiration_date'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <a class="action-link" href="discounts.php?edit=<?= (int) $discount['id'] ?>">Edit</a>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $discount['id'] ?>">
                                            <button type="submit" class="action-link danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <?php if ($flash): ?>
        <script>
            alert(<?= json_encode($flash, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>);
        </script>
    <?php elseif ($errors): ?>
        <script>
            alert(<?= json_encode(implode("\n", $errors), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>);
        </script>
    <?php endif; ?>
</body>
</html>
