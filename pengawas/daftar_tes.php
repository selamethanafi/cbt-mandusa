<?php
require_once '../inc/config.php';
require_once '../inc/fungsi.php';
require_once '../inc/admin.php';

$hal = 0;

if (isset($_GET['hal'])) {
    $hal = (int) $_GET['hal'];
}

$limit = $hal * 10;

$semester = cari_semester();
$ajaran = cari_thnajaran();

/*
 * =====================================================
 * PENCARIAN
 * =====================================================
 */
$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';

$where = '';

if ($cari !== '') {

    $cari_aman = mysqli_real_escape_string($db, $cari);

    $where = "WHERE ua.kode_soal LIKE '%$cari_aman%'
              OR ua.nama_soal LIKE '%$cari_aman%'";
}


/*
 * =====================================================
 * QUERY DATA TES
 * =====================================================
 */
$query = "SELECT 
            ua.id_ujian,
            ua.kode_soal,
            ua.nama_soal,
            ua.mapel,
            ua.kelas,
            ua.tampilan_soal,
            ua.status,
            ua.token,
            ua.waktu_ujian,
            ua.tanggal,
            ua.tanggal_selesai,
            COUNT(s.id) AS cacah_soal

        FROM ujian_aktif ua

        LEFT JOIN soal s 
            ON s.id_ujian = ua.id_ujian

        $where

        GROUP BY 
            ua.id_ujian,
            ua.kode_soal,
            ua.nama_soal,
            ua.mapel,
            ua.kelas,
            ua.tampilan_soal,
            ua.status,
            ua.token,
            ua.waktu_ujian,
            ua.tanggal,
            ua.tanggal_selesai

        ORDER BY ua.tanggal ASC

        LIMIT $limit,10";

$result = mysqli_query($db, $query);

if (!$result) {
    die('Error with the query: ' . mysqli_error($db));
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Daftar Tes</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container-fluid">
<p>
    <a class="btn btn-primary" href="menu.php">Menu</a>
</p>

<form method="get" class="mb-3">
    <div class="input-group">

        <input
            type="text"
            name="cari"
            class="form-control"
            placeholder="Cari kode soal atau nama soal..."
            value="<?= htmlspecialchars($cari); ?>"
        >

        <button type="submit" class="btn btn-primary">
            Cari
        </button>

        <?php if ($cari !== '') { ?>
            <a href="daftar_tes.php" class="btn btn-secondary">
                Reset
            </a>
        <?php } ?>

    </div>
</form>
<table class="table table-bordered table-striped table-sm align-middle">
<thead>
    <tr>
        <th>No</th>
        <th>Kode Soal</th>
        <th>Nama Soal</th>
        <th>Mapel</th>
        <th>Kls</th>
        <th>Jml Soal</th>
        <th>Durasi (menit)</th>
        <th>Tgl Ujian</th>
        <th>Tampilan</th>
        <th>Status</th>
        <th>Token</th>
    </tr>
</thead>
                                        <tbody>
                                            <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                                            <tr>
                                            
                                                <td><?php echo '<a href="ubah_tes.php?id='.$row['id_ujian'].'">'.$no++.'</a>'; ?></td>
<td>
    <a href="sinkron_jadwal.php?id=<?= $row['id_ujian']; ?>&hal=<?= $hal; ?>">
        <?= htmlspecialchars($row['kode_soal']); ?>
    </a>
</td>

<td>
    <?= htmlspecialchars($row['nama_soal']); ?>
</td>                                                
                                                <td><?php echo $row['mapel']; ?></td>
                                                <td><?php echo $row['kelas']; ?></td>
                                                <td><?php echo '<a href="view_soal.php?id_ujian='.$row['id_ujian'].'" target="_blank">'.$row['cacah_soal']; ?></a></td>
                                                <td><i class="fa fa-clock" aria-hidden="true"></i>
                                                    <?php echo $row['waktu_ujian']; ?></td>
                                                <td><i class="fa fa-calendar-alt" aria-hidden="true"></i>
                                                    <?php echo date('d M Y H:i', strtotime($row['tanggal'])); ?> s.d. <?php echo date('d M Y H:i', strtotime($row['tanggal_selesai'])); ?></td>
                                                <td><?php echo $row['tampilan_soal']; ?></td>
                                                <td>
                                                    <?php if ($row['status'] == 'Aktif') { ?>
                                                    <span class="badge bg-success">Aktif</span>  <a href="nonaktif.php?id_ujian=<?= $row['id_ujian'];?>" onclick="return confirm('Yakin menonaktifkan tes ini?')">Nonaktifkan</a>
                                                    <?php } else { ?>
                                                    <span class="badge bg-danger">Nonaktif</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $row['token']; ?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
<?php
$parameter_cari = '';

if ($cari !== '') {
    $parameter_cari = '&cari=' . urlencode($cari);
}

if ($hal == 0) {

    echo '<a href="daftar_tes.php?hal=1' . $parameter_cari . '">
            Halaman Selanjutnya
          </a>';

} elseif ($hal == 1) {

    echo '<a href="daftar_tes.php?hal=0' . $parameter_cari . '">
            Halaman Sebelumnya
          </a> ';

    echo '<a href="daftar_tes.php?hal=2' . $parameter_cari . '">
            Halaman Selanjutnya
          </a> ';

} else {

    $prev = $hal - 1;
    $nex  = $hal + 1;

    echo '<a href="daftar_tes.php?hal=' . $prev . $parameter_cari . '">
            Halaman Sebelumnya
          </a> ';

    echo '<a href="daftar_tes.php?hal=' . $nex . $parameter_cari . '">
            Halaman Selanjutnya
          </a> ';
}
?>
    </div>
  
</body>

</html>
