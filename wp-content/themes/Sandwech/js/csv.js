var reader = new FileReader();

$("#bottonone_upload").click(function () {
    var input = document.getElementById("file_upload");
    var file = input.files[0];
    reader.onload = function () {
        var extension = input.files[0].name.split('.').pop().toLowerCase();  //file extension from input file
        //console.log(extension);

        var file = reader.result;
        var jsonStudents = ExcelToJson(file);
        //SendData(JSON.stringify(jsonStudents));
    };
    reader.readAsBinaryString(file);
});

/*
La funzione ExcelToJson converte i dati in formato Excel in un oggetto JSON, utilizzando la libreria XLS.
La funzione legge i dati in formato binario utilizzando XLS.read(data, { type: 'binary' }); e assegna il risultato alla variabile cfb.
Viene quindi estratta la "worksheet" dai dati letti e assegnati alla variabile workSheet.
La funzione estrae i riferimenti alla prima e all'ultima cella della worksheet, che vengono assegnati 
alle variabili colFirst, colLast, rowFirst e rowLast.

Viene creato un array vuoto chiamato params che conterrà i nomi delle colonne. Viene quindi calcolato il numero di 
colonne utilizzando una funzione esterna chiamata AlphadecimalToDecimal(colLast) + 1 e viene eseguito un ciclo for 
per ogni colonna. In ogni iterazione, la funzione utilizza un'altra funzione esterna chiamata DecimalToAlphadecimal(i) + "1" 
per ottenere il nome della colonna corrente e lo inserisce nell'array params.

Viene creato un array vuoto chiamato students che conterrà i dati dello studente. Viene quindi eseguito un ciclo for per 
ogni riga, a partire dalla seconda riga (indice 2). In ogni iterazione, viene creato un oggetto vuoto chiamato student, 
poi viene eseguito un altro ciclo for per ogni colonna. In ogni iterazione del secondo ciclo, la funzione utilizza ancora 
una volta la funzione DecimalToAlphadecimal(j) + i.toString() per ottenere il valore della cella corrente, poi assegna questo 
valore all'oggetto student utilizzando il nome della colonna corrispondente (ottenuto dall'array params) come chiave. Alla 
fine dei due cicli, l'oggetto student viene inserito nell'array students.
*/
function ExcelToJson(data) {
    var cfb = XLS.read(data, { type: 'binary' });
    var workSheet = cfb.Sheets.Worksheet;
    //console.log(workSheet);

    var sheetREF = workSheet["!ref"];
    var columns = sheetREF.split(':');

    var colFirst = columns[0].replace(/[0-9]/g, '');
    var colLast = columns[columns.length - 1].replace(/[0-9]/g, '');

    var rowFirst = Number(columns[0].replace(/\D/g, ''));
    var rowLast = Number(columns[columns.length - 1].replace(/\D/g, ''));

    var params = [];
    let numberCols = AlphadecimalToDecimal(colLast) + 1;

    for (let i = 0; i < numberCols; i++) {
        //console.log(DecimalToAlphadecimal(i) + "1");
        let p = workSheet[DecimalToAlphadecimal(i) + "1"]["h"];

        //console.log("index: " + i + " value: " + p + " MAX: " + numberCols);
        params.push(p.toLowerCase().replace(' ', '_'));
    }
    //console.log(params);

    var students = [];

    for (let i = 2; i < rowLast + 1; i++) {
        student = {};
        for (let j = 0; j < numberCols; j++) {
            var casella = DecimalToAlphadecimal(j) + i.toString();
            //console.log("CASELLA: " + casella);

            try { student[params[j]] = workSheet[casella]["h"]; }
            catch (e) { student[params[j]] = ""; }
        }
        //esempio  --> "classe": "1E ITIS - ITIA - INFORMATICA"
        var anno = student["classe"].split(" ")[0].replace(/\D/g, '');
        student["anno"] = anno;

        students.push(student);
        //console.log("index: " + i);
        //console.log(student);
    }
    //console.log(students);

    /*var studentiPrimini = [];
    var studentiAltri = [];

    for (let i = 0; i < students.length; i++) {
        if (students[i]["anno"] == "1") {
            studentiPrimini.push(students[i]);
        }
        else {
            studentiAltri.push(students[i]);
        }
    }

    students = {
        "primini": studentiPrimini,
        "altri": studentiAltri
    }
    */

    //console.log(students);
    //console.log(JSON.stringify(students));
    return students;
}

function SendData(json) {
    $.ajax({
        type: 'POST',
        // make sure you respect the same origin policy with this url:
        // http://en.wikipedia.org/wiki/Same_origin_policy
        url: 'http://localhost/food-api/API/user/importUser.php',
        data: json,
        success: function () {
            //alert("Data sended");
        }
    });
}

