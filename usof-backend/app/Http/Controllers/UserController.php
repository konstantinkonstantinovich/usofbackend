<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use  Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Ramsey\Uuid\Type\Integer;

class UserController extends Controller {

    public function forget_password(Request $request){
        $user = User::where('email', '=', $request->only(['email']));


        $token = Str::random(20);

        $user->update(['remember_token' => $token]);
        $user = $user->first();
        $data = [
            'email' => $user->email,
            'token' => $token
        ];

        Mail::send('mail', $data, function ($m) use ($user) {
            $m->subject('Password recovery!');
            $m->to($user->email);
        });
        return 1;

    }

    public function reset_password(Request $request, $token){
        $user = User::where('remember_token', '=', $token);
        $user->update([
            'remember_token' => NULL,
            'password' => Hash::make($request->all()['password'])
        ]);
    }

    public function list_users() {
            $users = DB::table('users')->get();
            echo $users;
            return $users;
    }

    public function user_data(Request $request, $id) {
        return User::where('id', '=', intval($id))->first();
    }

    public function user_create(Request $request) {
        if($this->is_staff()){
            if($request['confirmed_password'] == $request['password']) {
                return User::create($request->all());
            }
        }
        return 'Permission denied!';
    }

    public function user_update(Request $request, $id) {
        $user = auth()->user();
        if($this->is_staff()){
            $user = User::where('id', '=', intval($id))->first();
            $user->update($request->all());
            return $user;
        }
        else if($user) {
            $user->update($request->all());
            return $user;
        }
        return 'Permission denied!';
    }

    static public function is_staff() {
        $user = auth()->user();
        return $user['role'] ? True : False;
    }

    public function user_delete(Request $request, $id) {
        $user = auth()->user();
        if($this->is_staff()){
            $user = User::where('id', '=', intval($id))->first();
            $user->delete();
            return $user;
        }
        else if($user) {
            $user->delete();
            return 'User is deleted!';
        }
        return 'Permission denied!';
    }

}
