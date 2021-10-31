<?php

// https://fdc.nal.usda.gov/api-guide.html

include_once __DIR__ . "/../servers.php";
include_once __DIR__ . "/food_ids.php";

$food_get = "https://api.nal.usda.gov/fdc/v1/food/";
$foods_get = "https://api.nal.usda.gov/fdc/v1/foods/";
$apple_no = "171515";
$api_query = "?api_key=" . API_KEY_FDC;

// $query = $food_get . $apple_no . $api_query /*. "&format=abridged"*/;


$ids = "";
foreach ($food_ids as $id => $_) {
    $ids .= "&fdcIds=$id";
}
// $ids = substr($ids, 0, strlen($ids) - 1);
// $nut_ids = "";
// foreach ($nutrient_ids as $nut_id => $_) {
//     $nut_ids .= "&nutrients=$nut_id";
// }

$query = $foods_get . $api_query . $ids  /* . $nut_ids */;

echo $query . "\n";

echo "[get_data] getting data...\n";
$data = file_get_contents($query);
echo "[get_data] received data\n";

// file_put_contents("data.json", json_encode(json_decode($data, true), JSON_PRETTY_PRINT));

$data = json_decode($data, true);

// $calories_name = "Energy";
// $fat_name = "Total lipid (fat)";
// $protein_name = "Protein";
// $carbs_name = "Carbohydrate, by difference";
// $fiber_name = "Fiber, total dietary";
// $sugar_name = "Sugars, total including NLEA";

$foods = [];
foreach ($data as $food) {
    $food_nutrients = $food['foodNutrients'];
    // echo "{$food["fdcId"]} {$food_ids[$food["fdcId"]]}\n";
    $nuts = array();
    foreach ($food_nutrients as $nutrient) {
        if (array_key_exists($nutrient['nutrient']['id'], $nutrient_ids)) {
            $nut = new Nutrient(
                $nutrient['nutrient']['name'],
                $nutrient['amount'],
                $nutrient['nutrient']['unitName'],
            );
            // echo "{$nutrient['nutrient']['id']}\n";
            $nuts[] = $nut;
        }
    }

    // foreach ($nuts as $nutrient) {
    //     echo "{$nutrient->name} {$nutrient->amount} {$nutrient->unit}\n";
    // }

    $foods[] = new Food($food_ids[$food["fdcId"]], $nuts);
}

foreach ($foods as $food) {
    echo "\n{$food->name}\n";
    foreach ($nuts as $nutrient) {
        echo "{$nutrient->name} {$nutrient->amount} {$nutrient->unit}\n";
    }
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