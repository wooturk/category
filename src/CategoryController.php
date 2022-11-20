<?php

namespace Wooturk;
use App\Http\Controllers\Controller;
use Google\Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
	function index(Request $request){
		return Response::success("Lütfen Giriş Yapınız");
	}
	function list(Request $request){
		if($rows = get_categories( $request->all() )){
			return Response::success("Kategori Bilgileri", $rows);
		}
		return Response::failure("Kategori Bulunamdı");
	}
	function get(Request $request, $id){
		if($brand = get_category($id)){
			return Response::success("Kategori Bilgileri", $brand);
		}
		return Response::failure("Kategori Bulunamdı");
	}
	function post(Request $request) {
		$exception = '';
		try {
			$fields = $request->validate([
				'name'       => 'required|string|max:255',
				'code'       => 'required|string|max:32|unique:categories',
				'slug'       => 'required|string|unique:categories',
				'sort_order' => 'required|integer',
				'status'     => 'required|boolean',
				'parent_id'  => 'required|integer',
				'is_last'    => 'required|boolean',
				'is_home'    => 'required|boolean',
				'parent_id_code'    => 'string'
			]);

			$row = create_category($fields);
			$this->re_create_path($row);
			if($row){
				return Response::success("Kategori Oluşturuldu", $row);
			}
			return Response::failure("Kategori Oluşturulamadı");
		} catch(\Illuminate\Database\QueryException $ex){
			$exception = $ex->getMessage();
		} catch (Exception $ex){
			$exception = $ex->getMessage();
		}
		return Response::exception( $exception);
	}
	function put(Request $request, $id){
		$exception = '';
		try {
			$fields = $request->validate([
				'name'       => 'required|string|max:255',
				'code'       => 'required|string|max:32|unique:categories',
				'rate'       => 'required|integer',
				'sort_order' => 'required|integer',
				'status'     => 'required|boolean',
				'slug'       => 'required|string|unique:categories'
			]);
			$row = update_category($id, $fields);
			$this->re_create_path($row);
			if($row){
				return Response::success("Kategori Güncellendi", $row);
			}
			return Response::failure("Kategori Güncellenemedi");
		} catch(\Illuminate\Database\QueryException $ex){
			$exception = $ex->getMessage();
		} catch (Exception $ex){
			$exception = $ex->getMessage();
		}
		return Response::exception( '$exception');
	}
	function delete(Request $request, $id){
		$exception = '';
		try {
			$brand = Brand::where('id', $id)->get()->first();
			if($brand){
				Brand::destroy($id);
				BrandDescription::where('brand_id', $id)->delete();
				return Response::success( "Kategori silindi", $brand);
			} else {
				return Response::failure("Kategori Bulunamadı");
			}
			return BrandResponse::failure("Kategori Silinemedi");
		} catch(\Illuminate\Database\QueryException $ex){
			$exception = $ex->getMessage();
		} catch (Exception $ex){
			$exception = $ex->getMessage();
		}
		return Response::exception( $exception );
	}
	private function re_create_path($row){
		if(empty($row['parent_id'])){
			$path = $row['id'];
		} else{
			$result = $this->get_category_parents($row['parent_id']);
			$result[] = $row['id'];
			$path = implode('_', $result);
		}
		return Category::where('id', $row['id'])->update(['path'=>$path]);
	}
	private function get_category_parents($id){
		$result[] =  $id;
		$row = get_category($id);
		if($row){
			if(!empty($row['parent_id'])){
				$sub_result = $this->get_category_parents($row['parent_id']);
				$result = array_merge($sub_result, $result);
			}
		}
		return $result;
	}
}
