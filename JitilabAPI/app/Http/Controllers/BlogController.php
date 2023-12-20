<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class BlogController extends Controller
{
    public function getAllData()
    {
        $MasterBlog = Blog::get();

        return json_decode($MasterBlog);
    }
    public function getDataById($id)
    {
        try {
            $blog = Blog::where('Id', $id)->get()->first();
            if (empty($blog)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Not Found',
                ], 404);
            }
            return json_decode($blog);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function InputData(Request $request)
    {
        try {
            $validateModel = validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'image' => 'required|file|mimes:jpg,png,webp',

                ]
            );
            if ($validateModel->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateModel->errors()
                ], 401);
            }

            //Save file ke folder Storage/public
            $filename = $this->UploadImage($request->file('image'));
            // End Save

            $blog = Blog::create([
                'uuid' => Str::uuid(),
                'title' => $request->title,
                'description' => $request->description,
                'image' => $filename,
                'created_at' => date("Y-m-d H:i:s")
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Blog Created Succesfully',
                'token' => $blog->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function UpdateData(Request $request)
    {
        try {
            $validateModel = validator::make(
                $request->all(),
                [
                    'image' => 'file|mimes:jpg,png,webp',
                ]
            );
            if ($validateModel->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateModel->errors()
                ], 401);
            }

            if (empty($request->image)) {
                Blog::where('Id', $request->id)->update([
                    'uuid' => Str::uuid(),
                    'title' => $request->title,
                    'description' => $request->description,
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
            } else {
                //Get Old Data
                $blog = Blog::where('Id', $request->id)->get()->first();

                //Delete Old Image If Updated
                $this->DeleteImage($blog->image);

                //Upload New Image If Updated
                $filename = $this->UploadImage($request->image);

                Blog::where('Id', $request->id)->update([
                    'uuid' => Str::uuid(),
                    'title' => $request->title,
                    'description' => $request->description,
                    'image' => $filename,
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => $request->title,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function Delete($id)
    {
        try {

            //Get Data
            $getData = Blog::where('Id', $id)->get()->first();


            //Check If Data Found
            if (empty($getData)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data Not Found',
                ], 404);
            }

            //Delete Data
            $data = Blog::where('Id', $id)->delete();



            //Delete Image 
            $this->DeleteImage($getData->image);


            return response()->json([
                'status' => true,
                'message' => 'Blog Delete Succesfully',

            ], 200);

            //end Delete Data
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    public function UploadImage($image)
    {
        // Upload Image into Storage
        $file = $image;
        $filename = date("Ymd His") . " " . rand(10, 100) . "." . $file->getClientOriginalExtension();;
        Storage::disk('public')->put($filename, file_get_contents($file));
        return $filename;
    }
    public function DeleteImage($image)
    {
        // Delete Image from Storage
        Storage::disk('public')->delete($image);
        return 'Ok';
    }
}
