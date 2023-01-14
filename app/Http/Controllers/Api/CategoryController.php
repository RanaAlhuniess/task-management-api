<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->respond(CategoryResource::collection(Category::latest()->get()));
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category  $category)
    {
        return $this->respond(new CategoryResource($category));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validators = Validator::make($request->all(), [
            'name' => 'required|unique:categories',
            'color' => 'required|string|max:255',
        ]);
        if ($validators->fails()) {
            return $this->respondError($validators->errors(), 422);
        }

        try {
            $category = new Category();
            $category->name = $request->name;
            $category->color = $request->color;
            $category->save();
            return $this->respond(new CategoryResource($category), 201);
        } catch (Exception $e) {
            $message = 'Oops! Unable to create a new Category.';
            return $this->respondError($message, 500);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Category   $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category  $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', Rule::unique('categories')->ignore($category->id)],
            'color' => 'required|string|max:255',
        ]);
        if ($validator->fails())
            return $this->respondError($validator->errors(), 422);
        try {
            $category->name = $request->name;
            $category->color = $request->color;
            $category->save();
            return $this->respond(new CategoryResource($category));
        } catch (Exception $e) {
            $message = 'Oops! Failed to update the Category.';
            return $this->respondError($message, 500);
        }
    }
     /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->respond(null, 204);
    }
}
