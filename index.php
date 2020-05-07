<?php

require 'vendor/autoload.php';

use Elasticsearch\ClientBuilder;

$client = ClientBuilder::create()->build();

// Create the index
$params = [
    'index' => 'name_index'
];

try {
    $client->indices()->get($params);
}catch (Exception $e){
    $client->indices()->create($params);
}

// Create the mapping
$mapping = [
    'index' => 'name_index',
    'body' => [
        'properties' => [
            'id' => [
                'type' => 'integer'
            ],
            'name' => [
                'type' => 'text',
            ],
            'gender' => [
                'type' => 'text'
            ]
        ]
    ]
];

$client->indices()->putMapping($mapping);


$servername = "localhost";
$dbname = "elasticsearchtest";
$username = "root";
$password = "12345";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}

// Index an datas
$query = $conn->query("SELECT * FROM names", PDO::FETCH_ASSOC);
if ( $query->rowCount() ){
    $data = ['body' => []];
    $i = 0;
    foreach( $query as $row ){
        $data['body'][] = [
            'index' => [
                '_index' => 'name_index',
                '_id'    => $i
            ]
        ];

        $data['body'][] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'gender' => $row['gender']
        ];
        $i++;
    }
    $responses = $client->bulk($data);
    unset($responses);
}



?>

<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<script src="https://twitter.github.io/typeahead.js/releases/latest/typeahead.bundle.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="form-group">
                <label class="form-controle"> Search</label>
                <input type="text" class="form-control" id="search" placeholder="Search..." onkeyup="getname(this.value);" name="typeahead">
            </div>

            <p id="txtHint""></p>
        </div>

    </body>
</html>

<script>
    function getname(str) {
        if (str.length == 0) {
            document.getElementById("txtHint").innerHTML = "";
            return;
        } else {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtHint").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "gethint.php?q=" + str, true);
            xmlhttp.send();
        }
    }
</script>