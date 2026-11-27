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


// ======================================================
// VALIDASI
// ======================================================
if ($id_siswa <= 0) {

    echo '<h3>ID siswa tidak valid</h3>';
    echo '<a href="menu.php">Kembali ke Menu</a>';

    exit;
}


// ======================================================
// AMBIL DATA SISWA
// ======================================================
$stmt = $db->prepare(
    "SELECT `id_siswa`, `nama_siswa`, `kelas`
     FROM `siswa`
     WHERE `id_siswa` = ?
     LIMIT 1"
);

$stmt->bind_param('i', $id_siswa);
$stmt->execute();

$result_siswa = $stmt->get_result();

$siswa = $result_siswa->fetch_assoc();

$stmt->close();


if (!$siswa) {

    echo '<h3>Data siswa tidak ditemukan</h3>';
    echo '<a href="menu.php">Kembali ke Menu</a>';

    exit;
}


// ======================================================
// AMBIL DAFTAR UJIAN SUSULAN
// ======================================================
$stmt = $db->prepare(
    "SELECT `tes_id`
     FROM `siswa_absen`
     WHERE `nis` = ?
     ORDER BY `tes_id` ASC"
);

$stmt->bind_param('i', $id_siswa);
$stmt->execute();

$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Siswa Tertunda</title>

    <link rel="stylesheet"
          href="../css/style.css">

    <style>

        .box-susulan {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
        }

        .info-siswa {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f8f8f8;
        }

        .ujian {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            background: #007bff;
            color: white;
        }

        .btn:hover {
            background: #0056b3;
        }

        .kosong {
            padding: 15px;
            border-radius: 8px;
            background: #f5f5f5;
        }

    </style>

</head>

<body>

<div class="box-susulan">

    <h2>Daftar Tes / Tugas Tertunda</h2>
<p><a class="btn btn-primary" href="menu.php">Menu</a></p>

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
    <!-- DAFTAR UJIAN -->
    <!-- ============================================= -->

    <?php

    if ($result->num_rows == 0) {

        echo '<div class="kosong">';
        echo 'Tidak ada tugas / tes tertunda.';
        echo '</div>';

    } else {

        while ($row = $result->fetch_assoc()) {

            $tes_id = $row['tes_id'];
	    ?>

            <div class="ujian">
                <div>

                    <strong>
                        <?php echo $tes_id; ?>
                    </strong>
			<?php
			if($tes_id > 0)
			{
				$ada = 0;
			$ta = mysqli_query($db, "SELECT `nama_soal` FROM `ujian_aktif` WHERE `kode_soal` like '%$tes_id'");
			while($da = mysqli_fetch_assoc($ta))
			{
				echo $da['nama_soal'].'<br />';
				$ada++;
			}
			}
			?>
			
                </div>

                <div>
<?php
			if(($tes_id > 0) and ($ada > 0))
			{?>
                    <a
                        class="btn"
                        href="hapus_tugas_tertunda.php?id_siswa=<?php echo $id_siswa; ?>&tes_id=<?php echo $tes_id; ?>"
                    >
                        Hapus
                    </a>
                    <?php
                    }
                    ?>
                </div>
            </div>
	<?php
        }
    }

    $stmt->close();

    ?>

</div>

</body>

</html>
