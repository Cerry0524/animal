<?php

namespace App\Http\Controllers;

use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use LDAP\Result;
use symfony\Component\HttpFoundation\Response;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $type = Type::get(); //全部輸出
        return response([
            'data' => $type // 輸出用data 包住
        ], Response::HTTP_OK);
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
        $this->validate($request, [
            //驗證法，使用陣列傳入驗證關鍵字
            'name' => [
                'required',
                'max:50',
                //typey 資料表中name欄位資料是唯一值
                Rule::unique('type', 'name')
            ],
            'sort' => 'nullable|integer',
        ]);
        // 如果沒有傳入sort 內容
        if (!isset($request->sort)) {
            //找到目前資料表的排序欄位最大值
            $max = Type::max('sort');
            $request['sort'] = $max + 1;
            //最大值+1寫入請求的資料中
        }
        $type = Type::create($request->all());//寫入資料庫
        return  response([
            'type'=> $type
        ],Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Type $type)
    {
        return response([
            'data'=>$type
        ],Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Type $type)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Type $type)
    {
        $this->validate($request,[
            'name'=>[
                'max:50',
                //更新時排除自己後檢查是否為唯一
                Rule::unique('types','name')->ignore($type->name,'name')
            ],
            'sort'=>'nullable|integer',
        ]);
        $type->update($request->all());
        return response([
            'data'=>$type
        ],Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Type $type)
    {
        $type->delete();
        return response(null,Response::HTTP_NO_CONTENT);
    }
}
