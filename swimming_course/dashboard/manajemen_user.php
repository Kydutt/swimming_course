<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../proses/login.php'); exit;
}
require_once '../function.php';
global $conn;

$current_page = 'manajemen_user';
$errors  = [];
$success = '';

// ── Atur Role (AJAX-style GET) ──────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'set_role' && isset($_GET['id'], $_GET['role'])) {
    $uid   = (int)$_GET['id'];
    $role  = in_array($_GET['role'], ['admin','user']) ? $_GET['role'] : 'user';
    // Jangan izin admin hapus role dirinya sendiri
    if ($uid !== (int)($_SESSION['user_id'] ?? 0)) {
        $conn->query("UPDATE user SET role='$role' WHERE id_user=$uid");
        header('Location: manajemen_user.php?success=role_updated'); exit;
    }
}

// ── Hapus User ───────────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $uid = (int)$_GET['id'];
    if ($uid !== (int)($_SESSION['user_id'] ?? 0)) {
        $conn->query("DELETE FROM user WHERE id_user=$uid");
        header('Location: manajemen_user.php?success=deleted'); exit;
    }
}

// ── Tambah User (POST) ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = in_array($_POST['role'] ?? '', ['admin','user']) ? $_POST['role'] : 'user';

    if (empty($name))     $errors[] = 'Nama harus diisi.';
    if (empty($email))    $errors[] = 'Email harus diisi.';
    if (empty($password)) $errors[] = 'Password harus diisi.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

    if (empty($errors)) {
        // Cek duplikat email
        $stmt = $conn->prepare("SELECT id_user FROM user WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Email sudah terdaftar.';
        } else {
            $stmt->close();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $name, $email, $hash, $role);
            if ($stmt->execute()) {
                $stmt->close();
                header('Location: manajemen_user.php?success=added'); exit;
            }
            $errors[] = 'Gagal menambah user.';
        }
        $stmt->close();
    }
}

// ── Edit User (POST) ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $uid      = (int)$_POST['id_user'];
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = in_array($_POST['role'] ?? '', ['admin','user']) ? $_POST['role'] : 'user';

    if (empty($name))  $errors[] = 'Nama harus diisi.';
    if (empty($email)) $errors[] = 'Email harus diisi.';

    if (empty($errors)) {
        if (!empty($password)) {
            if (strlen($password) < 6) { $errors[] = 'Password minimal 6 karakter.'; }
            else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE user SET name=?, email=?, password=?, role=? WHERE id_user=?");
                $stmt->bind_param('ssssi', $name, $email, $hash, $role, $uid);
                $stmt->execute(); $stmt->close();
            }
        } else {
            $stmt = $conn->prepare("UPDATE user SET name=?, email=?, role=? WHERE id_user=?");
            $stmt->bind_param('sssi', $name, $email, $role, $uid);
            $stmt->execute(); $stmt->close();
        }
        if (empty($errors)) { header('Location: manajemen_user.php?success=updated'); exit; }
    }
}

// ── Ambil data untuk edit (mode edit) ───────────────────────────
$edit_user = null;
if (isset($_GET['edit'])) {
    $uid = (int)$_GET['edit'];
    $res = $conn->query("SELECT id_user, name, email, role FROM user WHERE id_user=$uid");
    if ($res && $res->num_rows) $edit_user = $res->fetch_assoc();
}

// ── Ambil semua user ─────────────────────────────────────────────
$result    = $conn->query("SELECT id_user, name, email, role, created_at FROM user ORDER BY created_at DESC");
$user_list = [];
while ($row = $result->fetch_assoc()) { $user_list[] = $row; }

