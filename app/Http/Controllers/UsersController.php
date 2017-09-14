<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Dingo\Api\Routing\Helpers;
use App\Transformers\UserTransformer;
use Authorizer;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use GuzzleHttp\Client;
use App\Entities\Tercero;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;


class UsersController extends Controller
{
    use Helpers, AuthenticatesAndRegistersUsers, ThrottlesLogins;

    protected $username = 'usuario';

    public function access(Request $request)
    {
        $client = new Client();

        try {
            $res = $client->request('post', 'http://localhost/api/oauth/access_token', [
                'json' => [
                    "grant_type" => "password",
                    "client_id" => $request['client_id'],
                    "client_secret" => $request['client_secret'],
                    "username" => $request['username'],
                    "password" => $request['password']
                ]
            ]);
        } catch (RequestException $e) {
            return redirect()->back()->with([
                'error' => 'Usuario o contraseña incorrecto, por favor verifique sus datos.'
            ]);
        }

        $code = $res->getStatusCode();
        $results = json_decode($res->getBody(), true);

        if ($code == 200) {

            $access_token = $results['access_token'];
            $token_type = $results['token_type'];
            $expires_in = $results['expires_in'];

            $request = $client->request('get', 'http://localhost/api/oauth/access/login', [
                'headers' => [
                    "Authorization" => $token_type . " " . $access_token,
                ]
            ]);

            $code_final = $request->getStatusCode();

            if ($code_final == 200) {
                return 'sirve';
            }
        }
    }

    public function login()
    {
        return view('api.index');
    }

    public function verify($username, $password)
    {
        $credentials = [
            'email'    => $username,
            'password' => $password,
        ];

        if (Auth::once($credentials)) {
            return Auth::user()->id;
        }

        return false;
    }

    public function authorization()
    {
        return $this->response->array(Authorizer::issueAccessToken());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $users = User::paginate(25);

        return $this->response->paginator($users, new UserTransformer);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
