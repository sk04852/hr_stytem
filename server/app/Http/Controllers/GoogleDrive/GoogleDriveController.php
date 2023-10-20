<?php
namespace App\Http\Controllers\GoogleDrive;

use Google_Client;
use Google_Service_Drive;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoogleDrive\Requests\UploadRequest;
use App\Http\Controllers\Users\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;

class GoogleDriveController extends Controller
{
    private $client;
    public function __construct()
    {
        $client = new Google_Client();
        $client->setAuthConfig('credentials.json');
        $client->addScope(Google_Service_Drive::DRIVE);
        $guzzleClient = new \GuzzleHttp\Client(array('curl' => array(CURLOPT_SSL_VERIFYPEER => false)));
        $client->setHttpClient($guzzleClient);
        $this->client = $client;
        $this->client->setAccessType("offline");
        $this->client->setApprovalPrompt("force");
    }

    public function oauth()
    {
        $rurl = action('GoogleDrive\GoogleDriveController@oauth');
        $this->client->setRedirectUri($rurl);
        if (!isset($_GET['code'])) {
            $auth_url = $this->client->createAuthUrl();
            $filtered_url = filter_var($auth_url, FILTER_SANITIZE_URL);
            return redirect($filtered_url);
        } else {
            $this->client->authenticate($_GET['code']);
            $this->client->setAccessToken($this->client->getAccessToken());
            $user = User::where('id',1)->first();
            $user->drive_session_id = $this->client->getAccessToken();
            $user->save();
            return redirect()->route('drive.index');
        }
    }


    public function index()
    {
        $driveToken = User::select('drive_session_id')->where('id',1)->first();
        if (isset($driveToken->drive_session_id)) {
            $this->client->setAccessToken($driveToken->drive_session_id);
            $service = new Google_Service_Drive($this->client);
            $three_months_ago = Carbon::now()->subMonths(6)->toRfc3339String();
            $parameters = [
                'q' => "viewedByMeTime >= '$three_months_ago' or modifiedTime >= '$three_months_ago'",
                'orderBy' => 'modifiedTime desc',
                'fields' => 'nextPageToken, files(id, name, modifiedTime, iconLink, webViewLink, webContentLink, size, shared, trashed)',
            ];
            $results = $service->files->listFiles($parameters);
            return view('showDrive', ['results' => $results]);
        } else {
            return redirect()->route('drive_oauthCallback');
        }
    }

    public function search($query)
    {
        $files = [];
        $driveToken = User::select('drive_session_id')->where('id',1)->first();
        if (isset($driveToken->drive_session_id)) {
            $this->client->setAccessToken($driveToken->drive_session_id);
            $service = new Google_Service_Drive($this->client);
            $parameters = [
                'q' => "name contains '$query'",
                'fields' => 'files(id, name, modifiedTime, iconLink, webViewLink, webContentLink, size, shared, trashed)',
            ];
            $result = $service->files->listFiles($parameters);
            if($result){
                $files = $result->getFiles();
            }
        }else {
            return redirect()->route('drive_oauthCallback');
        }
        $page_data = [
            'query' => $query,
            'files' => $files
        ];
        return  $page_data;
   }


    public function delete($id)
    {
        try {
            $driveToken = User::select('drive_session_id')->where('id',1)->first();
            if (isset($driveToken->drive_session_id)) {
            $this->client->setAccessToken($driveToken->drive_session_id);
            $service = new Google_Service_Drive($this->client);
            $results = $service->files->delete($id);
            } else {
            return redirect()->route('drive_oauthCallback');
            }
        } catch (Exception $e) {
            return ('Something went wrong while trying to delete the file');
        }

        return ('File was deleted');
    }


    public function upload()
    {
        return view('uploadToDrive');
    }


    public function doUpload(UploadRequest $request)
    {
        $file = $request->file('file');
        $mime_type = $file->getMimeType();
        $title = $file->getClientOriginalName();
        $file->move(public_path('Uploads'), $title);
        $description = $request->input('description');
        $driveToken = User::select('drive_session_id')->where('id',1)->first();
        try {
            if (isset($driveToken->drive_session_id)) {
                $this->client->setAccessToken($driveToken->drive_session_id);
                $service = new Google_Service_Drive($this->client);
                $drive_file = new \Google_Service_Drive_DriveFile();
                $drive_file->setName($title);
                $drive_file->setDescription($description);
                $drive_file->setMimeType($mime_type);
                $drive_file->setParents(array('18IJy4GpymMnXyJwZ0quAl4dW5M4TCRRi'));
                $createdFile = $service->files->create($drive_file, [
                    'data' => file_get_contents(public_path('Uploads/'.$title)),
                    'mimeType' => $mime_type,
                    'includePermissionsForView'=>'published',
                    'uploadType' => 'multipart'
            ]);
            return view('showUpload', ['id' => $createdFile->getId()]);
            } else {
                return redirect()->route('drive_oauthCallback');
            }
            
        } catch (Exception $e) {

            return 1;
        }
    }

    public function logout(Request $request)
    {
        $driveToken = User::where('id',1)->first();
        $this->client->setAccessToken($driveToken->drive_session_id);
        $this->client->getAccessToken();
        $this->client->revokeToken($this->client->getAccessToken());
        $driveToken->drive_session_id=null;
        $driveToken->save();
        return redirect()->route('drive_oauthCallback');
    }

}