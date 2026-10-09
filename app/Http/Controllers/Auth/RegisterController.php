<?php

namespace App\Http\Controllers\Auth;

use \Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Empresa;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use illuminate\Support\Facades\DB;
use  Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {


        //$user=User::paginate(2);   

        $user = User::
            select(
                'id',
                'name',
                'username',
                'email',
                'especialidad',
                'estado'

            )->orderBy('users.id', 'desc')->paginate(50);
        return [
            'pagination' => [
                'total' => $user->total(),
                'current_page' => $user->currentPage(),
                'per_page' => $user->perPage(),
                'last_page' => $user->lastPage(),
                'from' => $user->firstItem(),
                'last_page' => $user->lastPage(),
                'to' => $user->lastPage(),
            ],
            'user' => $user,
        ];
    }
    public function consulta(Request $request, $id)
    {
        $input = $request->all();
        // $user=User::findOrFail($id); 
        $user = User::where('id', '=', $id)
            ->select(
                'id',
                'name',
                'username',
                'especialidad',
                'estado'
            )->get();
        return [
            'user' => $user
        ];

        //return response()->json($user);

    }

    public function create(Request $request)
    {
    }

    public function edit($id, Request $request)
    {


        $user = User::find($id);

        return [
            'user' => $user
        ];
    }
    public function show()
    {

        return redirect('/home');
    }


    public function store(Request $request)
    {
        $this->validate(request(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:1', 'confirmed'],
        ]);
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $users = User::create($input);


        return response()->json();
    }

    public function update(Request $request, $id)
    {
        $this->validate(request(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],

        ]);
        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }
        $user = User::findOrFail($id);
        $user->name = request('name');
        $user->email = request('email');
        $user->especialidad = request('especialidad');
        $user->username = request('username');
        $user->estado = request('estado');
        $user->save();


        return ['user' => $user];
    }
    public function password(Request $request, $id)
    {


        if (Hash::check($request->mypassword, Auth::user()->password)) {
            $users = new User;
            $users->where('username', '=', Auth::user()->username)
                ->update(['password' => bcrypt($request->password)]);

            return response()->json('si');
        } else {
            return response()->json('no');
        }
    }
}
