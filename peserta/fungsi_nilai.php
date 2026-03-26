<?php
function hitung_nilai($soal, $jawaban_siswa_raw)
{
    if ($jawaban_siswa_raw === null || $jawaban_siswa_raw === '') {
        return 0;
    }

    $tipe  = strtolower($soal['tipe']);
    $kunci = trim($soal['kunci']);
	$kunci_teks = $kunci;
    /* ===============================
       PG & BENAR / SALAH
    =============================== */
    
    if ($tipe == 'pg') {
        return ($jawaban_siswa_raw === $kunci) ? 1 : 0;
    }
    if ($tipe == 'bs') 
    {
    	 $kunci = [];
	    foreach (explode(',', $kunci_teks) as $x) 
	    {
	        [$k,$v] = explode(':', $x);
	        $kunci[$k] = $v;
	    }
	    $jawaban = json_decode($jawaban_siswa_raw, true);
	    if (!is_array($jawaban)) return 0;
	    $benar = 0;
	    foreach ($kunci as $k => $v) {
        	if (isset($jawaban[$k]) && $jawaban[$k] === $v) {
        	    $benar++;
	        }
    	}
	    return round($benar / count($kunci),2);
	}

    /* ===============================
       PG KOMPLEKS
       Format: A,C,E
    =============================== */
    if ($tipe == 'pg_kompleks') {
	$jawaban_siswa_raw =  str_replace("[", "", $jawaban_siswa_raw);
	$jawaban_siswa_raw =  str_replace("]", "", $jawaban_siswa_raw);
	$jawaban_siswa_raw =  str_replace('"', '', $jawaban_siswa_raw);
        $kunci_arr  = array_map('trim', explode(',', $kunci));
        $jawab_arr  = array_map('trim', explode(',', $jawaban_siswa_raw));

        sort($kunci_arr);
        sort($jawab_arr);

        if ($kunci_arr == $jawab_arr) {
            return 1;
        }

        // nilai parsial
        $benar = count(array_intersect($jawab_arr, $kunci_arr));
        return round(($benar / count($kunci_arr)));
    }

    /* ===============================
       MENJODOHKAN
       jawaban_benar:
       A:X|B:Y|C:Z
       jawaban_siswa (JSON):
       {"A":"X","B":"Y"}
    =============================== */
    if ($tipe == 'menjodohkan') {

        $jawaban_siswa = json_decode($jawaban_siswa_raw, true);
        if (!is_array($jawaban_siswa)) return 0;

        $raw = explode('|', $kunci);
        $kunci_map = [];
        foreach ($raw as $r) {
            [$k, $v] = explode(':', $r, 2);
            $kunci_map[trim($k)] = trim($v);
        }

        $total = count($kunci_map);

        $benar = 0;

        foreach ($jawaban_siswa as $k => $v) {
            if (isset($kunci_map[$k]) && $kunci_map[$k] === $v) {
                $benar++;
            }
        }

        $nilai_jd = $benar / $total;
        $nilai_jd = round($nilai_jd,2);
        //die(' total '.$total.' '.$benar.' '.$nilai_jd);        
        return $nilai_jd;
    }

    /* ===============================
       URAIAN (MANUAL)
    =============================== */
    if ($tipe == 'uraian') {
	return null;
    }

    return 0;
}

