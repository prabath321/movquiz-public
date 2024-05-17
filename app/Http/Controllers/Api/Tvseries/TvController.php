<?php

namespace App\Http\Controllers\Api\Tvseries;

use App\Http\Controllers\Controller;
use App\Models\HomeImage;
use Illuminate\Http\Request;
use Treinetic\ImageArtist\lib\Image;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\DB;

class TvController extends Controller
{
    protected $s3Client;
    private $poster_image_size = 'w154';

    public function __construct(S3Client $s3Client)
    {
        $this->s3Client = $s3Client;
    }


    public function home(){

        $this->page(1);

        /*$this->page(2);

        $this->page(3);

        $this->page(4);

        $this->page(5);

        $this->page(6);

        $this->page(7);*/

        
    }

    public function home1(){
        header('Content-Type: application/json');

        
        $response_arr = array(
        "status"=>200,
        "message"=>'Success',
        "featured"=>array(
            array('index'=>1,'title'=>'Upcoming','img'=>env('APP_URL')."/img/upcoming1.jpg"),
            array('index'=>2,'title'=>'Now Playing','img'=>env('APP_URL')."/img/now_playing1.jpg"),
            array('index'=>3,'title'=>'Popular','img'=>env('APP_URL')."/img/popular1.jpg"),
            array('index'=>4,'title'=>'Top Rated','img'=>env('APP_URL')."/img/top_rated1.jpg")
        ),
         "watchlist"=>array(array('index'=>1,'title'=>'Airing Today','img'=>env('APP_URL')."/img/tv_airing_today1.jpg"),
         array('index'=>2,'title'=>'On The Air','img'=>env('APP_URL')."/img/tv_on_the_air1.jpg"),
         array('index'=>3,'title'=>'Popular','img'=>env('APP_URL')."/img/tv_popular1.jpg"),
         array('index'=>4,'title'=>'Top Rated','img'=>env('APP_URL')."/img/tv_top_rated1.jpg")
     ),
        // "data"=>array(array("content"=>array())),
        

    );
        return response()->json($response_arr); 
        
    }

    public function page($page=1){
        $url1 = env('TMDB_URL').'tv/airing_today?language=en-US&adult=false&region=us&language=en-US&page='.$page;
        $url2 = env('TMDB_URL').'tv/on_the_air??language=en-US&adult=false&region=us&language=en-US&page='.$page;
        $url3 = env('TMDB_URL').'tv/top_rated?language=en-US&adult=false&region=us&language=en-US&page='.$page;
        $url4 = env('TMDB_URL').'tv/popular?language=en-US&adult=false&region=us&language=en-US&page='.$page;

        $data1 =  json_decode($this->callTMDB($url1)->getBody(), false);
        $data2 =  json_decode($this->callTMDB($url2)->getBody(), false);
        $data3 =  json_decode($this->callTMDB($url3)->getBody(), false);
        $data4 =  json_decode($this->callTMDB($url4)->getBody(), false);

        $this->createImage($data1,"tv_airing_today".$page."_");
        $this->createImage($data2,"tv_on_the_air".$page."_");
        $this->createImage($data3,"tv_top_rated".$page."_");
        $this->createImage($data4,"tv_popular".$page."_");
    }

    function createImage($data, $image_name){
        if(isset($data->results[0])){
            if($data->results[0]->poster_path==""){
                $image1 = new Image(env('APP_URL')."/img/blank_movie.jpg");
                $image1->scale(22.5);
            }else{
                $image1 = new Image(env('TMDB_BACKDROP_IMGPATH').$this->poster_image_size.$data->results[0]->poster_path);
            }

            if($data->results[1]->poster_path==""){
                $image2 = new Image(env('APP_URL')."/img/blank_movie.jpg");
                $image2->scale(22.5);
            }else{
                $image2 = new Image(env('TMDB_BACKDROP_IMGPATH').$this->poster_image_size.$data->results[1]->poster_path);
            }

            if($data->results[2]->poster_path==""){
                $image3 = new Image(env('APP_URL')."/img/blank_movie.jpg");
                $image3->scale(22.5);
            }else{
                $image3 = new Image(env('TMDB_BACKDROP_IMGPATH').$this->poster_image_size.$data->results[2]->poster_path);
            }


            if($data->results[3]->poster_path==""){
                $image4 = new Image(env('APP_URL')."/img/blank_movie.jpg");
                $image4->scale(22.5);
            }else{
                $image4 = new Image(env('TMDB_BACKDROP_IMGPATH').$this->poster_image_size.$data->results[3]->poster_path);
            }
        
        }else{
            $image1 = new Image(env('APP_URL')."/img/blank_movie.jpg");
            $image2 = new Image(env('APP_URL')."/img/blank_movie.jpg");
            $image3 = new Image(env('APP_URL')."/img/blank_movie.jpg");
            $image4 = new Image(env('APP_URL')."/img/blank_movie.jpg");
            $image1->scale(22.5);
            $image2->scale(22.5);
            $image3->scale(22.5);
            $image4->scale(22.5);
        }
        

        $img1 = $image1->merge($image2,$image3->getWidth(),0);
        $img2 = $image3->merge($image4,$image3->getWidth(),0);

        $imgFinal = $img1->merge($img2,0,$img1->getHeight());

        $base64URLData = $imgFinal->getDataURI(IMAGETYPE_JPEG);
        // echo "<img src='$base64URLData' />";
        //$imgFinal->save("img/".$image_name);

        $imageData = base64_decode(str_replace('data:image/jpeg;base64,', '', $base64URLData));

        // Assuming 'image' is the key for the base64url-encoded image data
        
        // Convert base64url to base64
        //$base64Data = str_replace(['-', '_'], ['+', '/'], $base64URLData);

        // Decode base64 data
        //$imageData = base64_decode($base64URLData);

        // Generate a unique filename or use the original filename
        $filename = $image_name.uniqid() . '.jpg'; // You can use any filename and extension you prefer

        // Upload image to S3 bucket
        



        $bucketName = env('AWS_BUCKET');
        $this->s3Client->putObject([
            'Bucket' => $bucketName,
            'Key'    => $filename,
            'Body'   => $imageData,
            'ACL'    => 'public-read',
            'ContentType' => 'image/jpeg'
        ]);


    
        

        $publicUrl = $this->s3Client->getObjectUrl($bucketName, $filename);

        $todaydate = date('Y-m-d');

        $results = array(
            "type" => 'tv',
            "image_url" => $publicUrl,
            "published_at" => $todaydate,
            "created_at" => now()
        );

        DB::table('home_images')->insert($results);



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
