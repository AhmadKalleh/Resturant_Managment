<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\TranslateHelper\TranslateHelper;
use App\Http\Controllers\Upload\UplodeImageHelper;
use App\Models\Table;
use Auth;
use Illuminate\Http\Request as HttpRequest;
use Request;
use Storage;

class TableService {
use UplodeImageHelper;

    use TranslateHelper;
        public function index():array
    {


        $lang = Auth::user()->preferred_language;
        $tables = Table::with('image')->get();
        if (!$tables) {
        return [
            'data' => [],
            'message' => __('message.resources_not_found', [], $lang),
            'code' => 404,
        ];
    }
        $data = [];
        foreach ($tables as $table)
        {
            $data[] = [
                'id'=> $table['id'],
                'location' => $table->getTranslation('location', $lang),
                'seats'=> $table['seats'],
                'status'=> $this->translate('status',$table['status']),
                'price' => $table->price_text,
                'image_path' => $table->image ? url('storage/' . $table->image->path) : null,
            ];
        }

       $message = __('message.All_Tables_Retrives',[],$lang);
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }
            public function store($request):array
    {


        $lang = Auth::user()->preferred_language;
        $table = Table::create([
            'seats' => $request['seats'],
            'location' =>[
            'en' =>  $request['location_en'],
            'ar' =>  $request['location_ar'],
            ],
            'price' =>$request['price'],
        ]);

       $table->image()->create([
            'path' => $this->uplodeImage($request['table_Image'],"tables"),
        ]);
        $data = [true];


       $message = __('message.Table_Created_Successfully',[],$lang);
        $code = 200;

        return ['data' =>$data,'message'=>$message,'code'=>$code];

    }
public function update($request, $id)
{
    $lang = Auth::user()->preferred_language;

    $table = Table::find($id);
    if (!$table) {
        return [
            'data' => [],
            'message' => __('message.resource_not_found', [], $lang),
            'code' => 404,
        ];
    }

    $table->update([
        'seats' => $request['seats'] ?? $table->seats,
        'location' =>[
            'en' =>  $request['location_en']?? $table->location->en,
            'ar' =>  $request['location_ar']?? $table->location->ar,
            ],
        'price' => $request['price'] ?? $table->price,
    ]);

    if (request()->hasFile('table_Image')) {
        if ($table->image) {
            Storage::disk('public')->delete($table->image->path);
        }

        $path = $this->uplodeImage(request()->file('table_Image'), 'tables');

        $table->image()->updateOrCreate([], ['path' => $path]);
    }

    return [
        'data' => [true],
        'message' => __('message.Table_Updated_Successfully', [], $lang),
        'code' => 200,
    ];
}


public function destroy($id)
{
    $lang = Auth::user()->preferred_language;

    $table = Table::find($id);
    if (!$table) {
        return [
            'data' => [],
            'message' => __('message.resource_not_found', [], $lang),
            'code' => 404,
        ];
    }

    $table->delete();

        if ($table->image) {
            Storage::disk('public')->delete($table->image->path);
        }

    return [
        'data' => [true],
        'message' => __('message.Table_Delete_Successfully', [], $lang),
        'code' => 200,
    ];
}
public function show ( $id ){
 $table = Table::with('image')->find($id);
     $lang = Auth::user()->preferred_language;

    if (!$table) {
        return [
            'data' => [],
            'message' => __('message.resource_not_found', [], $lang),
            'code' => 404,
        ];
    }

    $data[]=[
    "id"=>$table->id,
    'location' => $table->getTranslation('location', $lang),
    'seats'=> $table->seats,
    'status'=> $this->translate('status',$table['status']),
    'price' => $table->price_text,
    'image_path' => $table->image ? url('storage/' . $table->image->path) : null,
    ];
 return [
        'data' => [$data],
        'message' => __('Table_Displayed_Successfully', [], $lang),
        'code' => 200,
    ];
}
}
