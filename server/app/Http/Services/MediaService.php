<?php
namespace App\Http\Services;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Medias\Models\Media;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Medias\Models\MediaDriveRelation;
use App\Http\Controllers\Medias\Models\MediaDirectory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use DateTime;
use Exception;


require_once app_path().'/Helpers/WP/plugin.php';
require_once app_path().'/Helpers/WP/class-wp-error.php';
require_once app_path().'/Helpers/WP/functions.php';
require_once app_path().'/Helpers/WP/media.php';


class MediaService {
    private $mediaModel_;
    private $mediaDirectoryModel_;
    private $thumbnailsPath = '/storage/uploads/drive/thumbnails/';
    private $originalPath = '/storage/uploads/drive/original/';
    private $storagePathOriginal = "original/";
    private $thumbnailsPathOriginal = "thumbnails/";
    private $drivePathOriginal = "public/uploads/drive/";
    private $mediaThumnailsDirectory = NULL;
    private $drivePath = NULL;
    private $thumbnailSizes = [
        '1'=>['200x200', '80x80', '400x400'],
    ];

    public function __construct(Media $mediaModel, MediaDirectory $mediaDirectoryModel) {
        $this->mediaModel_ = $mediaModel;
        $this->mediaDirectoryModel_ = $mediaDirectoryModel;
        $this->setPath(Storage::path($this->drivePathOriginal.$this->storagePathOriginal));
        $this->setMediaThumnailsDirectory(Storage::path($this->drivePathOriginal.$this->thumbnailsPathOriginal));

    }

    public function setPath($path){
        $this->drivePath = $path;
    }

    public function setMediaThumnailsDirectory($directory) {
        return $this->mediaThumnailsDirectory = $directory;
    }

    public function saveMedia(string $uploadFileName, $request) {
        $mediaDirectoryId = ($request->get('media_directory_id', -1));
        $selectedDirectory = $this->getMediaDirectory($mediaDirectoryId);
        $selectedDirectoryLocation = ($selectedDirectory == null)? "" : $selectedDirectory->location;
        $extension = strtolower($request->file($uploadFileName)->getClientOriginalExtension());
        $relationId = ($request->has('relation_id'))? $request->get('relation_id') : 0;
        $mediaType = ($request->has('media_type'))? $request->get('media_type') : 1;
        $filename  = sha1(time().$this->quickRandom(12)).".{$extension}";
        $uploadDirectory = $this->getPath($selectedDirectoryLocation, true);
        $upload_success = $request->file($uploadFileName)->move($uploadDirectory, $filename);
        $moduleId = $request->module_id;
        if($upload_success){
            $media = $this->createMediaEntity($uploadDirectory, $filename, $relationId, $mediaType, $selectedDirectory, $moduleId);
            $media->save();
            $file['result'] = 'success';
            $file['media'] = $media;
            $file['media_id'] = $media->id;
            $file['thumbnails'] = $media->thumbnails;
            $file['path_media_drive'] = $this->getPath();
            $file['path_media_drive_cache']=$this->getMediaThumnailsDirectory($selectedDirectoryLocation);
            return $file;
        } else {
            throw new Exception("Unable to upload the media: ". $upload_success);
        }
    }

    protected function findDirectoryById(int $id)
    {
        return $this->mediaDirectoryModel_->where('id', $id)->first();
    }

    public function createMediaEntity(string $path, string $originalFileName, $relationId = 0, $mediaType = 1, $selectedDirectory = null, $moduleId = null) {
        $originalName = File::basename($path.$originalFileName);
        $extension = File::extension($path.$originalFileName);
        $mimeType = File::mimeType($path.$originalFileName);
        $size = File::size($path.$originalFileName);
        if($this->isImage($extension)){
            list($originalWidth, $originalHeight) = getimagesize($path.$originalFileName);
        }else{
            $originalWidth = 0; $originalHeight = 0;
        }

        $media = [
            'file_name'=> $originalName,
            'media_directory_id'=> ($selectedDirectory == null)? null: $selectedDirectory->id,
            'media_title'=> $originalFileName,
            'media_alt_text'=> $originalFileName,
            'media_description'=> '',//$originalName,
            'media_mime_type'=> $mimeType,
            'media_size'=> $size,
            'media_extension'=> $extension,
            'media_is_image'=> ($this->isImage($extension))? true: false,
            'relation_id'=> $relationId,
            'media_type'=> $mediaType,
            'thumbnails'=> json_encode($this->getGenerateThumbnails($path, $originalName, $mediaType)),
            'media_width'=> $originalWidth,
            'media_height'=> $originalHeight,
            'is_default'=> false
        ];

        $media = $this->mediaModel_->create($media);
        if($relationId > 0) {
            $mediaRelation = new MediaDriveRelation();
            $mediaRelation->media_id = $media->id;
            $mediaRelation->module_id = $moduleId;
            $mediaRelation->relation_id = $relationId;
            $media->relations()->save($mediaRelation);
        }
        return $media;
    }

