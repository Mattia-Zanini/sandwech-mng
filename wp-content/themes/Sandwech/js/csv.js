var reader = new FileReader();

$("#bottonone_upload").click(function () {
    var input = document.getElementById("file_upload");
    var file = input.files[0];
    reader.onload = function () {
        var file = reader.result.replaceAll("\r", "").replaceAll("\"", "");
        var json = CsvToJson(file);
        //console.log(json);
    };
    reader.readAsText(file);
});

function CsvToJson(csv) {
    //console.log(csv);
    var params = csv.split('\n')[0].split(',');

    var file = csv.split('\n');
    file.shift();
    file.pop();

    var output = [];
    for (let i = 0; i < file.length; i++) {
        elements = file[i].split(',');
        var jsonSingleLine = {};
        //console.log(elements);

        for (let j = 0; j < params.length; j++) {
            jsonSingleLine[params[j]] = elements[j];
        }
        output.push(jsonSingleLine);
    }
    //console.log(output);
    return output;
}

function SendData(json) {
    $.ajax({
        type: 'POST',
        // make sure you respect the same origin policy with this url:
        // http://en.wikipedia.org/wiki/Same_origin_policy
        url: 'http://localhost/food-api/API/user/importUser.php',
        data: json,
        success: function (msg) {
            alert("Data sended");
        }
    });
}