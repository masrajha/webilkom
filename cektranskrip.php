<?php
if (isset($_GET["transrkrip"])) {
    $kur_ilkom_12_wajib = ["UNI612101", "UNI612102", "UNI612104", "COM612101", "UNI612105", "UNI612108", "UNI612109", "COM612102", "MIP612101", "UNI612111", "UNI612110", "UNI612103", "MPK612107", "MAT612219", "COM612111", "COM612112", "COM612113", "COM612114", "UNI612106", "COM612115", "MAT612233", "MAT612104", "KOM612203", "KOM612205", "KOM612206", "COM612208", "COM612209", "KOM612202", "KOM612204", "KOM612201", "KOM612207", "COM612225", "COM612227", "COM612224", "COM612226", "COM612221", "COM612229", "COM612228", "COM612301", "COM612310", "COM612306", "UNI612206", "COM612305", "UNI512206", "COM612303", "COM612324", "COM612320", "COM612325", "COM612321", "MIP612303", "UNI612402", "MIP612103", "COM612401", "UNI612408", "MIP612301", "COM612404", "COM612497", "COM612499", "COM612498"];
    $kur_ilkom_05_wajib = ["MPK106", "MPK101", "MPK103", "MPK104", "MPK105", "COM101", "COM111", "MPK102", "KBI101", "MBB101", "MAT110", "FIS105", "KBS101", "COM100", "COM110", "COM120", "MAT222", "MAT221", "KJR116", "MAT111", "MAT130", "MPK107", "MAT109", "COM230", "COM221", "COM231", "COM212", "COM240", "COM202", "MAT230", "COM233", "COM223", "COM280", "COM241", "COM281", "COM232", "MJN160", "COM342", "COM334", "COM314", "MAT350", "MAT203", "COM351", "COM324", "COM493", "KOM101", "COM350", "COM337", "COM452", "COM403", "COM494", "COM425", "COM426", "MJN270", "COM495", "COM499", "UNI500", "UNI400", "COM497", "COM498"];
    $kur_d3mi_12_wajib = ["UNI512109", "UNI512107", "UNI512106", "UNI512105", "UNI512103", "UNI512102", "UNI512101", "MIN512103", "MIN512101", "MIN512102", "UNI612104", "UNI512110", "MIN512106", "MIN512104", "MIN512105", "MIN512108", "MIN512109", "MIN512110", "UNI512121", "MIN512205", "MIN512206", "MIN512207", "MIN512201", "MIN512202", "MIN512204", "MIN512211", "MIN512212", "MIN512213", "MIN512209", "UNI512206", "MIN512302", "MIN512303", "MIP512303", "MIN512301", "MIN512306", "MIN512305", "MIN512308",
    ];

    function getMahasiswa($array_traskrip)
    {
        $searchword = "Nama";
        foreach ($array_traskrip as $k => $v) {
            $mhs = [];
            if (preg_match("/\b$searchword\b/i", $v)) {
                for ($i = $k; $i < $k + 4; $i++) {
                    $data = explode("\t", $array_traskrip[$i]);
                    $mhs[$data[0]] = $data[1];
                    $mhs[$data[2]] = $data[3];
                }
                return $mhs;
            }
        }
        return $mhs;
    }
    function getMatakuliah($array_traskrip,$col=null)
    {
        $cari = 'KODE	MATA KULIAH	KREDIT	HM	JUMLAH PENGAMBILAN	SMT PENGAMBILAN TERAKHIR';
        $key = array_search($cari, $array_traskrip);
        $kode=[];
        $mk=[];
        $kredit=[];
        $hm=[];
        $all=[];
        if ($key >= 0) {
            $key++;
            do {
                $data = explode("\t", $array_traskrip[$key]);
                // echo $data[0] . "<br>";
                array_push($kode,$data[0]);
                array_push($mk,$data[1]);
                array_push($kredit,$data[2]);
                array_push($hm,$data[3]);

            } while (count(explode("\t", $array_traskrip[++$key])) == 6);
            $all = ["kode"=>$kode,"mk"=>$mk,"kredit"=>$kredit,"hm"=>$hm];
        }
        if ($col=='kode') return $kode;
        else if($col=='mk') return $mk;
        else if($col=='kredit') return $kredit;
        else if($col=='hm') return $hm;
        else return $all;
    }
//print_r($kur_ilkom_12);
    $data_transkrip = preg_split("/[\r\n]+/", $_GET["transrkrip"]);
    $mhs = (object) getMahasiswa($data_transkrip);
//   print_r($mhs);
    echo $mhs->{'Dosen Pemb. Akademik'};

    print_r(getMatakuliah($data_transkrip,'kode'));
    //echo strlen($cari)."-".strlen($data_transkrip[22]);
    //$str=$data_transkrip[22];
    //echo strlen($str);
    //foreach($data_transkrip as $key=>$val)
    //echo $key." - ".$val."<br>";
}
?>
<form method="get">
<input type="hidden" name="page_id" value="<?php echo $_GET["page_id"]; ?>">
<label> Input Text Transkrip </label>
<textarea rows="15" cols="40" name="transrkrip"><?php echo isset($_GET["transrkrip"]) ? $_GET["transrkrip"] : ""; ?></textarea>
<input type="submit" value="Submit" name="submit">
</form>