<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;

use Redirect;
use Request;
use Response;
use Config;
use Validator;
use Auth;

use App\Helpers\FlashMessengerHelper;

class HomeController extends Controller
{
    /**
     * Create action
     */
    public function create()
    {

        $targetDir = 'uploads/';
        if (!empty($_FILES)) {

            $targetFile = $targetDir . time() . '-' . $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $targetFile);

            exit();
        }

        /*BEGIN POST*/
        if(Request::isMethod('post')) {
            $data = Request::input();

            $files = File::allFiles('uploads');

            $base64_images = null;
            foreach ($files as $file)
            {
                echo (string)$file, "\n";
                $fileName= (string)$file;
                $base64_images = base64_encode(file_get_contents ($fileName));
            }

            $data2 = array();
            $data2['product'] = $data['product'];

            $data2['product']['images'] = array( array(
                'attachment'=>$base64_images

            ));

            $data2['product']['variants'] = array( array(
                'price'=>$data['price'],
                'sku'=>rand()

            ));

            $url = "https://72eabad8d897e6e6909625662959e010:332fe437357b6237e271cd2ad9dc1dc3@isratstore.myshopify.com/admin/products.json";

            $content = json_encode($data2);

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER,
                array("Content-type: application/json"));
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

            $json_response = curl_exec($curl);

            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ( $status != 201 ) {
                die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
            }
            curl_close($curl);

            File::cleanDirectory('uploads');

            FlashMessengerHelper::addSuccessMessage('Product successfully created!');
            return Redirect::action('HomeController@index');


        }
        /*END POST*/

        return view("domain.create", array());
    }

    /**
     * Index
     */
    public function index()
    {
        return view("home", array());
    }
}
