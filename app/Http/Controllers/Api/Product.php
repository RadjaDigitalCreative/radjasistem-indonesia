<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Models\Products;
use App\Http\Controllers\Api\Models\ProductImports;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Api\Exports\ProductExport;
use App\Jobs\ImportJob;
use DB;
use Response;


class Product extends Controller
{
	public function contoh_product()
	{
		$file= public_path(). "/format_produk_excel.xlsx";
		$headers = array(
			'Content-Type: application/xlsx',
		);
		return Response::download($file, 'format_produk_excel.xlsx', $headers);
	}
	
	public function export()
	{
		return Excel::download(new ProductExport, 'product.xlsx');
	}
	public function import(Request $request)
	{
		$data = Excel::import(new ProductImports($request->id), request()->file('file'));
		return response()->json([
			'products' => 'Sukses Import Data ',
			'status_code'   => 200,
			'msg'           => 'success',
		], 200);
	}
	public function record($id)
	{
		$data = DB::table('terjual')
		->join('products', function($join){
			$join->on('products.name', '=', 'terjual.name');
		})
		->select(
			'products.id',
			'products.name',
			'products.lokasi',
			'terjual.keperluan',
			'terjual.terjual',
			'terjual.created_at',
		)
		->where('products.id', $id)
		->get();
		return response()->json([
			'products' => $data,
			'status_code'   => 200,
			'msg'           => 'success',
		], 200);
	}
	public function index(Request $request)
	{
		$id = $request->id_team;
		$data = Products::with(array('grosir' => function ($query){
    	return DB::table('harga_grosir')
		->leftJoin('products', function($join){
			$join->on('harga_grosir.products_id', '=', 'products.id');
		})
		->select(
			'harga_grosir.id',
			'harga_grosir.qty',
			'harga_grosir.harga'
		)
    		->get();
        }))
        ->where('id_team', $id)
		->where('name', '!=', 'NAMA PRODUK')
        ->get();
		
		return response()->json([
			
			'products' =>  $data,
			'status_code'   => 200,
			'msg'           => 'success',
		], 200);

	}
public function edit($id)
{
	
    $data = Products::with(array('grosir' => function ($query){
    	return DB::table('harga_grosir')
		->leftJoin('products', function($join){
			$join->on('harga_grosir.products_id', '=', 'products.id');
		})
		->select(
			'harga_grosir.id',
			'harga_grosir.qty',
			'harga_grosir.harga'
		)
		->get();
    }))
    ->where('id', $id)
    ->get();
	return response()->json([
		
		'products' => [
			'data' => $data,
		],
		
		'status_code'   => 200,
		'msg'           => 'success',
	], 200);
}

