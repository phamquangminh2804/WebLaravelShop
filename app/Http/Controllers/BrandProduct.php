<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class BrandProduct extends Controller
{
    public function AuthLogin(){
        $admin_id = Session::get('admin_id');
        if($admin_id){
            return Redirect::to('dashboard');
        }else{
            return Redirect::to('admin')->send();
        }
    }
    public function add_brand_product(){
        $this->AuthLogin();
        return view('admin.brand_product.add_brand_product');
    }

    public function all_brand_product(){
        $this->AuthLogin();
        $all_brand_product = DB::table('tbl_brand_product')
            ->get();
        $manager_brand_product = view('admin.brand_product.all_brand_product')
            ->with('all_brand_product',$all_brand_product);
        return view('admin_layout')
            ->with('admin.brand_product.all_brand_product',$manager_brand_product);
        
    }
    public function save_brand_product(Request $request){
        $this->AuthLogin();
        $data = array();
        $data['brand_name'] = $request->brand_product_name;
        $data['brand_desc'] = $request->brand_product_desc;
        $data['slug_brand_name'] = $request->slug_brand_name;
        $data['meta_keywords'] = $request->brand_product_keywords;
        $data['brand_status'] = $request->brand_product_status;
        DB::table('tbl_brand_product')
            ->insert($data);
        Session::put('message','Thêm thương hiệu sản phẩm thành công');
        return Redirect::to('add-brand-product');
    }


    public function unactive_brand_product($brand_product_id){
        $this->AuthLogin();
        DB::table('tbl_brand_product')
            ->where('brand_id',$brand_product_id)
            ->update(['brand_status' => 1]);
        Session::put('message','Kích hoạt danh mục sản phẩm thành công');
        return Redirect::to('all-brand-product');
    }

    public function active_brand_product($brand_product_id){
        $this->AuthLogin();
        DB::table('tbl_brand_product')
            ->where('brand_id',$brand_product_id)
            ->update(['brand_status' => 0]);
        Session::put('message','Không kích hoạt danh mục sản phẩm thành công');
        return Redirect::to('all-brand-product');
    }

    public function edit_brand_product($brand_product_id){
        $this->AuthLogin();
        $edit_brand_product = DB::table('tbl_brand_product')
            ->where('brand_id',$brand_product_id)
            ->get();
        $manager_brand_product = view('admin.brand_product.edit_brand_product')
            ->with('edit_brand_product',$edit_brand_product);
        return view('admin_layout')
            ->with('admin.brand_product.edit_brand_product',$manager_brand_product);
        
    }

    public function update_brand_product(Request $request,$brand_product_id){
        $this->AuthLogin();
        $data = array();
        $data['brand_name'] = $request->brand_product_name;
        $data['brand_desc'] = $request->brand_product_desc;
        $data['meta_keywords'] = $request->brand_product_keywords;
        $data['slug_brand_name'] = $request->slug_brand_name;
        DB::table('tbl_brand_product')
            ->where('brand_id',$brand_product_id)
            ->update($data);
        Session::put('message','Cập nhật danh mục sản phẩm thành công');
        return Redirect::to('all-brand-product');
    }

    public function delete_brand_product($brand_product_id){
        $this->AuthLogin();
        DB::table('tbl_brand_product')
            ->where('brand_id',$brand_product_id)
            ->delete();
        Session::put('message','Xóa danh mục sản phẩm thành công');
        return Redirect::to('all-brand-product');
    }
//end function admin page
    public function show_brand_home(Request $request, $slug_brand_name){

        $brand_id = DB::table('tbl_brand_product')
            ->where('slug_brand_name', $slug_brand_name)
            ->pluck('brand_id');

        $category_product = DB::table('tbl_category_product')
            ->where('category_status','1')
            ->orderBy('category_id','desc')
            ->get();
        $brand_product = DB::table('tbl_brand_product')
            ->where('brand_status','1')
            ->orderBy('brand_id','desc')
            ->get();
        $brand_by_id = DB::table('tbl_product')
            ->join('tbl_brand_product','tbl_product.brand_id','=','tbl_brand_product.brand_id')
            ->where('tbl_product.brand_id',$brand_id)
            ->get();


        foreach($brand_by_id as $key => $value){
            $meta_desc = $value -> brand_desc;
            $meta_keywords = $value -> meta_keywords;
            $meta_title = $value -> brand_name;
            $url_canonical = $request->url();
        }
    
        $brand_name = DB::table('tbl_brand_product')
            ->where('tbl_brand_product.slug_brand_name',$slug_brand_name)
            ->limit(1)
            ->get();
        return view('pages.brand.show_brand')
            ->with('category',$category_product)
            ->with('brand',$brand_product)
            ->with('brand_by_id',$brand_by_id)
            ->with('brand_name',$brand_name)
            ->with('meta_desc',$meta_desc)
            ->with('meta_keywords',$meta_keywords)
            ->with('meta_title',$meta_title)
            ->with('url_canonical',$url_canonical);

    }
}