function DecimalToAlphadecimal(decimal) {
    /* 
    La funzione converte un numero in base 10 in un numero in base 26 utilizzando un alfabeto di 26 caratteri (A-Z).
    Inizializziamo la base a 26, che rappresenta il numero di caratteri utilizzati nell'alfabeto.
    Creiamo una variabile vuota per memorizzare il risultato finale.
    */
    let base = 26;
    let result = "";
    /*
    Utilizziamo un ciclo while per eseguire la conversione fino a quando decimal non è minore di zero.
    Calcoliamo il resto della divisione tra decimal e base e lo assegnamo alla variabile remainder.
    Utilizziamo la funzione Math.floor per calcolare l'intero più piccolo maggiore o uguale a decimal diviso base e 
    lo assegnamo alla variabile decimal.

    Utilizziamo la funzione NumberToAlphabets per convertire il resto in un carattere dell'alfabeto e lo assegnamo 
    alla variabile remainder.
    Aggiungiamo remainder all'inizio della stringa result.
    */
    if (decimal == 0) {
        return "A"; //A vale 0
    }
    while (decimal > 0) {
        let remainder = decimal % base;
        decimal = Math.floor(decimal / base);
        remainder = NumberToAlphabets(remainder);
        result = remainder + result;
    }
    //Restituiamo il valore di result, che rappresenta il numero in base 26.
    return result;
}

/*
La funzione AlphadecimalToDecimal converte un numero di base 26, noto anche come "alphadecimal", in un numero decimale.
Il numero alphadecimal è rappresentato come una stringa di caratteri, dove ogni carattere è una lettera dell'alfabeto.
La funzione utilizza un ciclo "forEach" per scorrere ogni carattere nella stringa e convertirlo in un numero utilizzando la funzione AlphabetsToNumber.
Viene quindi utilizzata la funzione Math.pow per calcolare la posizione del carattere nella stringa (esponente) e moltiplicato per il valore numerico del carattere.
Il valore totale viene quindi sommato nella variabile "decimal" e restituito come output finale.
*/
function AlphadecimalToDecimal(alphaDecimal) {
    let decimal = 0; // Variabile per contenere il valore decimale finale
    let base = 26; // base del numero alphadecimal
    let digits = alphaDecimal.toString().split(''); // dividiamo la stringa in un array di caratteri
    digits.forEach(function (digit, index) {
        decimal += AlphabetsToNumber(digit) * Math.pow(base, digits.length - 1 - index); //convertiamo il carattere in numero e lo moltiplichiamo per la posizione del carattere nella stringa
    });
    return decimal; //restituiamo il valore decimale
}

function NumberToAlphabets(number) {
    switch (number) {
        case 0: number = "A"; break;
        case 1: number = "B"; break;
        case 2: number = "C"; break;
        case 3: number = "D"; break;
        case 4: number = "E"; break;
        case 5: number = "F"; break;
        case 6: number = "G"; break;
        case 7: number = "H"; break;
        case 8: number = "I"; break;
        case 9: number = "J"; break;
        case 10: number = "K"; break;
        case 11: number = "L"; break;
        case 12: number = "M"; break;
        case 13: number = "N"; break;
        case 14: number = "O"; break;
        case 15: number = "P"; break;
        case 16: number = "Q"; break;
        case 17: number = "R"; break;
        case 18: number = "S"; break;
        case 19: number = "T"; break;
        case 20: number = "U"; break;
        case 21: number = "V"; break;
        case 22: number = "W"; break;
        case 23: number = "X"; break;
        case 24: number = "Y"; break;
        case 25: number = "Z"; break;
    }
    return number;
}

function AlphabetsToNumber(letter) {
    switch (letter) {
        case "A": letter = 0; break;
        case "B": letter = 1; break;
        case "C": letter = 2; break;
        case "D": letter = 3; break;
        case "E": letter = 4; break;
        case "F": letter = 5; break;
        case "G": letter = 6; break;
        case "H": letter = 7; break;
        case "I": letter = 8; break;
        case "J": letter = 9; break;
        case "K": letter = 10; break;
        case "L": letter = 11; break;
        case "M": letter = 12; break;
        case "N": letter = 13; break;
        case "O": letter = 14; break;
        case "P": letter = 15; break;
        case "Q": letter = 16; break;
        case "R": letter = 17; break;
        case "S": letter = 18; break;
        case "T": letter = 19; break;
        case "U": letter = 20; break;
        case "V": letter = 21; break;
        case "W": letter = 22; break;
        case "X": letter = 23; break;
        case "Y": letter = 24; break;
        case "Z": letter = 25; break;
    }
    return letter;
}