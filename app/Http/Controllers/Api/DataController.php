<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\YourModel;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function index(Request $request)
    {
	$todaydate = date('Y-m-d');
	//$todaydate = '2024-05-25';    
        $modelName = $request->route('model');
        $series = $request->route('series');
        // Dynamically instantiate the model based on the provided class name
        $modelClass = 'App\\Models\\' . $modelName;
        $data = $modelClass::where('question_series', $series)
        ->where('published_at',$todaydate)
        ->get();

	$res1 = array(
        "status"=>200,
        "message"=>'Success',
        "questions_items"=>$data
        );

        header('Content-Type: application/json');


        return response()->json($res1);
    }


    public function level(Request $request)
    {

        $todaydate = date('Y-m-d');
        //$todaydate = '2024-05-25';
        $modelName = $request->route('newmodel');
        $modelClass = 'App\\Models\\' . $modelName;

        if (! class_exists($modelClass) || ! is_subclass_of($modelClass, \Illuminate\Database\Eloquent\Model::class)) {
            return response()->json(array(
                "status" => 404,
                "message" => "Model not found",
                "level_items" => array()
            ), 404);
        }

        $result_final = array();

        for ($i = 1; $i <= 6; $i++) {
            $movie = $modelClass::where('question_series', $i)
                ->where('published_at', $todaydate)
                ->first();

            $result_final[] = array(
                'index' => $i,
                'level_title' => "LEVEL " . $i,
                'img' => $movie ? $movie->backdrop_url : null,
            );
        }

        $res1 = array(
            "status" => 200,
            "message" => 'Success',
            "level_items" => $result_final
        );

        return response()->json($res1);
    }
}
