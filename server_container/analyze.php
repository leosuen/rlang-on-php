<?php
    //header('Content-Type: text/plain; charset=utf-8');
    $web_header = "<head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'><link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' integrity='sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh' crossorigin='anonymous'><title>R 語言分析測試</title></head>";
    $web_navbar = "<nav class='navbar navbar-expand-lg navbar-light bg-light'><a class='navbar-brand' href='/'>R 語言分析系統</a><button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarNav' aria-controls='navbarNav' aria-expanded='false' aria-label='Toggle navigation'><span class='navbar-toggler-icon'></span></button><div class='collapse navbar-collapse' id='navbarNav'><ul class='navbar-nav'><li class='nav-item active'><a class='nav-link' href='#'>Home <span class='sr-only'>(current)</span></a></li><li class='nav-item'><a class='nav-link' href='./help.html'>幫助</a></li></ul></div></nav>";
    $web_container_head = "<div class='container'>";
    $web_container_end = "</div>";
    $web_footer = "";
    $analyzeType = $_POST['ana_type'];
    $analyzeData = $_FILES['ana_data'];
    $tmp_files = $analyzeData['tmp_name'];
    $filesha = ""; // 用以儲存檔案SHA的全域變數
    //move_uploaded_file($tmp_files, "./uploaded_file/". $analyzeData["name"]);
    $filesha = sha1_file($tmp_files);
    $fileReady = "";
    
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
            mkdir("./analyze_output/" . $filesha, 0777, true);
            move_uploaded_file($tmp_files, "./analyze_output/". $filesha . "/". $filesha . ".csv");
        }    
        //$file_name = "./analyze_output/". $filesha . "/". $filesha . ".csv";

        
        echo $web_header . $web_navbar . $web_container_head;
        rscript($analyzeType, $filesha);
        $time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        echo "本次所花費時間: " . number_format((float)$time, 2, '.', '') . " 秒";
        echo $web_container_end;
    
    } catch (RuntimeException $e) {
    
        echo $e->getMessage();
    
    }

    function getTable($arr){
        strpos($arr,'table');
    }

    function rscript($analyzeType, $filename){
        switch ($analyzeType) {
            case 'UD_CFA_c':
                // var_dump("Rscript ./rscript_warehouse/".$analyzeType.".R ./analyze_output/". $filename . "/" . $filename . ".csv " . $filename);
                exec("Rscript ./rscript_warehouse/".$analyzeType.".R ./analyze_output/". $filename . "/" . $filename . ".csv" , $result);
                // exec("Rscript XXX.R argu1 , $result);
                for($i = 0;$i<count($result);$i++){
                    if(strpos($result[$i], 'table') != ""){
                        $tttt = $result[$i];
                        break;
                    }
                }
                $res_json = $tttt; //In this case, the data which is returned from R at array[114]
                $utf8_res = json_decode($res_json);
                
                // $utf8_res->{'table'} 得知有一維陣列，總共22個元素，在此拆成11-11
                // $utf8_res->{'pngname'} 得知圖片產生路徑以及名稱
                $data = $utf8_res->{'table'};
                $pic = $utf8_res->{'pngname'};
                $displayOutput = "";
                for($i = 0; $i < count($data)/2 ; $i++){
                    
                    if(fmod(number_format(floatval($data[$i+11]),3), 1) !== 0.0 || $data[$i] == "顯著性"){
                        // your code if its decimals has a value
                        $displayOutput .= "<tr><td>" . $data[$i] . "</td><td>" . number_format(floatval($data[$i+11]),3) . "</td></tr>";
                    } else {
                        // your code if the decimals are .0, or is an integer
                        $displayOutput .= "<tr><td>" . $data[$i] . "</td><td>" . number_format(intval($data[$i+11])) . "</td></tr>";
                    }
                }
                //print_r("<table>" . $displayOutput . "</table>");
                $web_head = "<h1>問卷品質分析-建構效度分析結果</h1><p>建構效度分析結果一覽表</p>";
                $displayOutput = "<table border='1'>" . $displayOutput . "</table>";
                $displayOutput = $web_head . $displayOutput . "<p>建構效度指標圖</p>";
                $displayPic = "<img src=./analyze_output/" . $filename . "/" . $pic . " alt='R Graph' width='800' height='800'/><br>";
                $remark = "建構效度指標說明
                <table border='1'>
                    　<tr>
                        <td>指標</td>
                        <td>參考標準</td>
                    　</tr>
                    　<tr>
                        <td>比較性配適指標（Comparative fit index, CFI）</td>
                        <td>大於0.90為佳</td>
                    　</tr>
                    　<tr>
                        <td>非規範配適指標（non-normed fit index, NNFI），又稱TLI（Tucker-Lewis Index）</td>
                        <td>大於0.90為佳</td>
                    　</tr>
                    　<tr>
                        <td>標準化均方根殘差值（standardized root mean square residual, SRMR）</td>
                        <td>小於0.08為佳</td>
                    　</tr>
                    　<tr>
                        <td>近似均方根誤差（root mean square error of approximation, RMSEA）</td>
                        <td>小於0.08為佳</td>
                    　</tr>
                    　<tr>
                        <td>建構信度〈Construct reliability, CR〉</td>
                        <td>大於0.60為佳</td>
                    　</tr>
                    　<tr>
                        <td>平均變異抽取量〈Average Variance Extracted, AVE〉，為聚合效度（Convergent validity）指標</td>
                        <td>大於0.50為佳</td>
                    　</tr>
                </table>
                <p>註，本分析使用統計軟體R之套件：lavaan進行計算，若有疑問請洽校內分機2037校務研究辦公室洽詢</p>";
                echo $displayOutput . $displayPic . $remark;
                break;
            
            case 'Description_con':
                //var_dump("Rscript ./rscript_warehouse/".$analyzeType.".R ./analyze_output/". $filename . "/" . $filename . ".csv " . $filename);
                $rcmd = "Rscript ./rscript_warehouse/".$analyzeType.".R ./analyze_output/". $filename . "/" . $filename . ".csv";
                exec($rcmd, $result);
                // exec("Rscript XXX.R argu1 , $result);
                $res_json = $result[2]; //In this case, the data which is returned from R at array[2]
                $utf8_res = json_decode($res_json, true); //The second parameter 'true' can make first parameter return array class
                //題目代號
                $id = $utf8_res['題目代號'];
                //變數名稱
                $varname = $utf8_res['變數名稱'];
                //樣本數
                $sample_num = $utf8_res['樣本數'];
                //平均數
                $average = $utf8_res['平均數'];
                //標準差
                $std = $utf8_res['標準差'];
                //最小值
                $min = $utf8_res['最小值'];
                //最大值
                $max = $utf8_res['最大值'];
                //偏態
                $Skewness = $utf8_res['偏態'];
                //峰度
                $Kurtosis = $utf8_res['峰度'];
                //估計標準誤
                $sem = $utf8_res['估計標準誤'];
                //圖片位置
                $pic = $utf8_res['pngname'][0];
                $displayOutput = "";
                for($i = 0; $i < count($varname) ; $i++){
                    if($i == 0){
                        $displayOutput = "<thead class='thead-light'><tr><th scope='col'>題目代號</th><th scope='col'>變數名稱</th><th scope='col'>樣本數</th><th scope='col'>平均數</th><th scope='col'>標準差</th><th scope='col'>最小值</th><th scope='col'>最大值</th><th scope='col'>偏態</th><th scope='col'>峰度</th><th scope='col'>估計標準誤</th></tr><thead>";
                    }
                    $displayOutput .= "<tr><td>". $id[$i] . "</td><td>" . $varname[$i] . "</td><td>" . $sample_num[$i] . "</td><td>" . number_format($average[$i],3) . "</td><td>" . number_format($std[$i],3) . "</td><td>" . number_format($min[$i],3) . "</td><td>" . number_format($max[$i],3) . "</td><td>" . number_format($Skewness[$i],3) . "</td><td>" . number_format($Kurtosis[$i],3) . "</td><td>" . number_format($sem[$i],3) ."</td></tr>";
                }
                //print_r("<table>" . $displayOutput . "</table>");
                $web_head = "<h1>描述性統計結果-連續變項</h1><p>描述性統計表</p>";
                $displayOutput = "<table class='table table-bordered'>" . $displayOutput . "</table>";
                $displayOutput = $web_head . $displayOutput . "<p>不同變項之平均數長條圖</p>";
                $remark = "<p>註，圖表呈現各變項之平均數與上下兩倍估計標準誤之範圍</p>
                指標說明
                <table class='table table-bordered'>
                    　<tr>
                        <td>指標</td>
                        <td>說明/參考標準</td>
                    　</tr>
                    　<tr>
                        <td>樣本數</td>
                        <td>該變項納入計算之樣本數</td>
                    　</tr>
                    　<tr>
                        <td>平均數</td>
                        <td>該變項之算術平均數</td>
                    　</tr>
                    　<tr>
                        <td>標準差</td>
                        <td>該變項之標準差(分散程度)</td>
                    　</tr>
                    　<tr>
                        <td>最小值</td>
                        <td>該變項作答反應中最小的數值</td>
                    　</tr>
                    　<tr>
                        <td>最大值</td>
                        <td>該變項作答反應中最大的數值</td>
                    　</tr>
                    　<tr>
                        <td>偏態</td>
                        <td>該變項樣本偏離情況，若大於0則表示正偏(樣本多回答較低的分數)，若小於0則表示負偏(樣本多回答較高的分數)</td>
                    　</tr>
                    　<tr>
                        <td>峰度</td>
                        <td>該變項樣本集中情況，若大於3則表示高狹峰(樣本多聚集於平均數附近)，若小於0則表示低闊峰(樣本多平均分布於各分數)</td>
                    　</tr>
                    　<tr>
                        <td>估計標準誤</td>
                        <td>該變項平均數之估計誤差，與樣本數呈負相關，與標準差成正相關</td>
                    　</tr>
                </table>
                <p>註，本分析使用統計軟體R之套件：psych進行計算，若有疑問請洽校內分機2037校務研究辦公室洽詢</p>";
                $displayPic = "<img src=./analyze_output/" . $filename . "/" . $pic . " class='rounded mx-auto d-block' alt='R Graph' width='800' height='800' />";
                echo $displayOutput . $displayPic . $remark;
                break;
                
            default:
                echo "<div>參數錯誤</div>";
                break;
        }
    }
?>