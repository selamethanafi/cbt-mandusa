<?php

require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';


// ======================================================
// AMBIL ID SISWA
// ======================================================

$id_siswa = isset($_GET['id_siswa'])
    ? (int) $_GET['id_siswa']
    : 0;

if ($id_siswa <= 0) {
    die('ID siswa tidak valid.');
}


// ======================================================
// PROSES FORM
// ======================================================

$pesan = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_siswa_post = isset($_POST['id_siswa'])
        ? (int) $_POST['id_siswa']
        : 0;

    $password_baru = $_POST['password_baru'] ?? '';
    $password_ulang = $_POST['password_ulang'] ?? '';


    // Pastikan ID sama
    if ($id_siswa_post !== $id_siswa) {

        $error = 'ID siswa tidak sesuai.';

    } elseif ($password_baru === '') {

        $error = 'Password baru harus diisi.';

    } elseif ($password_baru !== $password_ulang) {

        $error = 'Konfirmasi password tidak sama.';

    } elseif (strlen($password_baru) < 6) {

        $error = 'Password minimal 6 karakter.';

    } else {

        

        // ==============================================
        // UPDATE PASSWORD
        // ==============================================

        $stmt = $db->prepare(
            "UPDATE `siswa`
             SET `password` = ?
             WHERE `id_siswa` = ?"
        );

        if (!$stmt) {

            $error = 'Prepare query gagal: ' . $db->error;

        } else {

            $stmt->bind_param(
                'si',
                $password_baru,
                $id_siswa
            );

            if ($stmt->execute()) {

                $stmt->close();

                header(
                    'Location: siswa.php'
                );

                exit;

            } else {

                $error = 'Gagal mengubah password: '
                    . $stmt->error;

                $stmt->close();
            }
        }
    }
}


// ======================================================
// AMBIL DATA SISWA
// ======================================================

$stmt = $db->prepare(
    "SELECT `id_siswa`, `nis`, `nama_siswa`, `kelas`, `password`
     FROM `siswa`
     WHERE `id_siswa` = ?
     LIMIT 1"
);

$stmt->bind_param(
    'i',
    $id_siswa
);

$stmt->execute();

$result = $stmt->get_result();

$siswa = $result->fetch_assoc();

$stmt->close();


if (!$siswa) {

    die('Data siswa tidak ditemukan.');
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Ubah Password Siswa</title>

    <link rel="stylesheet"
          href="../css/style.css">

    <style>

        .box-password {
            max-width: 500px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fff;
        }

        .info-siswa {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            background: #f5f5f5;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            border: 0;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #777;
            color: white;
        }

        .pesan-error {
            padding: 10px;
            margin-bottom: 15px;
            background: #f8d7da;
            color: #842029;
            border-radius: 5px;
        }

    </style>

</head>

<body>

<div class="box-password">

    <h2>Ubah Password Siswa</h2>


    <!-- ============================================= -->
    <!-- DATA SISWA -->
    <!-- ============================================= -->

    <div class="info-siswa">

        <strong>
            <?php echo htmlspecialchars($siswa['nama_siswa']); ?>
        </strong>

        <br>

        NIS:
        <?php echo htmlspecialchars($siswa['id_siswa']); ?>

        <br>

        Kelas:
        <?php echo htmlspecialchars($siswa['kelas']); ?>

    </div>


    <!-- ============================================= -->
    <!-- PESAN ERROR -->
    <!-- ============================================= -->

    <?php if ($error != '') { ?>

        <div class="pesan-error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php } ?>


    <!-- ============================================= -->
    <!-- FORM PASSWORD -->
    <!-- ============================================= -->

    <form method="post"
          action="ubah_password.php?id_siswa=<?php echo $id_siswa; ?>">

        <input
            type="hidden"
            name="id_siswa"
            value="<?php echo $id_siswa; ?>"
        >


        <div class="form-group">

            <label for="password_baru">
                Password Baru
            </label>

            <input
                type="password"
                id="password_baru"
                name="password_baru"
                required
                minlength="6"
                autocomplete="new-password"
            >

        </div>


        <div class="form-group">

            <label for="password_ulang">
                Ulangi Password Baru
            </label>

            <input
                type="password"
                id="password_ulang"
                name="password_ulang"
                required
                minlength="6"
                autocomplete="new-password"
            >

        </div>


        <button
            type="submit"
            class="btn btn-primary"
        >
            Simpan Password
        </button>


        <a
            href="ikutkansusulan.php?id_siswa=<?php echo $id_siswa; ?>"
            class="btn btn-secondary"
        >
            Batal
        </a>

    </form>

</div>

</body>

</html>
