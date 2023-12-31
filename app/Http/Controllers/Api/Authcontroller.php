<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Authenticate user and generate JWT token",
     *     tags={"Authentication"},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="email", example="mante.alicia@example.net"),
     *               @OA\Property(property="password", type="password", example="password")
     *            ),
     *        ),
     *    ),
     *     @OA\Response(response="200", description="token",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="object", example="{'token':'15|qw0rsNAs8gn3wUplKsaUBVZhPLUWi4ooRrtZCFVgb65c2164'}")
     *         )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity. The provided credentials are incorrect.",
     *          @OA\JsonContent()
     *     ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages(
                [
                    "email" => ['The provided credentials are incorrect.'],
                ]
            );
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(
                [
                    "email" => ['The provided credentials are incorrect.'],
                ]
            );
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Terminate current user login session & logout",
     *     tags={"Authentication"},
     *     @OA\Response(response="200", description="Logged out successfully"),
     *     @OA\Response(response="401", description="Unauthenticated."),
     *     @OA\Response(response="404", description="Error: Not Found. The route events could not be found."),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * @OA\Get(
     *    path="/user",
     *    operationId="getUserDetails",
     *    tags={"Authentication"},
     *    summary="Get an User Details",
     *    description="Get an User Details",
     *    security={{"bearerAuth":{}}},
     *     @OA\Response(response="401", description="Unauthenticated."),
     *     @OA\Response(response="404", description="Error: Not Found. The route events could not be found."),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 format="object",
     *                 example={
     *                  "id": "517",
     *                  "name": "Darius Lubowitz",
     *                  "email": "mante.alicia@example.net",
     *                },
     *                 @OA\Items(
     *                      @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example="191"
     *                      ),
     *                      @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Darius Lubowitz"
     *                      ),
     *                      @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="mante.alicia@example.net"
     *                      ),
     *                 )
     *             )
     *          )
     *     )
     *       )
     *  )
     */
    public function user(Request $request)
    {
        return new UserResource($request->user());
    }
}
