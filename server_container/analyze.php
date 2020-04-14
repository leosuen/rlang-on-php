<?php
    #header('Content-Type: text/plain; charset=utf-8');

    $analyzeType = $_POST['ana_type'];
    $analyzeData = $_FILES['ana_data'];
    $tmp_files = $analyzeData['tmp_name'];
    $filesha = ""; # 用以儲存檔案SHA的全域變數
    #move_uploaded_file($tmp_files, "./uploaded_file/". $analyzeData["name"]);
    $filesha = sha1_file($tmp_files);
    if(!file_exists("./analyze_output/" . $filesha)){
        mkdir("./analyze_output/" . $filesha);
    }
    move_uploaded_file($tmp_files, "./analyze_output/". $filesha . "/". $filesha . ".csv");
    $file_name = "./analyze_output/". $filesha . "/". $filesha . ".csv";
    try {
        if (!isset($analyzeData['error']) || is_array($analyzeData['error'])) {
            throw new RuntimeException('Invalid parameters.');
        }
        switch ($analyzeData['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }
        if ($analyzeData['size'] > 1000000) {
            throw new RuntimeException('Exceeded filesize limit.');
        }
        if(!file_exists("./analyze_output/" . $filesha)){
            mkdir("./analyze_output/" . $filesha);
            move_uploaded_file($tmp_files, "./analyze_output/". $filesha . "/". $filesha . ".csv");
        }    
        $file_name = "./analyze_output/". $filesha . "/". $filesha . ".csv";

        rscript($analyzeType, $filesha);
    
    } catch (RuntimeException $e) {
    
        echo $e->getMessage();
    
    }

    function rscript($analyzeType, $filename){
        switch ($analyzeType) {
            case 'UD_CFA_c':
                #var_dump("Rscript ./rscript_warehouse/".$analyzeType.".R ./analyze_output/". $filename . "/" . $filename . ".csv " . $filename);
                exec("Rscript ./rscript_warehouse/".$analyzeType.".R ./analyze_output/". $filename . "/" . $filename . ".csv" , $result);
                # exec("Rscript XXX.R argu1 , $result);
                #var_dump($result);
                $res_json = $result[114]; #In this case, the data which is returned from R at array[114]
                $utf8_res = json_decode($res_json);
                
                # print_r($utf8_res->{'table'});
                # $utf8_res->{'table'} 得知有一維陣列，總共22個元素，在此拆成11-11
                # $utf8_res->{'pngname'} 得知圖片產生路徑以及名稱
                $data = $utf8_res->{'table'};
                $pic = $utf8_res->{'pngname'};
                $displayOutput = "";
                for($i = 0; $i < count($data)/2 ; $i++){
                    
                    if(fmod(number_format(floatval($data[$i+11]),3), 1) !== 0.0){
                        // your code if its decimals has a value
                        $displayOutput .= "<tr><td>" . $data[$i] . "</td><td>" . number_format(floatval($data[$i+11]),3) . "</td></tr>";
                    } else {
                        // your code if the decimals are .0, or is an integer
                        $displayOutput .= "<tr><td>" . $data[$i] . "</td><td>" . number_format(intval($data[$i+11])) . "</td></tr>";
                    }
                }
                #print_r("<table>" . $displayOutput . "</table>");
                $displayOutput = "<table border='1'>" . $displayOutput . "</table>";
                $displayPic = "<img src=./analyze_output/" . $filename . "/" . $pic . " alt='R Graph' />";
                echo $displayOutput . $displayPic;
                break;
            
            default:
                # code...
                break;
        }
    }
?>