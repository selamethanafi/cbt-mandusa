<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

$data = [];

if ($keyword !== '') {

    $cari = "%" . $keyword . "%";

    $stmt = $db->prepare("
        SELECT 
            id_siswa,
            nama_siswa,
            kelas,
            rombel
        FROM siswa
        WHERE nama_siswa LIKE ?
           OR kelas LIKE ?
           OR rombel LIKE ?
        ORDER BY nama_siswa ASC
    ");

    $stmt->bind_param("sss", $cari, $cari, $cari);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pencarian Peserta Ujian</title>

    <link rel="stylesheet" href="../css/style.css">

    <style>
        .box-pencarian {
            max-width: 700px;
            margin: 30px auto;
            padding: 20px;
        }

        .form-cari {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-cari input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .btn-cari {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: white;
            cursor: pointer;
        }

        .btn-cari:hover {
            background: #0069d9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 9px;
            border: 1px solid #ddd;
        }

        th {
            background: #f2f2f2;
            text-align: center;
        }

        td {
            text-align: left;
        }

        .tengah {
            text-align: center;
        }

        .btn-pilih {
            display: inline-block;
            padding: 6px 10px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn-pilih:hover {
            background: #218838;
        }

        .info {
            margin-bottom: 15px;
        }

        .kosong {
            padding: 15px;
            text-align: center;
            background: #f8f8f8;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="box-pencarian">

        <h3>Pencarian Peserta Ujian</h3>

        <form method="get" class="form-cari">

            <input
                type="text"
                name="keyword"
                value="<?= htmlspecialchars($keyword) ?>"
                placeholder="Ketik nama siswa, kelas, atau rombel..."
                autofocus
            >

            <button type="submit" class="btn-cari">
                Cari
            </button>

        </form>

        <?php if ($keyword !== ''): ?>

            <div class="info">
                Hasil pencarian:
                <strong><?= htmlspecialchars($keyword) ?></strong>
                — ditemukan <strong><?= count($data) ?></strong> peserta.
            </div>

            <?php if (count($data) > 0): ?>

                <table>

                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama Siswa</th>
                            <th width="80">Kelas</th>
                            <th width="100">Rombel</th>
                            <th width="80">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php $no = 1; ?>

                    <?php foreach ($data as $row): ?>

                        <tr>

                            <td class="tengah">
                                <?= $no++ ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['nama_siswa']) ?>
                            </td>

                            <td class="tengah">
                                <?= htmlspecialchars($row['kelas']) ?>
                            </td>

                            <td class="tengah">
                                <?= htmlspecialchars($row['rombel']) ?>
                            </td>

                            <td class="tengah">

                                <a
                                    href="hasil_siswa.php?id=<?= urlencode($row['id_siswa']) ?>"
                                    class="btn-pilih"
                                >
                                    Pilih
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            <?php else: ?>

                <div class="kosong">
                    Peserta dengan kata kunci
                    <strong><?= htmlspecialchars($keyword) ?></strong>
                    tidak ditemukan.
                </div>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</div>

</body>
</html>