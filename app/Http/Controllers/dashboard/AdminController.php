<?php

namespace App\Http\Controllers\dashboard;

use Illuminate\Http\Request;
use App\Models\dashboard\Role;
use App\Models\dashboard\Admin;
use App\Http\Traits\Message_Trait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    use Message_Trait;

    public function index()
    {
        $admins = Admin::all();
        return view('dashboard.admins.index', compact('admins'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'password' => 'required',
                'password_confirmation' => 'required|same:password',
                'role_id' => 'required',
            ];
            $messages = [
                'name.required' => 'من فضلك ادخل اسم المستخدم ',
                'email.required' => 'من فضلك ادخل البريد الالكتروني ',
                'email.email' => 'من فضلك ادخل بريد الكتروني صحيح ',
                'phone.required' => 'من فضلك ادخل رقم الهاتف ',
                'password.required' => 'من فضلك ادخل كلمة المرور ',
                'password_confirmation.required' => 'من فضلك ادخل تاكيد كلمة المرور ',
                'password_confirmation.same' => 'كلمة المرور غير متطابقة ',
                'role_id.required' => 'من فضلك ادخل صلاحيات المستخدم ',
            ];
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $admin = new Admin();
            $admin->name = $data['name'];
            $admin->email = $data['email'];
            $admin->phone = $data['phone'];
            $admin->password = Hash::make($data['password']);
            $admin->role_id = $data['role_id'];
            $admin->status = 1;
            $admin->type = 'admin';
            $admin->save();
            return $this->success_message('تم اضافة المستخدم بنجاح');
        }
        $roles = Role::all();
        return view('dashboard.admins.create', compact('roles'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'role_id' => 'required',
            ];
            $messages = [
                'name.required' => 'من فضلك ادخل اسم المستخدم ',
                'email.required' => 'من فضلك ادخل البريد الالكتروني ',
                'email.email' => 'من فضلك ادخل بريد الكتروني صحيح ',
                'phone.required' => 'من فضلك ادخل رقم الهاتف ',
                'role_id.required' => 'من فضلك ادخل صلاحيات المستخدم ',
            ];

            if (isset($data['password']) && $data['password'] != null) {
                $rules = [
                    'password' => 'required',
                    'password_confirmation' => 'required|same:password',
                ];
                $messages = [
                    'password.required' => 'من فضلك ادخل كلمة المرور ',
                    'password_confirmation.required' => 'من فضلك ادخل تاكيد كلمة المرور ',
                    'password_confirmation.same' => 'كلمة المرور غير متطابقة ',
                ];

                $admin->password = Hash::make($data['password']);
                $admin->save();
            }
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $admin->name = $data['name'];
            $admin->email = $data['email'];
            $admin->phone = $data['phone'];
            $admin->role_id = $data['role_id'];
            $admin->save();
            return $this->success_message('تم تعديل المستخدم بنجاح');
        }
        $admin = Admin::find($id);
        $roles = Role::all();
        return view('dashboard.admins.update', compact('admin', 'roles'));
    }

    public function destroy($id)
    {

        $admin = Admin::find($id);
        if ($admin->id == 1) {
            return $this->Error_message('لا يمكن حذف المستخدم المدير');
        }
        $admin->delete();
        return $this->success_message('تم حذف المستخدم بنجاح');
    }
}