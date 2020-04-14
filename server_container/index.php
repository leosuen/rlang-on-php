<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>R 語言分析測試</title>
</head>
<body>
    <form action="analyze.php" method="post" enctype="multipart/form-data">
        <select name="ana_type" id="ana_type">
            <option value="test">test</option>
            <option value="UD_CFA">UD_CFA</option>
            <option value="UD_CFA_c">UD_CFA_c</option>  
        </select>
        <input type="file" name="ana_data" id="ana_data">
        <button type="submit">Send</button>
    </form>
    <img src="/var/www/rlang-php/rscript_warehouse/index.png" alt="R Graph" />
</body>
</html>