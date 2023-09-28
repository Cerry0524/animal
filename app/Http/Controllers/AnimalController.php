<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AnimalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->limit ?? 10;

        $query = Animal::query();//建立查詢建構器，分段撰寫SQL語句

        //設定欄位查詢條件
        if(isset($request->filters)){
            $filters = explode(',',$request->filters);
            foreach ($filters as $key => $filter) {
                list($key,$value)=explode(':',$filter);
                $query->where($key,'like',"%value%");
            }
        }
        //設定排序條件
        if(isset($request->sort)){
            $sorts=explode(",",$request->sorts);
            foreach ($sorts as $key => $sort) {
                list($key,$value)=explode(":",$sort);
                if($value=='asc' || $value=='desc'){
                    $query->orderBy($key,$value);
                }
            }
            }else{
                $query->orderBy('id', 'desc');
        }


        $animals = $query->paginate($limit) //使用分頁功能，使用後資料會自動被DATA包起來
        ->appends($request->query());//回傳參數到URL＄後面方便使用
        return response($animals, Response::HTTP_OK);
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
