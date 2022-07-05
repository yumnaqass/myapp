<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Driver;
use App\Models\Admin;
use Dotenv\Parser\Value;
use Dotenv\Validator as DotenvValidator;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class RegisterdriverController extends BaseController
{
public function register(Request $request , $admin_id)
{
    $admin = Admin::where('id', $admin_id)->first();
    $drivers=Driver::with(['admin'])->where('admin_id',$admin_id);
$rules =$this->getRulles();
$validator=validator($request->all(),$rules);
if ($validator->fails())
{
return $this ->sendError('validator error',$validator->errors());
}
$request['password']=Hash::make($request['password']);
$input=$request->all();
$user= new Driver ;
$user->name = $request->name;
$user->email = $request->email;
$user->password = $request->password;
$user->admin_id = $admin_id;
$user->save();
$token = $user->createToken('homam')->plainTextToken;
$success['token']=$token;
// $success['token']=$user->createToken('homam')->accessToken;
// $success['token']=$user->name;
return $this->sendResponse( $success,'User Register successfully');
}
public function login(Request $request)
{
$validator = validator($request->all(),[
'email'=>'required|email|unique:_drivers,email',
'password' =>'required',
] );
if ($validator->failed()){
return $this ->sendError('validator error',$validator->errors());
}
$user = Driver::where('email',$request->email) -> first();
if ($user){
if(Hash::check($request->password , $user->password)){
$success['token'] = $user->createToken('homam')->plainTextToken;
return $this->sendResponse( $success,'User login successfully');
} else {
return $this->sendError('Check your input', ['error' => 'Password mismatch']);
}
}
else {

return $this->sendError('Check your input', ['error' => 'User does not exist']);
}
}
public function logout(Request $request)
{
$token = $request->user()->token();
$token->revoke();
return response()->json(['message'=>'thank you for using our app came back later']);
}
public function getRulles()
{
return $rules =[
'name'=>'required|unique:_drivers,name',
'email'=>'required|email|unique:_drivers,email',
'password'=>'required',
];
}
}
