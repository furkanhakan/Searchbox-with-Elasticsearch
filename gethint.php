<?php
require 'vendor/autoload.php';
use Elasticsearch\ClientBuilder;
$client = ClientBuilder::create()->build();

$name = $_REQUEST["q"];
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
for ($i = 0; $i < count($response['hits']['hits']); $i++){
    if ($hint==="")
        $hint = $response['hits']['hits'][$i]['_source']['name'];
    else
        $hint .= ",".$response['hits']['hits'][$i]['_source']['name'];
}
echo $hint === "" ? "no suggestion" : $hint;