    private function getGenerateThumbnails(string $path, string $filename, $mediaType = 1) {
        $extension = File::extension($filename);
        if($this->isImage($extension)>0){
            $thumbnailSizes = $this->getThumbnailSizes($mediaType);
            if(count($thumbnailSizes) == 0)
                $thumbnailSizes = $this->getThumbnailSizes(100);
            $thumbnails = [];
            foreach($thumbnailSizes as $type=> $thumbnailSize){
                list($width,$height) = explode("x",$thumbnailSize);
                $WPImageEditor = wp_get_image_editor($path.$filename);
                $cacheFileName = $thumbnailSize."_".$filename;
                $WPImageEditor->resize($width,$height,true);
                $WPImageEditor->save(dirname($path).'/thumbnails/'.$cacheFileName);
                $thumbnails[] = $cacheFileName;
            }
        }else{
            $thumbnails = [];
        }
        return $thumbnails;
    }

    private function getMediaDirectory(int $mediaDirectoryId = -1) {
        if($mediaDirectoryId == -1)
            return null;
        return $this->findDirectoryById($mediaDirectoryId);
    }

    private function isImage($mime_type){
        if(!empty($mime_type)){
            if(array_key_exists($mime_type, $this->getImageMimeType()))
                return true;
            else
                return false;
        }else{
            return false;
        }
    }

    private function getImageMimeType(){
        return [
                'jpg'                 		   => 'image/jpeg',
                'jpeg'                		   => 'image/jpeg',
                'jpe'                 		   => 'image/jpeg',
                'gif'                          => 'image/gif',
                'png'                          => 'image/png',
                'bmp'                          => 'image/bmp',
                'tif|tiff'                     => 'image/tiff',
                'ico'                          => 'image/x-icon'
            ];
    }

    public function getThumbnailSizes($mediaType){
        if(array_key_exists($mediaType, $this->thumbnailSizes))
            return $this->thumbnailSizes[$mediaType];
        else
            return [];
    }

    private function quickRandom($length = 16){
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }

    public function getPath($subDirectory = "", $fullPath = false){
        if(!empty($subDirectory))
            return ($fullPath)? Storage::disk('public')->path($this->drivePathOriginal . $subDirectory."/") : $this->drivePathOriginal . $subDirectory."/";
        else
            return $this->drivePath;
    }

    public function getFilePath(string $fileName, $subDirectory = "",  $fullPath = false ) {
        return $this->getPath($subDirectory = "", $fullPath) . $fileName;
    }

    public function fetchMediaByMediaDirectoryId(int $mediaDirectoryId) {
        return $this->mediaModel_->where('media_directory_id', $mediaDirectoryId)->get();
    }

    public function fetchMediaByMediaDirectoryIds(array $mediaDirectoryIds) {
        return $this->mediaModel_->whereIn('media_directory_id', $mediaDirectoryIds)->get();
    }

    public function fetchMediaByMediaType(int $mediaTypeId) {
        return $this->mediaModel_->where('media_type', $mediaTypeId)->get();
    }

    public function searchMediaByName(string $keyword) {
        return $this->mediaModel_->where('media_title', 'LIKE', "%".$keyword."%")->get();
    }

    public function fetchUserMedia(int $userId) {
        $usersDirectories = $this->mediaDirectoryModel_->where('created_by', $userId)->select('id')->get();
        $directoryIds = $usersDirectories->map( function($directory){ return $directory->id; })->toArray();
        return $this->fetchMediaByMediaDirectoryIds($directoryIds);
    }

    public function getMediaThumnailsDirectory($subDirectory = null, $fullPath= false){
        if(!empty($subDirectory)) {
            return ($fullPath)? Storage::disk('public')->path($this->drivePathOriginal . $subDirectory."/thumbnails/") : $this->drivePathOriginal . $subDirectory."/thumbnails/";
        }
        return $this->mediaThumnailsDirectory;
    }

