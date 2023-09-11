<?php

namespace App\Http\Controllers;


use App\Helpers\FileHelpers;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;




class usersController extends Controller
{
    //

    private $user;

    public function __construct(User $user)

    {
        $this->user = $user;
    }

    public function index()
    {
        $users = DB::table('users')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->join('users_status', 'users.status_id', '=', 'users_status.id')
            ->select('users.*', 'departments.name as departments', 'users_status.name as status')
            ->get();

        return response()->json($users);
    }

    public function create()
    {

        $departments = DB::table('departments')->select('id as value', 'name as label')
            ->get();
        $users_status = DB::table('users_status')->select('id as value', 'name as label')->get();

        // dd($users_status);
        return response()->json([
            'departments' => $departments,
            'users_status' => $users_status
        ]);
    }





    public function store(Request $request)
    {


        $validated = $request->validate([
            'status_id' => 'required',
            'username' => 'required | unique:users,username',
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'department_id' => 'required',

        ], [
            'status_id.required' => 'Vui lòng chọn trạng thái',
            'username.required' => 'Vui lòng nhập tên tài khoản',
            'username.unique' => 'Đã tồn tại tài khoản',

            'name.required' => 'Vui lòng nhập tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Vui lòng nhập đúng định dạng email',

            'password.required' => 'Nhập pass',
            'password_confirmation.required' => 'Nhập lại mật khẩu',
            'password.confirmed' => 'Mật khẩu không khớp',
            'department_id.required' => 'Vui lòng chọn phòng ban',
        ]);


        DB::beginTransaction();

        try {
            $this->user->username = $request->username;
            $this->user->name = $request->name;
            $this->user->email  = $request->email;
            $this->user->password = Hash::make($request->password);
            $this->user->department_id  = $request->department_id;
            $this->user->status_id  = $request->status_id;
            $this->user->remember_token = $request->session()->token();
            $this->user->save();

            if ($request->imageUrl) {
                FileHelpers::uploadFile($request->imageUrl, "User", $this->user->id, User::class, "Avatar", 2);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = $this->user::find($id);
        $imagePath = $this->user->find($id)->image;
        // dd($imagePath);

        $departments = DB::table('departments')->select('id as value', 'name as label')->get();
        $users_status = DB::table('users_status')->select('id as value', 'name as label')->get();


        return response()->json([
            'user' => $user,
            'imagepath' => $imagePath,
            'departments' => $departments,
            'users_status' => $users_status
        ]);
    }


    public function update(Request $request, $id)
    {


        $validated = $request->validate([
            'status_id' => 'required',
            'username' => 'required | unique:users,username,' . $id,
            'name' => 'required',
            'email' => 'required|email',
            'department_id' => 'required',

        ], [
            'status_id.required' => 'Vui lòng chọn trạng thái',
            'username.required' => 'Vui lòng nhập tên tài khoản',
            'username.unique' => 'Đã tồn tại tài khoản',
            'name.required' => 'Vui lòng nhập tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Vui lòng nhập đúng định dạng email',
            'department_id.required' => 'Vui lòng chọn phòng ban',
        ]);

        if ($request['change_password'] == true) {
            $validated = $request->validate([

                'password' => 'required|confirmed',
                'password_confirmation' => 'required',

            ], [
                'password.required' => 'Nhập pass',
                'password.confirmed' => 'Mật khẩu không khớp',
            ]);
        }

        DB::beginTransaction();

        try {

            $object = $this->user::findOrFail($id);
            $object->username = $request->username;
            $object->name = $request->name;
            $object->email  = $request->email;
            // $this->user->password = Hash::make($request->password);
            $object->department_id  = $request->department_id;
            $object->status_id  = $request->status_id;


            if ($request->imageUrl) {
                if ($object->image) {
                    FileHelpers::forceDeleteFiles($object->image->id, $object->id, User::class, 'Avatar');
                }
                FileHelpers::uploadFile($request->imageUrl, 'User', $object->id, User::class, 'Avatar', 2);
            }

            if ($request['change_password'] == true) {
                $object->password = Hash::make($request->password);
                $object->change_password_at = now();
            }

            $object->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function deletel($id)
    {
        $user = $this->user->findOrFail($id);


        if (!$user->canDelete()) {

            return response()->json([
                'code' => 0,
                "message" => "Không thể xóa!!"
            ]);
        } else {

            $user->delete();
        }
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Đéo được'
            ], 444);
        }

        $user = Auth::user();
        $token = $user->createToken('token')->plainTextToken;
        $role = $user->department_id;
        $cookie = cookie('jwt', $token, 60 * 24); //1day
        return response([
            'token' => $token,
            'role' => $role,
            'message' => 'đăng nhập thành công',
        ])->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $cookie = Cookie::forget('jwt');
        return response([
            'message' => 'success'
        ])->withCookie($cookie);
    }

    public function user()
    {
        return Auth::user();
    }


    public function search($key)
    {

        $result = $this->user->where('name', 'like', "$key%")->get();

        return response([
            'result' => $result,
        ]);
    }
}
