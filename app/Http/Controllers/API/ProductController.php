<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\Delivered;
use App\Models\SellerDelivered;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json([
            'status'=> 200,
            'products'=>$products,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image'=>'required|image|mimes:jpeg,png,jpg|max:2048',
            'category'=>'required|max:191',
            'name'=>'required|max:191',
            'description'=>'required|max:191',
            'price'=>'required|max:191',
            'quantity'=>'required|max:191',

        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=> 422,
                'validate_err'=> $validator->messages(),
            ]);
        }
        else
        {
            $product = new Product;

            if($request->hasFile('image'))
            {
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $file->move('uploads/product/',$filename);
                $product->image = 'uploads/product/'.$filename;
            }

            $product->user_id = $request->input('user_id');
            $product->category = $request->input('category');
            $product->name = $request->input('name');
            $product->seller_name = $request->input('seller_name');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->quantity = $request->input('quantity');
            $product->save();

            return response()->json([
                'status'=> 200,
                'message'=>'Product Added Successfully',
            ]);
        }

    }

    public function edit($id)
    {
        $product = Product::find($id);
        if($product)
        {
            return response()->json([
                'status'=> 200,
                'product' => $product,
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No Product ID Found',
            ]);
        }

    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'category'=>'required|max:191',
            'name'=>'required|max:191',
            'description'=>'required|max:191',
            'price'=>'required|max:191',
            'quantity'=>'required|max:191',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=> 422,
                'validationErrors'=> $validator->messages(),
            ]);
        }
        else
        {
            $product = Product::find($id);
            if($product)
            {   
                $product->user_id = $request->input('user_id');
                $product->category = $request->input('category');
                $product->name = $request->input('name');
                $product->description = $request->input('description');
                $product->price = $request->input('price');
                $product->quantity = $request->input('quantity');
                $product->update();

                return response()->json([
                    'status'=> 200,
                    'message'=>'Product Updated Successfully',
                ]);
            }
            else
            {
                return response()->json([
                    'status'=> 404,
                    'message' => 'No Product ID Found',
                ]);
            }
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if($product)
        {
            $product->delete();
            return response()->json([
                'status'=> 200,
                'message'=>'Product Deleted Successfully',
            ]);
        }
        else
        {
            return response()->json([
                'status'=> 404,
                'message' => 'No Product ID Found',
            ]);
        }
    }
    
    public function getUserProducts(Request $request, $id)
    {
        $products = Product::where('user_id', '=', $id)->get();

        return $products;
    }

    public function vegetable()
    {
        $products = Product::where('category', 'Like', 'Vegetables')->get();
        return response()->json([
            'status'=> 200,
            'products'=>$products,
        ]);
        
        
    }
    public function fruit()
    {
        $products = Product::where('category', 'Like', 'Fruits')->get();
        return response()->json([
            'status'=> 200,
            'products'=>$products,
        ]);
    }

    public function search($key)
    {
        return Product::where('name', 'Like', "%$key%")->get();
    }

    public function viewfruit($product_id)
    {
        $review = Review::where('product_id', $product_id)->get();
        return response()->json([
            'status'=> 200,
            'reviews' =>$review,
        ]);
    }

    public function viewvegetable($product_id)
    {
        $review = Review::where('product_id', $product_id)->get();
        return response()->json([
            'status'=> 200,
            'reviews' =>$review,
        ]);
    }

    
    public function viewproductrecommendation($product_id)
    {
        $review = Review::where('product_id', $product_id)->get();
        return response()->json([
            'status'=> 200,
            'reviews' =>$review,
        ]);
    }

    public function recentSold($user_id)
    {
        $recent = SellerDelivered::where('seller_id', $user_id)->orderBy('updated_at', 'desc')->get();
        return response()->json([
            'status'=>200,
            'sellerdelivered'=>$recent,
        ]);
    }

    /*public function dataVisualization()
    {
        $Cabbage = Review::where('order_name', 'Like', 'Cabbage' )->get();
        $Carrots = Review::where('order_name', 'Like', 'Carrots' )->get();
        $Kangkong = Review::where('order_name', 'Like', 'Kangkong')->get();
        $Pechay = Review::where('order_name', 'Like', 'Pechay')->get();
        $Mustasa = Review::where('order_name', 'Like', 'Mustasa')->get();
        $Sayote = Review::where('order_name', 'Like', 'Sayote')->get();
        $Malunggay = Review::where('order_name', 'Like', 'Malunggay')->get();
        $Patola = Review::where('order_name', 'Like', 'Patola')->get();

        $CabbageCount = $Cabbage->count();
        $CarrotsCount = $Carrots->count();
        $KangkongCount = $Kangkong->count();
        $PechayCount = $Pechay->count();
        $MustasaCount = $Mustasa->count();
        $SayoteCount = $Sayote->count();
        $MalunggayCount = $Malunggay->count();
        $PatolaCount = $Patola->count();

        $data = [
            $CabbageCount,
            $CarrotsCount,
            $KangkongCount,
            $PechayCount,
            $MustasaCount,
            $SayoteCount,
            $MalunggayCount,
            $PatolaCount,
        ]; 

        return response()->json([
            'status'=>200,
            'data'=> $data,
        ]);

    }*/

    public function recommended()
    {
        /*$data = Delivered::where('id', 'created_at')->get()->groupBy(function($data)
        {
            return Carbon::parse($data->created_at)->format('M');
            return response()->json([
                'delivered'=> $data,
            ]);
        });

        $data = Review::table('reviews')
             ->select(Review::raw('count(*) as user_count, created_at'))
             ->where('created_at', '<>', 1)
             ->groupBy('created_at')
             ->get();   
        
        echo $data;*/
        
        $data = SellerDelivered::select('product_id', 'order_price','order_name','order_qty',)->groupBy('product_id', 'order_price','order_name','order_qty')->orderBy('order_qty', 'desc')->get();
        return response()->json([
            'status'=>200,
            'data'=> $data,       
        ]);

    }


    public function visualization($id)
    {
        $data = SellerDelivered::select('order_qty')->where('seller_id', $id)->get();

        return response()->json([
            'status'=>200,
            'data'=> $data,
        ]);
    }

    /*public function visualization()
    {
        $data = Delivered::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();
        $datas = Delivered::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->subMonth(1))->count();
        $datass = Delivered::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->subMonth(2))->count();
        $datasss = Delivered::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->subMonth(3))->count();

        $productCount = array($data, $datas, $datass, $datasss);

        return response()->json([
            'status'=>200,
            'data'=> $productCount,
        ]);
    }*/
    public function myproducts(Request $request, $id) 
    {
        $products = Product::where('user_id', $id)->get();

        return response()->json([
            'status' => 200,
            'products' => $products,
        ]);
    }

    public function sample($id)
    { 
        $data = SellerDelivered::where('seller_id', $id)->get();
        $products = SellerDelivered::where('seller_id', $id)->select('order_name')->groupBy('order_name')->get();
        $qty = SellerDelivered::where('seller_id', $id)->selectRaw('product_id,SUM(order_qty) as total_qty')->groupBy('product_id')->get();
        $total = $data->sum->order_total;

        return response()->json([
            'status' => 200,
            'price' => $total,
            'products'=>$products,
            'qty'=>$qty

        ]);

    }

    public function orderMonthCount($id)
    {
        $orderCount = SellerDelivered::whereMonth('created_at', Carbon::now()->month)
        ->where('seller_id', $id)
        ->get();

        return response()->json([
            'status'=> 200,
            'orderCount'=> $orderCount->count()
        ]);
    }
}