<?php

namespace App\Http\Controllers\Api\Movie;

use Illuminate\Routing\Controller as BaseController;
use LengthException;
use Treinetic\ImageArtist\lib\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Aws\S3\S3Client;



class UpcomingController extends BaseController
{
    private $backdrop_image_size = 'w300';
    protected $s3Client;

    public function __construct(S3Client $s3Client)
    {
        $this->s3Client = $s3Client;
    }

    public function upcoming(Request $request){

        

        $keyword = $request->route('keyword');
        $keyword1 = $request->route('keyword1');
        $keyword2 = $request->route('keyword2');
        $series = $request->route('series');

        $question_content = 'Movie';
        if($keyword=='tv'){
            $question_content = 'TV Series';
        }

        
        $url1 = env('TMDB_URL').$keyword."/".$keyword1.'?language=en-US&adult=false&region=US&page=1';

        $url2 = env('TMDB_URL').$keyword."/".$keyword1.'?language=en-US&adult=false&region=US&page=2';
        
        $url3 = env('TMDB_URL').$keyword."/".$keyword1.'?language=en-US&adult=false&region=&page=3';

        $url4 = env('TMDB_URL').$keyword."/".$keyword1.'?language=en-US&adult=false&region=US&page=4';

        $data1 =  json_decode($this->callTMDB($url1)->getBody(), true);

        $data2 =  json_decode($this->callTMDB($url2)->getBody(), true);

        $data3 =  json_decode($this->callTMDB($url3)->getBody(), true);

        $data4 =  json_decode($this->callTMDB($url4)->getBody(), true);

        // echo "<pre>";
        // print_r($data1);
        // echo "</pre>";
        // die();
        // echo "<pre>";
        // print_r($data2);
        // echo "</pre>";


        $data_temp = array_merge($data1['results'],$data2['results']);

        $data_temp1 = array_merge($data_temp,$data3['results']);

        $data = array_merge($data_temp1,$data4['results']);
        
        $array_with_backdrops = array();
        $index = 0;
        foreach($data as $val){
            // echo "<pre>";
            // print_r($val['backdrop_path']);
            if($val['backdrop_path']==""){
                continue;
            }else{
               $array_with_backdrops[$index++] = $val;
            }
            // var_dump($values);
            // echo "</pre>";
        }
       
        // echo "<pre>";
        // echo count($array_with_backdrops);
        // echo "</pre>";
        
        $in = array();
        for($i=0;$i<count($array_with_backdrops);){
            $rand = random_int(0,count($array_with_backdrops)-1);
            if(in_array($rand,$in)){
                continue;
            }else{
                $in[$i] = $rand;
                $i++;
            }

        }
        // echo "INNININNINININININNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN";
        // echo count($in);
        // echo "INNININNINININININNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNN";
        
       
        
        

        $in_answers_correct = array_slice($in,0,9);

        // echo "<br />";
        // echo "Answers Correct";
        // print_r($in_answers_correct);
        // echo "Answers Correct";

        $in_final = array_slice($in,10,count($in));

        $final_array_answers_wrong = array_chunk($in_final,3);

        // echo "<br />";
        // echo "Answers Wrong";
        // print_r($final_array_answers_wrong);
        // echo "Answers Wrong";

        $final_questions = array();
        for($counter=0;$counter<8;$counter++){
            $temp_arr = $final_array_answers_wrong[$counter]; 
            $temp_arr[4] = $in_answers_correct[$counter];
            shuffle($temp_arr);
            $final_questions[$counter] = array($temp_arr, $in_answers_correct[$counter]);
        }

	$todaydate = date('Y-m-d');
	//$todaydate = '2024-05-25';
        //echo "<br />";
        //echo "Answers Question";
        //print_r($final_questions);
        //var_dump($final_questions[1]);
        $results = array();
        $questions_counter = 1;
        foreach($final_questions as $questions){
            
            $image1 = new Image(env('TMDB_BACKDROP_IMGPATH').$this->backdrop_image_size.$array_with_backdrops[$questions[1]]['backdrop_path']);

            $base64URLData = $image1->getDataURI(IMAGETYPE_JPEG);
            $imageData = base64_decode(str_replace('data:image/jpeg;base64,', '', $base64URLData)); 
            $filename = $keyword1."_".$series."_".uniqid(). '.jpg';

            // $bucketName = env('AWS_BUCKET');
            // $this->s3Client->putObject([
            //     'Bucket' => $bucketName,
            //     'Key'    => $filename,
            //     'Body'   => $imageData,
            //     'ACL'    => 'public-read',
            //     'ContentType' => 'image/jpeg'
            // ]);

            // $public_url = $this->s3Client->getObjectUrl($bucketName, $filename);
            // echo "<pre />";
            // var_dump($array_with_backdrops);
            // echo "<pre />";
            // die();


            // Create folder if it doesn't exist
            $folder = public_path('movie_and_tv_images');

            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            // Save image
            file_put_contents($folder . '/' . $filename, $imageData);

            // Path to store in database
            $imagePath = 'movie_and_tv_images/' . $filename;


            $results += array(($questions_counter-1)=>array(
                "question_series" => $series,
                "question_number" => $questions_counter,
                "question" => "Name of the ".$question_content."?",
                "choice1" => $array_with_backdrops[$questions[0][0]][$keyword2],
                "choice2" => $array_with_backdrops[$questions[0][1]][$keyword2],
                "choice3" => $array_with_backdrops[$questions[0][2]][$keyword2],
                "choice4" => $array_with_backdrops[$questions[0][3]][$keyword2],
                "answer"  => array_search($questions[1], $questions[0])+1,
                "backdrop_url" => $imagePath,
                "published_at" => $todaydate,
                "created_at" => now()

            ));
            $questions_counter++;
        }
        //echo "Answers Question";
        $table_name = $keyword."_".$keyword1;

        DB::table($table_name)->insert($results);

        header('Content-Type: application/json');

        $response_arr = array(
            "status"=>200,
            "message"=>'Success',
            "response"=> $results
        );

        return response()->json($response_arr); 

        
    }

    function callTMDB($url){
        $client = new \GuzzleHttp\Client();
        $auth = 'Bearer '.env('TMDB_TOKEN');

        $response = $client->request('GET', 
        $url
        , [
        'headers' => [
            'Authorization' => $auth,
            'accept' => 'application/json',
        ],
        ]);

        return $response;
    }


    
}
