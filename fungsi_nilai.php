<?php
function nilai_otomatis($tipe, $jawaban, $kunci){

    if($tipe == 'pg' || $tipe == 'bs'){
        return trim($jawaban) == trim($kunci) ? 1 : 0;
    }

    if($tipe == 'pg_kompleks'){
        $j = explode(',', strtoupper($jawaban));
        $k = explode(',', strtoupper($kunci));
        sort($j); sort($k);
        return $j == $k ? 1 : 0;
    }

    if($tipe == 'jodohkan'){
        return json_decode($jawaban,true) ==
               json_decode($kunci,true) ? 1 : 0;
    }

    return 0; // uraian dinilai manual
}
