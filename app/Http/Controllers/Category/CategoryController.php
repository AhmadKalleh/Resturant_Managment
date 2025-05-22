<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Category\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Throwable;

class CategoryController extends Controller
{
    use ResponseHelper;
    private CategoryService $_categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->_categoryService = $categoryService;
    }
    public function index():JsonResponse
    {

        $data=[];
        try
        {
            $data = $this->_categoryService->index();
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function show(FormRequestCategory $request):JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_categoryService->show($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function store(FormRequestCategory $request):JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_categoryService->store($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function update(FormRequestCategory $request):JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_categoryService->update($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }

    public function destroy(FormRequestCategory $request):JsonResponse
    {

        $data=[];

        try
        {
            $data = $this->_categoryService->delete($request);
            return $this->Success($data['data'],$data['message'],$data['code']);
        }
        catch(Throwable $e)
        {
            $message = $e->getMessage();
            return $this->Error($data,$message);
        }
    }
}
