<?php

// https://fdc.nal.usda.gov/api-guide.html

include_once "servers.php";

require_once __DIR__ . '/../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

include_once __DIR__ . "/../servers.php";
include_once __DIR__ . "/food_ids.php";

$food_get = "https://api.nal.usda.gov/fdc/v1/food/";
$foods_get = "https://api.nal.usda.gov/fdc/v1/foods/";
$apple_no = "171515";
$api_query = "?api_key=" . API_KEY_FDC;

// $query = $food_get . $apple_no . $api_query /*. "&format=abridged"*/;
$data = array();
foreach (array_chunk($food_ids, count($food_ids) / 5, true) as $chunk) {
    $ids = "&fdcIds=";
    foreach ($chunk as $id => $_) {
        echo "$id\n";
        $ids .= "$id%2C";
    }
    $ids = substr($ids, 0, strlen($ids) - 3);
    // $nut_ids = "";
    // foreach ($nutrient_ids as $nut_id => $_) {
    //     $nut_ids .= "&nutrients=$nut_id";
    // }

    $query = $foods_get . $api_query . $ids  /* . $nut_ids */;

    echo $query . "\n";

    echo "[get_data] getting data...\n";
    ($received = json_decode(file_get_contents($query), true)) or die("Failed to get data\n");
    $data = array_merge($data, $received);
    echo "[get_data] received some data\n";

    // file_put_contents("data.json", json_encode($data, JSON_PRETTY_PRINT));

    // $calories_name = "Energy";
    // $fat_name = "Total lipid (fat)";
    // $protein_name = "Protein";
    // $carbs_name = "Carbohydrate, by difference";
    // $fiber_name = "Fiber, total dietary";
    // $sugar_name = "Sugars, total including NLEA";

    $foods = [];
    foreach ($data as $food) {
        echo "{$food['fdcId']}\n";
        $food_nutrients = $food['foodNutrients'];
        $nuts = array();
        foreach ($food_nutrients as $nutrient) {
            if (array_key_exists($nutrient['nutrient']['id'], $nutrient_ids)) {
                $nut = new Nutrient(
                    $nutrient['nutrient']['name'],
                    $nutrient['amount'],
                    $nutrient['nutrient']['unitName'],
                );
                $nuts[] = $nut;
            }
        }

        $foods[] = new Food($food_ids[$food["fdcId"]], $nuts);
    }

    // echo json_encode($foods, JSON_PRETTY_PRINT) . "\n";
    $data_publish_connection = new AMQPStreamConnection(rabbit_server, 5672, back_server_creds[0], back_server_creds[1]);
    $data_publish_channel = $data_publish_connection->channel();
    $message = new AMQPMessage(json_encode($foods, JSON_PRETTY_PRINT));
    echo "[get_data] sending new data to database...\n";
    $data_publish_channel->basic_publish($message, '', BACK_DATA);
    echo "[get_data] sent\n";

    echo "[get_data] sleeping 1 second\n";
    sleep(1);
}

// json structure:
/* 
foodNutrients = array[
    {
        nutrient {
            name
            unitName
        }
        amount
    }
]
*/

// Food, Serving_Size, Calories, Fat, Protein, Carbs, Fiber, Sugar
// serving size = 100g
// calories = amount, nutrient.name = "Energy"
// fat = amount, nutrient.name = "Total lipid (fat)"
// protein = amount, nutrient.name = "Protein"
// carbs = amount, nutrient.name = "Carbohydrate, by difference"
// fiber = amount, nutrient.name = "Fiber, total dietary"
// sugar = amount, nutrient.name = "Sugars, total including NLEA"

class Nutrient
{
    public string $name;
    public float $amount;
    public string $unit;

    public function __construct(string $name, float $amount, string $unit)
    {
        $this->name = $name;
        $this->amount = $amount;
        $this->unit = $unit;
    }
}

class Food
{
    public string $name;
    public array $nutrients;

    public function __construct(string $name, array $nutrients)
    {
        $this->name = $name;
        $this->nutrients = $nutrients;
    }
}