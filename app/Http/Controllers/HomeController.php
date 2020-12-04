<?php

namespace App\Http\Controllers;

use App\Model\listmodule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Hash;
use Auth;

class HomeController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	/*public function __construct()
	{
		$this->middleware('auth');
	}*/

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getIndex()
	{
		return view('home');
	}

	public function getLogin()
	{
		if (Auth::check()) {
			return redirect()->route('get-index');
		}
		return view('admin.login');
	}

	public function postLogin(Request $request)
	{
		$username = $request->username;
		$password = $request->password;

		if (Auth::attempt(['username' => $username, 'password' => $password])) {
			return redirect()->route('get-index');
		} else {
			return redirect()->back()->with('mess_error', 'Sai tài khoản hoặc mật khẩu!');
		}
	}

	public function logout()
	{
		Auth::logout();
		return redirect()->route('get-login');
	}

	public function getThaydoithongtin()
	{
		if (!Auth::check()) {
			return redirect()->route('get-login');
		}
		return view('admin.thaydoithongtin');
	}

	public function postThaydoithongtin(Request $request)
	{
		$name = $request->name;
		$checkbox = $request->checkdoimatkhau;

		if ($checkbox == 'check') {
			$this->validate($request,
				[
					'name' => 'required',
					'matkhaumoi' => 'required'
				],
				[
					'name.required' => 'Vui lòng nhập Tên hiển thị',
					'matkhaumoi.required' => 'Vui lòng nhập Mật khẩu mới'
				]);
		} else {
			$this->validate($request,
				[
					'name' => 'required',
				],
				[
					'name.required' => 'Vui lòng nhập Tên hiển thị',
				]);
		}

		$user_id = Auth::user()->id;
		$obj_user = User::find($user_id);

		if ($checkbox == 'check') {
			$matkhaucu = Auth::user()->password;
			if (Hash::check($request->matkhaucu, $matkhaucu)) {
				$obj_user->password = Hash::make($request->matkhaumoi);
			} else {
				return redirect()->back()->with('mess_error', 'Mật khẩu cũ sai!');
			}
		}

		$obj_user->name = $name;
		$obj_user->save();

		return redirect()->back()->with('mess_succ', 'Thay đổi thông tin thành công!');
	}

	public function getDanhsachuser()
	{
		$user = new User();
		$listUser = $user->getListuser()->get()->paginate(50);

		return view('admin.danhsachuser', compact('listUser'));
	}

	public function getThemmoiuser()
	{
		return view('admin.themmoiuser');
	}

	public function postThemmoiuser(Request $request)
	{
		$this->validate($request,
			[
				'name' => 'required',
				'username' => 'required|unique:user',
				'matkhau' => 'required'
			],
			[
				'name.required' => 'Vui lòng nhập Tên hiển thị',
				'username.required' => 'Vui lòng nhập Tên đăng nhập',
				'matkhau.required' => 'Vui lòng nhập Mật khẩu mới',
				'username.unique' => 'Username đã tồn tại',
			]);

		$user = new User();
		$user->name = $request->name;
		$user->username = $request->username;
		$user->password = Hash::make($request->matkhau);
		$user->save();

		return redirect()->route('get-danhsachuser')->with('mess_succ', 'Thêm user thành công!');
	}

	public function getXoauser($id)
	{
		$user = new User();
		$xoauser = $user->xoaUser($id);

		return redirect()->back()->with('mess_succ', 'Xoá user thành công!');
	}

	public function getResetpassword($id)
	{
		$newpass = Hash::make('123456');
		User::where('id', $id)->update(['password' => $newpass]);

		return redirect()->back()->with('mess_succ', 'Reset mật khẩu thành công');
	}

	public function getPhanquyenuser($id)
	{
		$user = new User();
		$user_obj = $user->getUser($id);
		$permission = json_decode($user_obj->permission, true);
		if (empty($permission)) {
			$permission = [];
		}
		$module = new listmodule();
		$listModule = $module->getListmodule()->get();

		return view('admin.phanquyenuser', compact('id', 'listModule', 'permission'));
	}

	public function postPhanquyenuser(Request $request)
	{
		$id = $request->id;
		$checkname = $request->checkname;
		$permission = json_encode($checkname);

		$user = new User();
		$user_obj = $user->getUser($id);
		$user_obj->permission = $permission;
		$user_obj->save();

		return redirect()->back()->with('mess_succ', 'Phân quyền thành công!');
	}

	public function getKhongcoquyen()
	{
		if (!Auth::check()) {
			return redirect()->route('get-login');
		}
		return view('khongcoquyen');
	}

}
