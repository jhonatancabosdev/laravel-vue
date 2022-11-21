<?php

use App\State;
use Illuminate\Database\Seeder;

class StateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $states =[
      ['id' => '1','name' => 'Washington','abv' => 'WA','country_id' => '1'],
      ['id' => '2','name' => 'Virginia','abv' => 'VA','country_id' => '1'],
      ['id' => '3','name' => 'Delaware','abv' => 'DE','country_id' => '1'],
      ['id' => '4','name' => 'District of Columbia','abv' => 'DC','country_id' => '1'],
      ['id' => '5','name' => 'Wisconsin','abv' => 'WI','country_id' => '1'],
      ['id' => '6','name' => 'West Virginia','abv' => 'WV','country_id' => '1'],
      ['id' => '7','name' => 'Hawaii','abv' => 'HI','country_id' => '1'],
      ['id' => '8','name' => 'Florida','abv' => 'FL','country_id' => '1'],
      ['id' => '9','name' => 'Wyoming','abv' => 'WY','country_id' => '1'],
      ['id' => '10','name' => 'New Hampshire','abv' => 'NH','country_id' => '1'],
      ['id' => '11','name' => 'New Jersey','abv' => 'NJ','country_id' => '1'],
      ['id' => '12','name' => 'New Mexico','abv' => 'NM','country_id' => '1'],
      ['id' => '13','name' => 'Texas','abv' => 'TX','country_id' => '1'],
      ['id' => '14','name' => 'Louisiana','abv' => 'LA','country_id' => '1'],
      ['id' => '15','name' => 'North Carolina','abv' => 'NC','country_id' => '1'],
      ['id' => '16','name' => 'North Dakota','abv' => 'ND','country_id' => '1'],
      ['id' => '17','name' => 'Nebraska','abv' => 'NE','country_id' => '1'],
      ['id' => '18','name' => 'Tennessee','abv' => 'TN','country_id' => '1'],
      ['id' => '19','name' => 'New York','abv' => 'NY','country_id' => '1'],
      ['id' => '20','name' => 'Pennsylvania','abv' => 'PA','country_id' => '1'],
      ['id' => '21','name' => 'California','abv' => 'CA','country_id' => '1'],
      ['id' => '22','name' => 'Nevada','abv' => 'NV','country_id' => '1'],
      ['id' => '23','name' => 'Puerto Rico','abv' => 'PR','country_id' => '1'],
      ['id' => '24','name' => 'Colorado','abv' => 'CO','country_id' => '1'],
      ['id' => '26','name' => 'Alaska','abv' => 'AK','country_id' => '1'],
      ['id' => '27','name' => 'Alabama','abv' => 'AL','country_id' => '1'],
      ['id' => '28','name' => 'Arkansas','abv' => 'AR','country_id' => '1'],
      ['id' => '29','name' => 'Vermont','abv' => 'VT','country_id' => '1'],
      ['id' => '30','name' => 'Illinois','abv' => 'IL','country_id' => '1'],
      ['id' => '31','name' => 'Georgia','abv' => 'GA','country_id' => '1'],
      ['id' => '32','name' => 'Indiana','abv' => 'IN','country_id' => '1'],
      ['id' => '33','name' => 'Iowa','abv' => 'IA','country_id' => '1'],
      ['id' => '34','name' => 'Oklahoma','abv' => 'OK','country_id' => '1'],
      ['id' => '35','name' => 'Arizona','abv' => 'AZ','country_id' => '1'],
      ['id' => '36','name' => 'Idaho','abv' => 'ID','country_id' => '1'],
      ['id' => '37','name' => 'Connecticut','abv' => 'CT','country_id' => '1'],
      ['id' => '38','name' => 'Maine','abv' => 'ME','country_id' => '1'],
      ['id' => '39','name' => 'Maryland','abv' => 'MD','country_id' => '1'],
      ['id' => '40','name' => 'Massachusetts','abv' => 'MA','country_id' => '1'],
      ['id' => '41','name' => 'Ohio','abv' => 'OH','country_id' => '1'],
      ['id' => '42','name' => 'Utah','abv' => 'UT','country_id' => '1'],
      ['id' => '43','name' => 'Missouri','abv' => 'MO','country_id' => '1'],
      ['id' => '44','name' => 'Minnesota','abv' => 'MN','country_id' => '1'],
      ['id' => '45','name' => 'Michigan','abv' => 'MI','country_id' => '1'],
      ['id' => '46','name' => 'Rhode Island','abv' => 'RI','country_id' => '1'],
      ['id' => '47','name' => 'Kansas','abv' => 'KS','country_id' => '1'],
      ['id' => '48','name' => 'Montana','abv' => 'MT','country_id' => '1'],
      ['id' => '49','name' => 'Mississippi','abv' => 'MS','country_id' => '1'],
      ['id' => '50','name' => 'South Carolina','abv' => 'SC','country_id' => '1'],
      ['id' => '51','name' => 'Kentucky','abv' => 'KY','country_id' => '1'],
      ['id' => '52','name' => 'Oregon','abv' => 'OR','country_id' => '1'],
      ['id' => '53','name' => 'South Dakota','abv' => 'SD','country_id' => '1'],
    ];

    foreach ($states as $state) {
      State::create($state);
    }
    }
}