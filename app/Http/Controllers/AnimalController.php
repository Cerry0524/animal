<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class AnimalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //使用網址設定為快取檔案名稱
        //取得網址

        $url = $request->url();
        //取得query參數 例如?limit=5&page=2 網址問號後面參數
        $queryParams = $request->query();
        //每個人請求query順序可能不同，使用參數第一個英文字母排序
        ksort($queryParams);
        //利用http_bulid_query($queryParams)將查詢方法轉變為字串
        $queryString = http_build_query($queryParams);
        //組合成完整網址
        $fullUrl = "{$url}{$queryString}";

        //檢查是否有快取紀錄
        if (Cache::has($fullUrl)) {
            //使用return 直接回傳快取資料不做其他程式邏輯
            return Cache::get($fullUrl);
        }


        $limit = $request->limit ?? 10;

        $query = Animal::query(); //建立查詢建構器，分段撰寫SQL語句

        //設定欄位查詢條件
        if (isset($request->filters)) {
            $filters = explode(',', $request->filters);
            foreach ($filters as $key => $filter) {
                list($key, $value) = explode(':', $filter);
                $query->where($key, 'like', "%value%");
            }
        }
        //設定排序條件
        if (isset($request->sort)) {
            $sorts = explode(",", $request->sorts);
            foreach ($sorts as $key => $sort) {
                list($key, $value) = explode(":", $sort);
                if ($value == 'asc' || $value == 'desc') {
                    $query->orderBy($key, $value);
                }
            }
        } else {
            $query->orderBy('id', 'desc');
        }


        $animals = $query->paginate($limit) //使用分頁功能，使用後資料會自動被DATA包起來
            ->appends($request->query()); //回傳參數到URL＄後面方便使用
        return Cache::remember($fullUrl, 60, function () use ($animals) { //記住快取60秒
            return response($animals, Response::HTTP_OK);
        });
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [ //表單驗證
            'type_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'birthday' => 'nullable|date',
            'area' => 'nullable|string|max:255',
            'fix' => 'required|boolean',
            'description' => 'nullable',
            'personality' => 'nullable',
        ]);

        $request['user_id']=1;//先暫時這樣寫之後加入辯證功能修改

        $animal = Animal::create($request->all());
        $animal = $animal->refresh();
        return response($animal, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Animal $animal)
    {
        return response($animal, Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Animal $animal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Animal $animal)
    {
        $animal->update($request->all());
        return response($animal, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Animal $animal)
    {
        $animal->delete();
        return response(null, 204);
    }
}
