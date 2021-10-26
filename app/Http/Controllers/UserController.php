<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Throwable;
use \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class UserController extends Controller
{

    public  UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        if (!Gate::allows('access-users', Auth::user())) {
            return redirect('/', 302);
        }
    }

    public function change_role(Request $request, User $user)
    {
        if (!Gate::allows('access-users', Auth::user())) {
            abort(403);
        }

        $request->validate([
            'role' => 'nullable|string',
        ]);

        try {
            $this->userService->changeRole($user, $request->role);
            return response(201);
        } catch (HttpExceptionInterface $th) {
            abort($th->getStatusCode());
        } catch (\Throwable $th) {
            dd($th->getMessage());
            abort(500, $th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();


        if (!Gate::allows('access-users', Auth::user())) {
            return redirect('/', 302);
        }
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        if (!Gate::allows('access-users', Auth::user())) {
            return redirect('/', 302);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if (!Gate::allows('access-users', $user)) {
            return redirect('/', 302);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // TODO: Refactor to use Policies
        if (!Gate::allows('access-users', Auth::user())) {
            abort(403);
        }

        try {
            $userData = $this->userService->deleteUser($user);
            return response($userData, 200);
        } catch (HttpExceptionInterface $th) {
            //dd($th, $th->getCode());
            abort($th->getStatusCode());
        } catch (Throwable $th) {
            abort(500);
        }
    }
}
