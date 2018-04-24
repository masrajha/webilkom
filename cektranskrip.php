<?php
function loadXMLToArray($path)
{
    $xmlfile = file_get_contents($path);
    $xml = simplexml_load_string($xmlfile);
    $json = json_encode($xml);
    $xmlArr = json_decode($json, true);
    return $xmlArr;
}

if (isset($_GET["transrkrip"])) {
    $report_data = [];

    class MataKuliah
    {
        private $kode;
        private $arr_kode = array();
        public function __construct()
        {
            $this->kode = "";
            $argv = func_get_args();
            switch (func_num_args()) {
                case 1:
                    self::__construct1($argv[0]);
                    break;
            }
        }
        public function __construct1($kode)
        {
            $this->kode = $kode;
        }
        public function isKode($var)
        {
            $object = (object) $var;
            // print_r($object->KODE);
            // echo $this->kode;
            return ($object->KODE == $this->kode);
        }
        public function isFail($var)
        {
            $hm = $var['HM'];
            //print_r($object);
            return (in_array(trim($hm), ['E', '-', '']));
        }
    }
    function isMKWajib($var)
    {
        $object = (object) $var;
        return ($object->SIFAT == "WAJIB");
    }
    function getKodeMKKur($array_kur)
    {
        $kode = [];
        foreach ($array_kur as $key => $val) {
            $mk = (object) $array_kur[$key];
            //print_r($mk);
            array_push($kode, $mk->KODE);
        }
        return $kode;
    }
    function getMKKur($kur, $kode)
    {
        $data = array_filter($kur, array(new MataKuliah($kode), 'isKode'));
        $mk = current($data);
        return $mk;
    }
    function getListMK($kur, $array_kode)
    {
        $mk_list = [];
        // print_r($array_kode);

        foreach ($array_kode as $key => $kode) {
            $cur_mk = [];
            $mk = getMKKur($kur, $kode);
            $key = array_keys($mk);
            for ($i = 0; $i < count($key) - 1; $i++) {
                $cur_mk[$key[$i]] = $mk[$key[$i]];
            }
            array_push($mk_list, $cur_mk);
        }
        return ($mk_list);
    }
    function getMKTidakLulus($array_transkrip)
    {
        $data = array_filter($array_transkrip, array(new MataKuliah(), 'isFail'));
        return $data;
    }
    function getMKWajib($array)
    {
        $data = array_filter($array, 'isMKWajib');
        return $data;
    }
    function getMHSFromTranskrip($array_transkrip)
    {
        $searchword = "Nama";
        foreach ($array_transkrip as $k => $v) {
            $mhs = [];
            if (preg_match("/\b$searchword\b/i", $v)) {
                for ($i = $k; $i < $k + 4; $i++) {
                    $data = explode("\t", $array_transkrip[$i]);
                    $mhs[$data[0]] = $data[1];
                    $mhs[$data[2]] = $data[3];
                }
                return $mhs;
            }
        }
        return $mhs;
    }
    function getKodeMK($array)
    {
        $data = [];
        foreach ($array as $key => $val) {
            array_push($data, $val["KODE"]);
        }
        return $data;
    }
    function getMKFromTranskrip($array_transkrip, $col = null)
    {
        $cari = 'KODE	MATA KULIAH	KREDIT	HM	JUMLAH PENGAMBILAN	SMT PENGAMBILAN TERAKHIR';
        $key = array_search($cari, $array_transkrip);
        $kode = [];
        $mk = [];
        $kredit = [];
        $hm = [];
        $all = [];
        if ($key >= 0) {
            $key++;
            if (!array_key_exists($key, $array_transkrip)) {
                return [];
            }

            if (count(explode("\t", $array_transkrip[$key])) < 4) {
                return [];
            }
            do {
                $row_all = array();
                $data = explode("\t", $array_transkrip[$key]);
                // echo $data[0] . "<br>";
                array_push($kode, $data[0]);
                array_push($mk, $data[1]);
                array_push($kredit, $data[2]);
                array_push($hm, $data[3]);
                $row_all["KODE"] = $data[0];
                $row_all["MK"] = $data[1];
                $row_all["KREDIT"] = $data[2];
                $row_all["HM"] = $data[3];
                array_push($all, $row_all);
            } while (count(explode("\t", $array_transkrip[++$key])) == 6);

        }
        if ($col == 'kode') {
            return $kode;
        } else if ($col == 'mk') {
            return $mk;
        } else if ($col == 'kredit') {
            return $kredit;
        } else if ($col == 'hm') {
            return $hm;
        } else {
            return $all;
        }

    }
    function getSKSLulusTranskrip($array_transkrip)
    {
        $sks = 0;
        foreach ($array_transkrip as $key => $val) {
            if (!in_array($val["HM"], ['E', '-', ''])) {
                $sks += $val["KREDIT"];
            }

        }
        return $sks;
    }
    function validateTranskrip($teks_transkrip)
    {
        // $data_transkrip = preg_split("/[\r\n]+/", $_GET["transrkrip"]);
        $path_file = "/var/www/web-ilkom/data";
        // echo count($kur_ilkom_2005)." ".count($kur_ilkom_2012)." ".count($kur_mi_2012);
        $kur_ilkom_2012_agama = ["UNI612101", "UNI612102", "UNI612103", "UNI612104", "UNI612105"];
        $kur_ilkom_2005_agama = ["MPK101", "MPK102", "MPK103", "MPK104", "MPK105"];
        $kur_mi_2012_agama = ["UNI512101", "UNI512102", "UNI512103", "UNI612104", "UNI512105"];

        global $report_data;
        //  echo count($kur_ilkom_2005)." ".count($kur_ilkom_2012)." ".count($kur_mi_2012)." ".count($kur_mi_2012_agama);
        $data_transkrip = preg_split("/[\r\n]+/", $teks_transkrip);
        $mhs = getMHSFromTranskrip($data_transkrip);
        $report_data["Mahasiswa"] = $mhs;
        $kurikulum = [];
        $mk_agama = [];
        $min_sks = 0;
        $field = array(0 => "Nama", 1 => "NPM", 2 => "Fakultas", 3 => "Jurusan", 4 => "Prog Studi", 5 => "Dosen Pemb. Akademik", 6 => "Jumlah SKS", 7 => "IPK");
        if (count(array_diff(array_keys($mhs), $field)) != 0) {
            return 1; //Format teks transkrip tidak sesuai
        }
        $mktranskrip = getMKFromTranskrip($data_transkrip);
        if (count($mktranskrip) == 0) {
            return 1;
        }
        if (!($mhs['Prog Studi'] == 'D3 Manajemen Informatika' || $mhs['Prog Studi'] == 'Ilmu Komputer')) {
            return 2; //Program studi belum terdaftar
        }
        if ($mhs['Prog Studi'] == 'D3 Manajemen Informatika') {
            if (substr($mhs['NPM'], 0, 2) < 16) {
                $kur_mi_2012 = loadXMLToArray($path_file . "/kur_mi_2012.xml")["MATAKULIAH"];
                $kurikulum = $kur_mi_2012;
                $mk_agama = $kur_mi_2012_agama;
                $min_sks = 115;
            } else {
                return 3; //Tidak ada data kurikulum yang sesuai dengan mahasiswa
            }

        } else if ($mhs['Prog Studi'] == 'Ilmu Komputer') {
            if (substr($mhs['NPM'], 0, 2) < 12) {
                $kur_ilkom_2005 = loadXMLToArray($path_file . "/kur_ilkom_2005.xml")["MATAKULIAH"];
                $kurikulum = $kur_ilkom_2005;
                $mk_agama = $kur_ilkom_2005_agama;
                $min_sks = 144;
            } else if (substr($mhs['NPM'], 0, 2) < 16) {
                $kur_ilkom_2012 = loadXMLToArray($path_file . "/kur_ilkom_2012.xml")["MATAKULIAH"];
                $kurikulum = $kur_ilkom_2012;
                $mk_agama = $kur_ilkom_2012_agama;
                $min_sks = 144;
            } else {
                return 3; //Kurikulum tidak didefinisikan
            }

        } else {
            return 2; //Porgram Studi blm terdaftar
        }
        //print_r($kurikulum);
		$error_code = [];
        if ($mhs["Jumlah SKS"] < $min_sks) {
            array_push($error_code, 4); //SKS Kurang dari minimal
        }
        $mkTidakLulus = getMKTidakLulus($mktranskrip);
        $MKWajib = getMKWajib($kurikulum);
        $MKWajibTidakLulus = array_intersect(getKodeMK($mkTidakLulus), getKodeMK($MKWajib));
        //print_r(getKodeMK($mktranskrip));
        $listMKWajibTidakLulus = getListMK($kurikulum, $MKWajibTidakLulus);
        $report_data["MK Wajib Belum Lulus"] = $listMKWajibTidakLulus;
        $MKLulus = array_diff(getKodeMK($mktranskrip), getKodeMK($mkTidakLulus));
        // print_r($MKLulus);
        // print_r(array_diff($mk_agama,getKodeMK($mktranskrip) ));
        if (count($MKWajibTidakLulus) > 0) {
            array_push($error_code, 5); //Ada MK Wajib yang belum lulus
        }
        $report_data["SKS LULUS"] = getSKSLulusTranskrip($mktranskrip);
        if (getSKSLulusTranskrip($mktranskrip) < $min_sks) {
            array_push($error_code, 6); //Total SKS Lulus kurang dari MIN SKS
        }

        $KodeMKTranskrip = getKodeMK($mktranskrip);
        $KodeMKWajib = getKodeMK($MKWajib);
        // print_r($KodeMKTranskrip);
        // echo "<hr>";
        // print_r($KodeMKWajib);
        $cekMKWajib = (array_diff($KodeMKWajib, $KodeMKTranskrip));

        $cekMKWajibBelumDiambil = array_diff($cekMKWajib, $mk_agama);
        //print_r($cekMKWajibBelumDiambil);
        $listCekMKWajibBelumDiambil = getListMK($kurikulum, $cekMKWajibBelumDiambil);
        $report_data["MK WAJIB BELUM DIAMBIL"] = $listCekMKWajibBelumDiambil;
        if (count($cekMKWajibBelumDiambil) > 0) {
            array_push($error_code, 7); //Ada MK Wajib belum diambil
        }
        if (count(array_diff($mk_agama, getKodeMK($mktranskrip))) > 4) {
            array_push($error_code, 8); //Belum mengambil MK Agama
        }
        if (count($error_code) > 0) {
            return $error_code;
        }

        return 0;
    }
    function makeList($array)
    {

        //Base case: an empty array produces no list
        if (empty($array)) {
            return ' : <b>Tidak Ada</b>';
        }

        //Recursive Step: make a list with child lists
        $output = '<ul>';
        if (is_array($array)) {
            foreach ($array as $key => $subArray) {
                if (is_numeric($key)) {
                    $key++;
                }

                $output .= '<li>' . $key . makeList($subArray) . '</li>';
            }
        } else {
            $output .= '<b>' . $array . '</b>';
        }
        $output .= '</ul>';

        return $output;
    }
     function printValidasi($transkrip)
    {
        global $report_data;
        $validate = validateTranskrip($transkrip);
        if (is_int($validate)) {
            switch ($validate) {
                case 0:
                    echo '[box type="info" align="aligncenter" class="" width=""]TRANSKRIP NILAI OK, TIDAK ADA MASALAH[/box]';
                    break;
                case 1:
                    echo '[box type="error" align="aligncenter" class="" width=""]Format teks transkrip tidak sesuai[/box]';
                    break;
                case 2:
                    echo '[box type="error" align="aligncenter" class="" width=""]Program Studi Belum Terdaftar[/box]';
                    break;
                case 3:
                    echo '[box type="error" align="aligncenter" class="" width=""]Tidak ada data kurikulum yang sesuai dengan mahasiswa[/box]';
                    break;
            }
        } else if (is_array($validate)) {
            $errormsg = array(
                4 => "SKS Kurang dari minimal",
                5 => "Ada MK Wajib yang belum lulus",
                6 => "Total SKS Lulus kurang dari MIN SKS",
                7 => "Ada MK Wajib belum diambil",
                8 => "Belum mengambil MK Agama",
            );
            $msg = '[box type="warning" align="aligncenter" class="" width=""]<ul>';
            foreach ($validate as $key => $val) {
                $msg .= '<li>' . $errormsg[$val] . '</li>';
            }
            $msg .= '</ul>[/box]';
            echo $msg;
        }

        echo '[toggle title="Klik untuk Melihat Informasi Rinci Transkrip Anda" state="close"]';
        echo makeList($report_data);
        echo '[/toggle]';
    }
    printValidasi($_GET["transrkrip"]);

}
?>
<h3>Berikut Langkah-langkah untuk memeriksa transkrip Nilai</h3>
<ul>
<li>Buka Halaman Transkrip di SIAKAD</li>
<li>Select All dengan cara tekan CTRL+A</li>
<li>Copy</li>
<li>Paste di textarea Input Text Transkrip</li>
<li>Klik Submit</li>
</ul>
<a href="http://ilkom.unila.ac.id/wp-content/uploads/2018/04/transkrip.png"><img src="http://ilkom.unila.ac.id/wp-content/uploads/2018/04/transkrip.png" alt="" width="40%" height="40%" class="alignnone size-full wp-image-3247" /></a>
<form method="get">
<input type="hidden" name="page_id" value="<?php echo $_GET["page_id"]; ?>">
<label> Input Text Transkrip </label>
<textarea rows="15" cols="100" name="transrkrip"></textarea>
<input type="submit" value="Submit" name="submit">
</form>