<?php

namespace App\Http\Controllers\Favorite;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Throwable;


class FavoriteController extends Controller
{
    use ResponseHelper;

    private FavoriteService $_favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->_favoriteService = $favoriteService;
    }

    public function index():JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_favoriteService->index();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }


    public function store(FormRequestFavorite $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_favoriteService->store($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function destroy(FormRequestFavorite $request):JsonResponse
    {
        $data=[];

        try
        {
            $data = $this->_favoriteService->destroy($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

}
