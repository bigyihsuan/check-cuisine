<?php

// https://fdc.nal.usda.gov/api-guide.html

include_once __DIR__ . "/../servers.php";

$food_get = "https://api.nal.usda.gov/fdc/v1/food/";
$apple_no = "171515";
$api_query = "?api_key=" . API_KEY_FDC;

$query = $food_get . $apple_no . $api_query /*. "&format=abridged"*/;

echo $query . "\n";

echo "[get_data] getting data...\n";
$data = file_get_contents($query);
echo "[get_data] received data\n";
$data = json_decode($data, true);

$calories_name = "Energy";
$fat_name = "Total lipid (fat)";
$protein_name = "Protein";
$carbs_name = "Carbohydrate, by difference";
$fiber_name = "Fiber, total dietary";
$sugar_name = "Sugars, total including NLEA";

$food_nutrients = $data['foodNutrients'];

$nuts = array();

foreach ($food_nutrients as $nutrient) {
    switch ($nutrient['nutrient']['name']) {
        case $calories_name:
        case $fat_name:
        case $protein_name:
        case $carbs_name:
        case $fiber_name:
        case $sugar_name: {
                $nut = new Nutrient(
                    $nutrient['nutrient']['name'],
                    $nutrient['amount'],
                    $nutrient['nutrient']['unitName'],
                );
                $nuts[] = $nut;
                break;
            }
        default: {
                break;
            }
    }
}

foreach ($nuts as $nutrient) {
    echo "{$nutrient->name} {$nutrient->amount} {$nutrient->unit}\n";
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