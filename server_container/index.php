<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>R 語言分析測試</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/">
            R 語言分析系統
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./help.html">幫助</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <form action="analyze.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="type">分析類型</label>
                <select name="ana_type" id="ana_type" class="custom-select">
                    <option value="Description_con">描述性統計分析-連續型資料</option>
                    <option value="disabled" disabled>問卷品質分析-建構效度</option>
                </select>
            </div>
            <div class="form-group">
                <label for="notification">目前只支援CSV文件檔</label>
                <div class="custom-file">
                    <input type="file" class="form-control-file custom-file-input" name="ana_data" id="ana_data">
                    <label class="custom-file-label" id="file_name" for="customFile">Choose file</label>
                </div>
                <button class="btn btn-outline-secondary" type="submit" id="inputGroupFileAddon04">Send</button>
                
            </div>
            
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="./index.js"></script>
</body>
</html>