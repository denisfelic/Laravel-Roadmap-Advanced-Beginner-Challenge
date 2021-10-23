<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Gate::allows('access-clients')) {
            abort(403);
        }

        $clients = Client::all();
        return view('client-page', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Gate::allows('access-clients', Auth::user())) {
            abort(403);
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
        if (!Gate::allows('access-clients', Auth::user())) {
            abort(403);
        }

        $request->validate([
            'company_name' => 'required|string',
            'VAT' => 'required|string',
            'address' => 'required|string',
        ]);

        $createdClientData = Client::create($request->only('company_name', 'VAT', 'address'))
            ->toArray();

        return response($createdClientData, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        if (!Gate::allows('access-clients', Auth::user())) {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        if (!Gate::allows('access-clients', Auth::user())) {
            abort(403);
        }

        $request->validate([
            "company_name" => 'required|string',
            "VAT" => 'required|string',
            "address" => 'required|string',
        ]);

        $client->company_name = $request->company_name;
        $client->VAT = $request->VAT;
        $client->address = $request->address;

        $client->save();
        return response($client->toArray(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {

        if (!Gate::allows('access-clients', Auth::user())) {
            abort(403);
        }

        $clientData = $client->toArray();
        $client->delete();
        return response($clientData, 200);
    }
}
