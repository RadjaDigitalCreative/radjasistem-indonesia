<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Exports\KasExport;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class Kas extends Controller
{
	public function export()
	{
		return Excel::download(new KasExport, 'kas.xlsx');
	}
	public function index(Request $request)
	{
		$data = DB::table('orders')
		->join('users', 'users.id', '=', 'orders.created_by')  
		->select(
			'orders.id',
			'orders.table_number',
			'orders.discount',
			'orders.total',
			'orders.keperluan',
			'orders.image',
			'orders.payment_id',
			'users.name',
			'orders.lokasi',
			'orders.created_at',
		)
		->where('orders.id_team', $request->id_team)
		->get();
		return response()->json([
			'kas' => $data,
			'status_code'   => 200,
			'msg'           => 'success',
		], 200);

	}
	public function edit($id)
	{
		$data = DB::table('orders')
		->join('users', 'users.id', '=', 'orders.created_by')  
		->select(
			'orders.id',
			'orders.table_number',
			'orders.discount',
			'orders.total',
			'orders.keperluan',
			'orders.image',
			'orders.payment_id',
			'users.name',
			'orders.lokasi',
		)
		->where('orders.id', $id)
		->get();
		$false = DB::table('orders')
		->where('id', '!=', $id)
		->get();
		if ($data) {
			return response()->json([
				'kas' => $data,
				'status_code'   => 200,
				'msg'           => 'success',
			], 200);
		}
	}
	public function uang_keluar(Request $request)
	{
		$image = $request->file('image');
		$new_name = rand() . '.' . $image->getClientOriginalExtension();
		$image->move(public_path('images'), $new_name);
		$data = DB::table('orders')
		->insert([
			'name'               =>         $request->name,
			'table_number'               =>         1,
			'total'               =>            $total,
			'payment_id'               =>         $request->payment_id,
			'created_by'               =>         $request->created_by,
			'note'               =>         $request->note,
			'keperluan'               =>         $request->keperluan,
			'lokasi'               =>         $request->lokasi,
            'id_team' => $request->id_team,
			'image'               =>        $new_name
		]);
		return response()->json([
			'input uang keluar' => $data,
			'status_code'   => 200,
			'msg'           => 'success',
		], 201);
	}

	public function uang_masuk(Request $request)
	{
		$image = $request->file('image');
		$new_name = rand() . '.' . $image->getClientOriginalExtension();
		$image->move(public_path('images'), $new_name);
		$data = DB::table('orders')
		->insert([
			'name'               =>         $request->name,
			'table_number'               =>         1,
			'total'               =>            $total,
			'payment_id'               =>         $request->payment_id,
			'created_by'               =>         $request->created_by,
			'note'               =>         $request->note,
			'keperluan'               =>         $request->keperluan,
			'lokasi'               =>         $request->lokasi,
            'id_team' => $request->id_team,
			'image'               =>        $new_name
		]);
		return response()->json([
			'input uang masuk' => $data,
			'status_code'   => 200,
			'msg'           => 'success',
		], 201);
	}
	public function delete(Request $request, $id)
	{
		$data = DB::table('kas')
		->where('id', $id)
		->delete();
		return response()->json([
			'Uang Kas' => 'Data Berhasil Dihapus',
			'status_code'   => 200,
			'msg'           => 'success',
		], 200);

	}
	public function invoice($id)
	{
		$data = DB::table('orders')
		->join('users', function($join){
			$join->on('users.id', '=', 'orders.created_by');
		})
		->where('orders.id', $id)
		
		->join('payments', function($join){
			$join->on('payments.id', '=', 'orders.payment_id');
		})
		->select('orders.id' ,'orders.note', 'orders.table_number', 'orders.discount AS bayar', 'orders.total as total semua', 'orders.keperluan'
			,'payments.name as payment', 'orders.name', 'orders.lokasi', 'orders.notelp','users.name AS kasir')
		
		->get();
		$data2 = DB::table('order_details')
		->where('order_id', $id)
		->get();
		$data3 = DB::table('orders')
		->join('users', function($join){
			$join->on('users.id', '=', 'orders.created_by');
		})
		->join('payments', function($join){
			$join->on('payments.id', '=', 'orders.payment_id');
		})
		->where('orders.id', $id)
		->get();


		$false = DB::table('orders')
		->where('id', '!=', $id)
		->get();
		if ($data != NULL) {
			return response()->json([
				'invoice kas' => $data,
				'detail kas'  => $data2,
				'status_code'   => 200,
				'msg'           => 'success',
			], 200);

		}else{
			return response()->json([
				'invoice kas' => $data3,
				'status_code'   => 200,
				'msg'           => 'success',
			], 200);
		}	
	}
}
