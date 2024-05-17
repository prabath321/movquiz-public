<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Promise\Utils as PromiseUtils;

class CronController extends Controller
{
    //
    public function index(Request $request){

        //Test

        $secret = $request->route('secret');

        if($secret=="mymovquiz@123"){

            $list  = array('upcoming','top_rated','popular','now_playing');

            $url_array = array();
            foreach($list as $li){
                for($i=1;$i<7;$i++){
                    $url_array [] = env('APP_URL')."/api/movie/questions/movie/".$li."/title/".$i;
                    
                }

            }

            $list_tv  = array('airing_today','on_the_air','popular','top_rated');

            foreach($list_tv as $li){
                for($i=1;$i<7;$i++){
                    $url_array [] = env('APP_URL')."/api/movie/questions/tv/".$li."/original_name/".$i;
                    
                }

            }


            $client = new \GuzzleHttp\Client();


            // Create an array to store promises
            $promises = [];


            $promises = [];

            // Loop through each URL and create a promise for each
            foreach ($url_array as $url) {
                $promises[] = $client->getAsync($url);
            }

            // Use Guzzle's Promise\all() method to execute all promises concurrently
            $results = PromiseUtils::all($promises)->wait();

            // Process the results
            foreach ($results as $response) {
                // Check if the response is successful
                if ($response->getStatusCode() == 200) {
                    // echo $response->getBody()->getContents() . PHP_EOL;
                    echo "Success";
                } else {
                    // Handle error
                    echo "Error: " . $response->getStatusCode() . PHP_EOL;
                }
            }

            
        }
        
    }

    function callServer($url){
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 
        $url
        , [
        'headers' => [
            'accept' => 'application/json',
        ],
        ]);

        return $response;
    }
}