    public function fetchMediaByEntity(int $moduleId, $entityId, array $filters, $sortBy, $sort, $perPage) {
        $medias =
        $this->mediaModel_->select(Media::$tableName.".*")
                          ->join(MediaDriveRelation::$tableName, function($join){
                                $join->on(MediaDriveRelation::$tableName.'.media_id', '=', Media::$tableName.'.id');
                          })->where(function($query) use($moduleId, $entityId){
                            return $query->where(MediaDriveRelation::$tableName.'.module_id', $moduleId)
                                         ->where(MediaDriveRelation::$tableName.'.relation_id', $entityId);
                          })->orderBy(Media::$tableName.".".$sortBy, $sort)->paginate($perPage);

        $medias = $this->buildThumbnails($medias);

        return $medias;
    }

    public function fetchMedia(array $filters, $sortBy, $sort, $perPage) {

        $medias =
        $this->mediaModel_->select(Media::$tableName.".*");
        if(array_key_exists("module_id", $filters) || array_key_exists("relation_id", $filters)) {
            $this->mediaModel_ = $this->mediaModel_->join(MediaDriveRelation::$tableName, function($join) use($filters){
                $join->on(MediaDriveRelation::$tableName.'.media_id', '=', Media::$tableName.'.id')
                    ->where(function($query) use($filters) {
                        if(array_key_exists("module_id", $filters)) {
                            $query = $query->where(MediaDriveRelation::$tableName.'.module_id', $filters["module_id"]);
                        }
                        if(array_key_exists("relation_id", $filters)) {
                            $query = $query->where(MediaDriveRelation::$tableName.'.relation_id', $filters["relation_id"]);
                        }
                        return $query;
                    });
            });
        }

        if(array_key_exists("file_name", $filters)) {
            $this->mediaModel_ = $this->mediaModel_->simpleSearch([Media::$tableName.".media_title", Media::$tableName.".media_alt_text"], $filters["file_name"]);
        }

        if(array_key_exists("from", $filters)) {
            $this->mediaModel_ = $this->mediaModel_->where(Media::$tableName.'.created_at','>=', $filters["from"]);
        }

        if(array_key_exists("until", $filters)) {
            $this->mediaModel_ = $this->mediaModel_->where(Media::$tableName.'.created_at','<=', $filters["until"]);
        }

        $medias = $this->mediaModel_->orderBy(Media::$tableName.".".$sortBy, $sort)->paginate($perPage);
        $medias = $this->buildThumbnails($medias);

        return $medias;
    }

    public function attachMedia(Model $entity, array $mediaIds) {
        $moduleId = getModuleIdFromEntity($entity);
        if($moduleId === 0) {
            throw new Exception("Unable to attach media. Invalid module");
        }
        $mediaRelationships = [];
        foreach($mediaIds as $mediaId) {
            $mediaRelationships[] = [
                                        'module_id'=>$moduleId,
                                        'media_id'=> $mediaId,
                                        'relation_id'=> $entity->id,
                                        'created_at'=> new DateTime(),
                                        'updated_at'=> new DateTime()
                                    ];
        }
        MediaDriveRelation::insert($mediaRelationships);
    }

    public function createMediaRelationship(int $moduleId, int $relationId, array $mediaIds) {
        $mediaRelationships = [];
        foreach($mediaIds as $mediaId) {
            $mediaRelationships[] = [
                                        'module_id'=> $moduleId,
                                        'media_id'=> $mediaId,
                                        'relation_id'=> $relationId,
                                        'created_at'=> new DateTime(),
                                        'updated_at'=> new DateTime()
                                ];
        }
        MediaDriveRelation::insert($mediaRelationships);
    }

    private function buildThumbnails($medias) {
        $medias->each(function($media) {
            $media->uri = URL::to($this->originalPath.$media->file_name);
            if(!empty($media->thumbnails)) {
                $thumbnails = [];
                foreach(json_decode($media->thumbnails) as $thumbnail) {
                    $thumbnails[] = URL::to($this->thumbnailsPath.$thumbnail);
                }
                $media->thumbnails = $thumbnails;
            }
        });
        return $medias;
    }

    public function removeMedia(Model $entity) {
        $moduleId = getModuleIdFromEntity($entity);
        $relation_id = $entity->id;
        if($moduleId === 0) {
            throw new Exception("Unable to attach media. Invalid module");
        }

        MediaDriveRelation::where(function($query) use($moduleId, $relation_id){
            return $query->where('module_id', $moduleId)->where('relation_id', $relation_id);
        })->delete();
    }

    public function getMediaForEntity(Model $entity) {
        $moduleId = getModuleIdFromEntity($entity);
        $relationId = $entity->id;

        if($moduleId === 0) {
            throw new Exception("Unable to attach media. Invalid module");
        }
        return $this->fetchMediaByEntity($moduleId, $relationId, [], 'id', 'DESC', 100);
    }
}

?>
