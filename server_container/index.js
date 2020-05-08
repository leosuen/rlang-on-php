$("#ana_data").change(function(){
    $("#file_name").text($("#ana_data").val().replace(/C:\\fakepath\\/i, ''));
})