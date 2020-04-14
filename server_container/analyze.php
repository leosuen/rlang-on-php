<?php
    $analyzeType = $_POST['ana_type'];
    $analyzeData = $_FILES['ana_data'];
    $filesha = ""; # 用以儲存檔案SHA的全域變數
    $filesha = sha1_file($analyzeData);
    mkdir("./analyze_output/" . $filesha);
    rename("./uploaded_file/" . $analyzeData["name"] , "./analyze_output/" . $filesha . "/" . $filesha . ".csv");
    try {
   
        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (
            !isset($analyzeData['error']) ||
            is_array($analyzeData['error'])
        ) {
            throw new RuntimeException('Invalid parameters.');
        }
    
        // Check $analyzeData['error'] value.
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
    
        // You should also check filesize here.
        if ($analyzeData['size'] > 1000000) {
            throw new RuntimeException('Exceeded filesize limit.');
        }
    
        // DO NOT TRUST $analyzeData['mime'] VALUE !!
        // Check MIME Type by yourself.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            $finfo->file($analyzeData['tmp_name']),
            array(
                'csv' => 'text/csv',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ),
            true
        )) {
            throw new RuntimeException('Invalid file format.');
        }
    
        // You should name it uniquely.
        // DO NOT USE $analyzeData['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
        if (!move_uploaded_file(
            $analyzeData['tmp_name'],
            sprintf('./uploads/%s.%s',
                sha1_file($analyzeData['tmp_name']),
                $ext
            )
        )) {
            throw new RuntimeException('Failed to move uploaded file.');
        }
    
        echo 'File is uploaded successfully.';

        rscript($analyzeType, sha1_file($analyzeData['tmp_name']));
    
    } catch (RuntimeException $e) {
    
        echo $e->getMessage();
    
    }

    function rscript($analyzeType, $analyzeData){
        switch ($analyzeType) {
            case 'UD_CFA_c':
                exec("Rscript ./rscript_warehouse/".$analyzeType.".R ./uploaded_file/CFA_test.csv", $result);
                $res_json = $result[114]; #In this case, the data which is returned from R at array[114]
                $utf8_res = json_decode($res_json);
                
                # print_r($utf8_res->{'table'});
                # $utf8_res->{'table'} 得知有一維陣列，總共22個元素，在此拆成11-11
                $data = $utf8_res->{'table'};
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
                echo $displayOutput;
                break;
            
            default:
                # code...
                break;
        }
    }
?>