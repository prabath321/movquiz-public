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
        $modelName = $request->route('model');
        $series = $request->route('series');
        // Dynamically instantiate the model based on the provided class name
        $modelClass = 'App\\Models\\' . $modelName;
        $data = $modelClass::where('question_series', $series)
        ->where('published_at',$todaydate)
        ->get();
        // $data = $modelClass::all();
        return response()->json($data);
    }
}

