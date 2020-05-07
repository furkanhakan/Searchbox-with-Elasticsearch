<?php
require 'vendor/autoload.php';
use Elasticsearch\ClientBuilder;
$client = ClientBuilder::create()->build();

$name = $_POST['search'];
$hint = "";


$searchdata = [
    'index' => 'name_index',
    'body'  => [
        'query' => [
            'prefix' => [
                'name' => $name
            ]
        ]
    ]
];

$response = $client->search($searchdata);
$output='';
for ($i = 0; $i < count($response['hits']['hits']); $i++){
    $output .= "<a class=\"dropdown-item\">".$response['hits']['hits'][$i]['_source']['name']."</a>";
}
echo $output === "" ? "no suggestion" : $output;