$flash_map = [
    'added'        => ['User baru berhasil ditambahkan.',  'alert-success'],
    'updated'      => ['Data user berhasil diperbarui.',   'alert-success'],
    'deleted'      => ['User berhasil dihapus.',           'alert-success'],
    'role_updated' => ['Role user berhasil diperbarui.',   'alert-success'],
];
$flash     = $flash_map[$_GET['success'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/manajemen_user.css">
</head>
<body>
<?php require_once 'partials/topbar.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>
<?php require_once 'partials/icons.php'; ?>
<div class="layout-body">
<main class="page-wrapper">
    <?php $breadcrumb_title = 'Manajemen User'; require_once 'partials/breadcrumb.php'; ?>
    <h1 class="page-title"><?= icon('users',22) ?> Manajemen User</h1>
    <p class="page-subtitle">Kelola akun pengguna — tambah, edit, dan atur role</p>

    <?php if ($flash): ?>
    <div class="mu-alert alert-success" id="flashMsg">
        <?= icon('check',16) ?> <?= $flash[0] ?>
    </div>
    <?php endif; ?>

    <div class="mu-layout">
        <!-- ── Tabel user ── -->
        <div class="mu-table-col">
            <div class="card">
                <div class="card-header">
                    <h2><?= icon('users',18) ?> Daftar User (<?= count($user_list) ?>)</h2>
                    <div class="card-header-actions">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Cari nama, email...">
                        </div>
                        <a href="manajemen_user.php?add=1" class="btn btn-primary">+ Tambah User</a>
                    </div>
                </div>
                <div class="table-container">
                    <?php if (!empty($user_list)): ?>
                    <table id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Atur Role</th>
                                <th>Tgl Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach ($user_list as $u):
                                $init     = strtoupper(substr($u['name'], 0, 1));
                                $is_self  = (int)$u['id_user'] === (int)($_SESSION['user_id'] ?? 0);
                                $av_grad  = $u['role']==='admin' ? 'linear-gradient(135deg,#8b5cf6,#a78bfa)' : 'linear-gradient(135deg,#3b82f6,#06b6d4)';
                            ?>
                            <tr>
                                <td><span class="id-badge"><?= ++$no ?></span></td>
                                <td>
                                    <div class="name-cell">
                                        <div class="name-avatar" style="background:<?= $av_grad ?>; color:#fff;"><?= $init ?></div>
                                        <div>
                                            <span class="name-text"><?= htmlspecialchars($u['name']) ?></span>
                                            <?php if ($is_self): ?><br><span style="font-size:.7rem;color:#2563eb;font-weight:700;">● Anda</span><?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td style="color:#475569;"><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <span class="status-badge role-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span>
                                </td>
                                <td>
                                    <?php if (!$is_self): ?>
                                    <div class="role-btns">
                                        <?php if ($u['role'] !== 'admin'): ?>
                                        <a href="manajemen_user.php?action=set_role&id=<?= $u['id_user'] ?>&role=admin"
                                           class="role-btn role-btn-admin"
                                           onclick="return confirm('Jadikan <?= htmlspecialchars($u['name']) ?> sebagai Admin?')">
                                            <?= icon('shield-user',13) ?> Admin
                                        </a>
                                        <?php else: ?>
                                        <a href="manajemen_user.php?action=set_role&id=<?= $u['id_user'] ?>&role=user"
                                           class="role-btn role-btn-user"
                                           onclick="return confirm('Turunkan ke role User?')">
                                            <?= icon('user',13) ?> User
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                    <?php else: ?>
                                    <span style="color:#94a3b8;font-size:.8rem;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= !empty($u['created_at']) ? date('d/m/Y', strtotime($u['created_at'])) : '—' ?></td>
                                <td>
                                    <div class="mu-actions">
                    <a href="manajemen_user.php?edit=<?= $u['id_user'] ?>" class="btn btn-edit"><?= icon('pencil',14) ?> Edit</a>
                                        <?php if (!$is_self): ?>
                                        <a href="manajemen_user.php?action=delete&id=<?= $u['id_user'] ?>"
                                           class="btn btn-delete"
                                           onclick="return confirm('Hapus user <?= htmlspecialchars($u['name']) ?>?')"><?= icon('trash',14) ?></a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="no-data"><div class="no-data-icon"><?= icon('inbox',40) ?></div><p>Belum ada data user.</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ── Panel: Tambah / Edit ── -->
        <?php $show_panel = isset($_GET['add']) || $edit_user !== null || !empty($errors); ?>
        <?php if ($show_panel): ?>
        <div class="mu-form-col">
            <div class="mu-panel">
                <div class="mu-panel-header">
                    <span class="mu-panel-icon"><?= $edit_user ? icon('pencil',20) : icon('plus',20) ?></span>
                    <span class="mu-panel-title"><?= $edit_user ? 'Edit User' : 'Tambah User Baru' ?></span>
                </div>
                <div class="mu-panel-body">
                    <?php if (!empty($errors)): ?>
                    <div class="mu-errors">
                        <strong><?= icon('warning', 16) ?> Perbaiki:</strong>
                        <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="manajemen_user.php">
                        <input type="hidden" name="action" value="<?= $edit_user ? 'edit' : 'tambah' ?>">
                        <?php if ($edit_user): ?>
                        <input type="hidden" name="id_user" value="<?= $edit_user['id_user'] ?>">
                        <?php endif; ?>

                        <div class="mu-fg">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" placeholder="Nama lengkap" required
                                   value="<?= htmlspecialchars($edit_user['name'] ?? $_POST['name'] ?? '') ?>">
                        </div>
                        <div class="mu-fg">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="nama@email.com" required
                                   value="<?= htmlspecialchars($edit_user['email'] ?? $_POST['email'] ?? '') ?>">
                        </div>
                        <div class="mu-fg">
                            <label>Password <?= $edit_user ? '(kosongkan jika tidak diubah)' : '' ?></label>
                            <input type="password" name="password" placeholder="Min. 6 karakter" <?= $edit_user ? '' : 'required' ?>>
                            <?php if ($edit_user): ?>
                            <small>Biarkan kosong untuk mempertahankan password lama.</small>
                            <?php endif; ?>
                        </div>
                        <div class="mu-fg">
                            <label>Role</label>
                            <select name="role">
                                <option value="user"  <?= ($edit_user['role'] ?? 'user') === 'user'  ? 'selected':'' ?>><?= icon('user',13) ?> User</option>
                                <option value="admin" <?= ($edit_user['role'] ?? '')      === 'admin' ? 'selected':'' ?>><?= icon('shield-user',13) ?> Admin</option>
                            </select>
                        </div>

                        <div style="display:flex; gap:10px; margin-top:20px;">
                            <a href="manajemen_user.php" class="btn" style="background:#f1f5f9;color:#475569;flex:1;justify-content:center;"><?= icon('x-circle',14) ?> Batal</a>
                            <button type="submit" class="btn btn-primary" style="flex:2;">
                                <?= $edit_user ? icon('save',14).' Simpan' : icon('plus',14).' Tambah' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div><!-- .mu-layout -->
</main>
</div>

<?php require_once 'partials/scripts.php'; ?>
<script src="../assets/js/manajemen_user.js"></script>
</body>
</html>
