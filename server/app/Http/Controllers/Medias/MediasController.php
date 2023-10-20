<?php

namespace App\Http\Controllers\Medias;

use Exception;
use App\Http\Services\MediaService;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Medias\Models\Media as ThisModel;
use App\Http\Controllers\Medias\Models\MediaDirectory;
use App\Http\Controllers\Medias\Requests\CreateMediaDirectoryRequest;
use App\Http\Controllers\Documents\Requests\UploadMediaRequest;
use Illuminate\Support\Facades\Storage;

class MediasController extends Controller
{
    const MEDIA_DIRECTORY_CREATED = 'Directory has been created successfuly';
    private $mediaService_ = NULL;
    public function __construct(ThisModel $model, MediaService $mediaService)
    {
        parent::__construct($model);
        $this->mediaService_ = $mediaService;
    }

    public function getDrivePath($subDirectory)
    {
        if (!empty($subDirectory)) {
            return $this->drivePathOriginal . $subDirectory;
        }
        return $this->drivePathOriginal;
    }

    public function upload(UploadMediaRequest $request)
    {
        try {
            $media = $this->mediaService_->saveMedia('file', $request);
            return $this->created(['message' => MediasController::RECORD_CREATED, 'media' => $media]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function delete(int $media_id)
    {
        $subDirectory = null;
        $media = $this->model->where('id', $media_id)->first();
        if ($media && !empty($media->file_name)) {
            if ($media->media_directory_id != NULL) {
                $mediaDirectory = $this->findDirectoryById($media->media_directory_id);
                if ($mediaDirectory != null) {
                    $subDirectory = $mediaDirectory->location;
                }
            }
            $thumbnails = json_decode($media->thumbnails);
            $filesTobeRemoved = [];
            if (count($thumbnails)) {
                foreach ($thumbnails as $thumbnail) {
                    $filesTobeRemoved[] = $this->mediaService_->getMediaThumnailsDirectory($subDirectory, false) . $thumbnail;
                }
            }
            $filesTobeRemoved[] = $this->mediaService_->getPath($subDirectory, false) . $media->file_name;
            if (count($filesTobeRemoved) > 0)
                File::delete($filesTobeRemoved);
            return $media->delete();
        }
    }

    public function storeDirectory(CreateMediaDirectoryRequest $request)
    {
        try {
            $directoryName = $request->get('directory_name');
            if ($this->createDirectory($directoryName)) {
                return $this->created(['message' => MediasController::MEDIA_DIRECTORY_CREATED]);
            }
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function listDirectories()
    {
        try {
            $directories = $this->mediaDirectoryModel_->orderBy($this->getSortBy(), $this->getSort())->paginate($this->getPerPage());
            return $this->created(['directories' => $directories]);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function createDirectory(string $directoryName)
    {
        $nameOnDrive = $directoryName . "_" . $this->userId();
        if (!Storage::has($this->getDrivePath($nameOnDrive))) {
            if (Storage::makeDirectory($this->getDrivePath($nameOnDrive))) {
                $mediaDirectory =  new MediaDirectory();
                $mediaDirectory->name = $directoryName;
                $mediaDirectory->location = $nameOnDrive;
                $mediaDirectory->created_by = $this->userId();
                return $mediaDirectory->save();
            }

            return false;
        } else
            throw new Exception("Directory '" . $directoryName . "' already exists.");
    }

    public function destroyMedia($id)
    {
        try {
            if ($this->delete($id)) {
                return $this->created(['message' => MediasController::RECORD_DELETED]);
            }
            return $this->noRecord(['messsage' => MediasController::RECORD_NOT_FOUND]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }

    public function destroyDirectory($id)
    {
        try {
            $directory = $this->findDirectoryById($id);
            if ($directory) {
                $mediasInDirectory = $this->mediaService_->fetchMediaByMediaDirectoryId($directory->id);
                $mediaIds = $mediasInDirectory->map(function ($media) {
                    return $media->id;
                })->toArray();

                if (isset($mediaIds[0])) {
                    $this->model->whereIn('id', $mediaIds)->delete();
                }

                if (Storage::disk('public')->move($this->getDrivePath($directory->location, true), $this->getDrivePath($directory->location, true) . "_deleted")) {
                    if ($directory->delete()) {
                        return $this->created(['message' => MediasController::RECORD_DELETED]);
                    }
                }
            }
            return $this->noRecord(['messsage' => MediasController::RECORD_NOT_FOUND]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return $this->serverSQLError($ex);
        } catch (Exception $ex) {
            return $this->serverError($ex);
        }
    }


    public function getMediaForEntity(int $moduleId, int $relationId)
    {
        $filters = request()->all();
        $media = $this->mediaService_->fetchMediaByEntity($moduleId, $relationId, $filters, $this->getSortBy(), $this->getSort(), $this->getPerPage());
        return $this->created(['media' => $media]);
    }

    public function getMedia()
    {
        $filters = request()->all();
        $media = $this->mediaService_->fetchMedia($filters, $this->getSortBy(), $this->getSort(), $this->getPerPage());
        return $this->created(['media' => $media]);
    }

    public function serve($filename)
    {
        $path = storage_path('public/' . $filename);
        if (!File::exists($path)) {
            abort(404);
        }
        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    }
}