	public function create(Request $request)
	{
		if($request->file('image') == NULL){
			$data = DB::table('products')
			->insert([
				'name' => $request->name,
				'lokasi' => $request->nama_cabang,
				'price' => $request->price,
				'purchase_price' => $request->purchase_price,
				'status' => $request->status,
				'merk' => $request->merk,
				'stock' => $request->stock,
				'satuan' => $request->satuan,
				'stock_minim' => $request->stock_minim,
				'id_team' => $request->id_team,
				'created_at' => now(), 
				'updated_at' => now(), 

			]);
			$id = DB::getPdo()->lastInsertId();

			DB::table('terjual')
			->insert([
				'product_id' => $id,
				'terjual' => $request->stock,
				'name' => $request->name,
				'cabang' => $request->nama_cabang,
				'keperluan' => 'Stock Awal',
				'created_at' => now(),
				'updated_at' => now(),
			]);
			if($request->qty != NULL){
				$count  = count($request->qty);

				for ($i=0; $i < $count; $i++) { 
					DB::table('harga_grosir')
					->insert([
						'products_id'  => $id,
						'qty'  => $request->qty[$i],
						'harga' => $request->harga[$i],
					]);
				}
			}

			return response()->json([
				'products' => $data,
				'status_code'   => 200,
				'msg'           => 'success',
			], 201);
		}else{
			$image = $request->file('image');
			$new_name = rand() . '.' . $image->getClientOriginalExtension();
			$image->move(public_path('images'), $new_name);

			$data = DB::table('products')
			->insert([
				'name' => $request->name,
				'lokasi' => $request->nama_cabang,
				'price' => $request->price,
				'purchase_price' => $request->purchase_price,
				'status' => $request->status,
				'merk' => $request->merk,
				'stock' => $request->stock,
				'satuan' => $request->satuan,
				'stock_minim' => $request->stock_minim,
				'image' => $new_name,
				'id_team' => $request->id_team,
				'created_at' => now(), 
				'updated_at' => now(), 

			]);
			$id = DB::getPdo()->lastInsertId();

			DB::table('terjual')
			->insert([
				'product_id' => $id,
				'terjual' => $request->stock,
				'name' => $request->name,
				'cabang' => $request->nama_cabang,
				'keperluan' => 'Stock Awal',
				'created_at' => now(),
				'updated_at' => now(),
			]);
			if($request->qty != NULL){
				$count  = count($request->qty);

				for ($i=0; $i < $count; $i++) { 
					DB::table('harga_grosir')
					->insert([
						'products_id'  => $id,
						'qty'  => $request->qty[$i],
						'harga' => $request->harga[$i],
					]);
				}
			}

			return response()->json([
				'products' => $data,
				'status_code'   => 200,
				'msg'           => 'success',
			], 201);
		}
		
	}
public function update(Request $request, $id)
{
	if($request->file('image') == NULL){
		$data = DB::table('products')
		->where('id', $id)
		->update([
			'name' => $request->name,
			'lokasi' => $request->nama_cabang,
			'price' => $request->price,
			'purchase_price' => $request->purchase_price,
			'status' => $request->status,
			'merk' => $request->merk,
			'stock' => $request->stock,
			'satuan' => $request->satuan,
			'stock_minim' => $request->stock_minim,
			'created_at' => now(), 
			'updated_at' => now(), 

		]);
		$grosir = DB::table('harga_grosir')
		->where('products_id', $id)
		->first();

		if($request->id_grosir == NULL){
			$count  = count($request->qty);
			for ($i=0; $i < $count; $i++) { 
				DB::table('harga_grosir')
				->insert([
					'products_id'  => $id,
					'qty'  => $request->qty[$i],
					'harga' => $request->harga[$i],
				]);
			}
		}else{
			$count  = count($request->id_grosir);
			for ($i=0; $i < $count; $i++) { 
				DB::table('harga_grosir')
				->where('id', $request->id_grosir[$i])
				->update([
					'qty'  => $request->qty[$i],
					'harga' => $request->harga[$i],
				]);
			}
		}

		return response()->json([
			'products' => $data,
			'status_code'   => 200,
			'msg'           => 'success',
		], 201);
	}else{
		$image = $request->file('image');
		$new_name = rand() . '.' . $image->getClientOriginalExtension();
		$image->move(public_path('images'), $new_name);

		$data = DB::table('products')
		->where('id', $id)
		->update([
			'name' => $request->name,
			'lokasi' => $request->nama_cabang,
			'price' => $request->price,
			'purchase_price' => $request->purchase_price,
			'status' => $request->status,
			'merk' => $request->merk,
			'stock' => $request->stock,
			'satuan' => $request->satuan,
			'stock_minim' => $request->stock_minim,
			'image' => $new_name,
			'created_at' => now(), 
			'updated_at' => now(), 

		]);
		$grosir = DB::table('harga_grosir')
		->where('products_id', $id)
		->first();
		if($request->id_grosir == NULL){
			$count  = count($request->qty);
			for ($i=0; $i < $count; $i++) { 
				DB::table('harga_grosir')
				->insert([
					'products_id'  => $id,
					'qty'  => $request->qty[$i],
					'harga' => $request->harga[$i],
				]);
			}
		}else{
			$count  = count($request->id_grosir);
			for ($i=0; $i < $count; $i++) { 
				DB::table('harga_grosir')
				->where('id', $request->id_grosir[$i])
				->update([
					'qty'  => $request->qty[$i],
					'harga' => $request->harga[$i],
				]);
			}
		}
		return response()->json([
			'products' => $data,
			'status_code'   => 200,
			'msg'           => 'success',
		], 201);
	}

}
public function update_no_grosir(Request $request, $id)
{
	if($request->file('image') == NULL){
		$data = DB::table('products')
		->where('id', $id)
		->update([
			'name' => $request->name,
			'lokasi' => $request->nama_cabang,
			'price' => $request->price,
			'purchase_price' => $request->purchase_price,
			'status' => $request->status,
			'merk' => $request->merk,
			'stock' => $request->stock,
			'satuan' => $request->satuan,
			'stock_minim' => $request->stock_minim,
			'created_at' => now(), 
			'updated_at' => now(), 
		]);
		
		return response()->json([
			'products' => $data,
			'status_code'   => 200,
			'msg'           => 'success',
		], 201);
	}else{
		$image = $request->file('image');
		$new_name = rand() . '.' . $image->getClientOriginalExtension();
		$image->move(public_path('images'), $new_name);

		$data = DB::table('products')
		->where('id', $id)
		->update([
			'name' => $request->name,
			'lokasi' => $request->nama_cabang,
			'price' => $request->price,
			'purchase_price' => $request->purchase_price,
			'status' => $request->status,
			'merk' => $request->merk,
			'stock' => $request->stock,
			'satuan' => $request->satuan,
			'stock_minim' => $request->stock_minim,
			'image' => $new_name,
			'created_at' => now(), 
			'updated_at' => now(), 

		]);
	
		return response()->json([
			'products' => $data,
			'status_code'   => 200,
			'msg'           => 'success',
		], 201);
	}

}
public function update_add_grosir(Request $request, $id)
{
    	$count  = count($request->qty);
			for ($i=0; $i < $count; $i++) { 
				$data =DB::table('harga_grosir')
				->insert([
					'products_id'  => $id,
					'qty'  => $request->qty[$i],
					'harga' => $request->harga[$i],
				]);
			}
			return response()->json([
				'add_grosir' => $data,
				'status_code'   => 200,
				'msg'           => 'success',
			], 201);
}
public function update_delete_grosir($id)
{
    $data = DB::table('harga_grosir')->where('id', $id)->delete();
    if($data == TRUE){
        return response()->json([
			'products' => 'Data Berhasil Dihapus',
			'status_code'   => 200,
			'msg'           => 'success',
		], 200);
    }else{
         return response()->json([
			'products' => 'Data Tidak Ditemukan',
			'status_code'   => 202,
			'msg'           => 'success',
		], 202);
    }
    	
}
	public function delete(Request $request, $id)
	{
		$data = DB::table('products')
		->where('id', $id)
		->delete();
		DB::table('harga_grosir')
		->where('products_id', '=', $id)
		->delete();
		DB::table('terjual')
		->where('product_id', '=', $id)
		->delete();
		return response()->json([
			'products' => 'Data Berhasil Dihapus',
			'status_code'   => 200,
			'msg'           => 'success',
		], 200);

	}
}
