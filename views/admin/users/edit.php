<?php

use app\models\Session;

global $SITE_URL;
$title = 'Edit Administrator | Admin Panel';
$admin_user = $data['admin'] ?? null;

if (!$admin_user) {
    header('Location: ' . Router::getRoute('/admin/users'));
    exit;
}

$header_actions = [
    [
        'label' => 'Back to Users',
        'url' => Router::getRoute('/admin/users'),
        'class' => 'btn-outline-secondary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
    ]
];

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit Administrator</h2>
    </div>

    <div class="card-body">
        <form id="admin-form" action="<?= Router::getRoute('/api/admin/users/admin/' . $admin_user->id) ?>" method="post">
            <div class="form-group">
                <label for="username">Username*</label>
                <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($admin_user->username) ?>" required>
                <div class="invalid-feedback" data-error-for="username"></div>
            </div>

            <div class="form-group">
                <label for="email">Email*</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($admin_user->email) ?>" required>
                <div class="invalid-feedback" data-error-for="email"></div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control">
                <small class="form-text text-muted">Leave blank to keep the current password</small>
                <div class="invalid-feedback" data-error-for="password"></div>
            </div>

            <div class="form-group">
                <label for="role">Role*</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="admin" <?= $admin_user->role === 'admin' ? 'selected' : '' ?>>Administrator</option>
                    <?php if ($user->role === 'super_admin'): ?>
                        <option value="super_admin" <?= $admin_user->role === 'super_admin' ? 'selected' : '' ?>>Super Administrator</option>
                    <?php endif; ?>
                </select>
                <div class="invalid-feedback" data-error-for="role"></div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?= Router::getRoute('/admin/users') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['users.js'];
include('views/admin/layout.php');
?>