
var reader = new FileReader();


/*Questa funxione prende in input il file uploaddato e ne scrive il contenuto in console*/ 
$("#bottonone").click(function () {
    
    var data = document.getElementById('file_upload').files;
    //var dati= data.text();
    readText(data);
    //console.log(dati);
})

	function readText(files){

		if(files && files[0]){
			reader.onload = function (e) {  
				var output=e.target.result;

				//process text to show only lines with "@":				
				//output=output.split("\n").filter(/./.test, /\@/).join("\n");
                console.log(JSON.stringify(output));
				//document.getElementById('main').innerHTML= output;
			};//end onload()
			reader.readAsText(files[0]);
		}//end if html5 filelist support
	} 


    // Method to upload a valid csv file
    function upload() {
        var files = document.getElementById('file_upload').files;
        if(files.length==0){
          alert("Please choose any file...");
          return;
        }
        var filename = files[0].name;
        var extension = filename.substring(filename.lastIndexOf(".")).toUpperCase();
        if (extension == '.CSV') {
            //Here calling another method to read CSV file into json
            csvFileToJSON(files[0]);
        }else{
            alert("Please select a valid csv file.");
        }
      }
       
      //Method to read csv file and convert it into JSON 
      function csvFileToJSON(file){
          try {
            var reader = new FileReader();
            reader.readAsBinaryString(file);
            reader.onload = function(e) {
                var jsonData = [];
                var headers = [];
                var rows = e.target.result.split("\r\n");               
                for (var i = 0; i < rows.length; i++) {
                    var cells = rows[i].split(",");
                    var rowData = {};
                    for(var j=0;j<cells.length;j++){
                        if(i==0){
                            var headerName = cells[j].trim();
                            headers.push(headerName);
                        }else{
                            var key = headers[j];
                            if(key){
                                rowData[key] = cells[j].trim();
                            }
                        }
                    }
                    //skip the first row (header) data
                    if(i!=0){
                        jsonData.push(rowData);
                    }
                }
                  
                //displaying the json result in string format
                //document.getElementById("display_csv_data").innerHTML=JSON.stringify(jsonData);
                console.log(JSON.stringify(jsonData));
                }
            }catch(e){
                console.error(e);
            }
      }

