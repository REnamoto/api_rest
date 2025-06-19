<?php

namespace App\Http\Controllers;

use App\Models\Clients;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @OA\Get(
     *     path="/api/clients",
     *     tags={"Clients"},
     *     summary="List all clients",
     *     @OA\Response(
     *         response=200,
     *         description="List all clients"
     *     )
     * )
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Clients::all();
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @OA\POST(
     *     path="/api/clients",
     *     tags={"Clients"},
     *     summary="Create a new client",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="João da Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@email.com"),
     *             @OA\Property(property="phone", type="string", example="11999999999")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string',
        ]);

        $client = Clients::create($validated);

        return response()->json($client, 201);
    }

    /**
     * Display the specified resource.
     * @OA\GET(
     *     path="/api/clients/{id}",
     *     tags={"Clients"},
     *     summary="Show the client by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the client to retrieve",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
     *     )
     * )
     * 
     * @param  \App\Models\Clients  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Clients $client)
    {
        return response()->json($client);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Clients  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Clients $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * 
     * @OA\Put(
     *     path="/api/clients/{id}",
     *     tags={"Clients"},
     *     summary="Update the client",
     *     description="Update client by ID. You can send only the fields you want to update.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the client to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="João Atualizado"),
     *             @OA\Property(property="email", type="string", format="email", example="joao.atualizado@email.com"),
     *             @OA\Property(property="phone", type="string", example="12987654321")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Clients  $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Clients $client)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:clients,email,' . $client->id,
            'phone' => 'sometimes|nullable|string',
        ]);

        $client->update($validated);

        return response()->json($client);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @OA\Delete(
     *     path="/api/clients/{id}",
     *     tags={"Clients"},
     *     summary="Delete the client",
     *     description="Deletes a client by their ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the client to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Client deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
     *     )
     * )
     * 
     * @param  \App\Models\Clients  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clients $client)
    {
        //Delete client
        $client->delete();

        return response()->json(null, 204);
    }
}
