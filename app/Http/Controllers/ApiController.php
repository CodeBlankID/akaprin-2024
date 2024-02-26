<?php

namespace App\Http\Controllers;

use App\Models\AddressBook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Dokumentasi API",
 *      description="Lorem Ipsum",
 *      @OA\Contact(
 *          email="rifkyr.personal@gmail.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *  @OA\SecurityScheme(
 *      type="apiKey",
 *      in="header",
 *      name="Authorization",
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
 * 
 *
 */
class ApiController extends Controller
{
    /**
     *    @OA\Get(
     *       path="/api/showuser",
     *       tags={"Account"},
     *       operationId="show user",
     *       summary="Show Data Users",
     *       description="Get All Data User",
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent(),
     *      ),
     *     
     *  )
     */
    public function showUser()
    {
        $user = User::all();
        return response()->json($user, 200);
    }
    /**
     * @OA\Post(
     *     path="/api/createaccount",
     *     tags={"Account"},
     *     operationId="createaccount",
     *     summary="Create Account & Generate Token For Getting Address Book Data",
     *     description="Please Make sure you save or remember the token after the token is generated ",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                      type="string||email"
     *                 ),
     *              @OA\Property(
     *                     property="password",
     *                      type="string"
     *                 ),
     *                 example={"name": "Testing", "email": "Testing@testing.com", "password": "testing"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *           response="200",
     *           description="Ok",
     *   
     *        )
     *     )
     * )
     */
    public function createaccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required|string'
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        if ($user)
        {
            $token = $user->createToken('auth_token')->plainTextToken;
            $user->setRememberToken($token);
            $user->save();
            $result = [
                "status" => "Ok",
                "data" => $user,
                "message" => "Register Successfully",
                "token" => "Bearer " . $token
            ];
        }
        else
        {
            $result = [
                 "status" => "Failed",
                 "data" => $user,
                 "message" => "Register Failed",
                 "token" => null
             ];
        }
        return response()->json($result);
    }
    /**
     *    @OA\Delete(
     *       path="/api/deleteaccount",
     *       tags={"Account"},
     *       operationId="deleteaccount",
     *       summary="Delete Account",
     *       description="Delete Data User",
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Delete Account User",
     *         required=true,
     *      ),
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent({})
     *      ),
     *     
     *  )
     */
    public function deleteAccount(Request $req)
    {
        $user = User::where('id', $req->id)->first();
        if (!empty($user))
            $user?->tokens()->delete();
        $user = User::destroy($req->id);
        if ($user)
        {
            $result = [
                "status" => "Ok",
                "message" => "Delete Account Successfully",
            ];
        }
        else
        {
            $result = [
                 "status" => "Failed",
                 "message" => "Delete Account Failed",
             ];
        }


        return response()->json($result);
    }

    /**
     *    @OA\Post(
     *       path="/api/store",
     *       security={{"bearer_token": {} }},
     *       tags={"Address Book"},
     *       operationId="store adressbook",
     *       summary="Store Address Book",
     *       description="Insert Data Address Book",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                      type="string||email"
     *                 ),
     *              @OA\Property(
     *                     property="phone_number",
     *                      type="int"
     *                 ),
     *              @OA\Property(
     *                     property="address",
     *                      type="string"
     *                 ),
     *                 example={"name": "Testing", "email": "Testing@testing.com", "phone_number": "testing","address": "testing"}
     *             )
     *         )
     *      ),
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent()
     *      ),
     *     
     *  )
     */
    public function store(Request $request)
    {
        $user = AddressBook::create($request->all());

        return response()->json($this->show()->original, 200);
    }
    /**
     *    @OA\Get(
     *       path="/api/show",
     *       security={{"bearer_token": {} }},
     *       tags={"Address Book"},
     *       operationId="show adressbook",
     *       summary="Show Address Book",
     *       description="Get All Data Address Book",
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent
     *           (example={
     *                {
     *                   {
     *                   "id": "1",
     *                   "name": "Testing",
     *                   "email": "testing@testing.com",
     *                   "phone_number": "234456678",
     *                   "address": "Testing Street No 14",
     *                  }
     *              }
     *          }),
     *      ),
     *     
     *  )
     */
    public function show()
    {
        $ad = AddressBook::all();
        return response()->json($ad, 200);
    }
    /**
     *    @OA\Put(
     *       path="/api/update",
     *       security={{"bearer_token": {} }},
     *       tags={"Address Book"},
     *       operationId="update adressbook",
     *       summary="Update Address Book",
     *       description="Update Data Address Book",
     *       @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Add Id Address Book",
     *         required=true,
     *      ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                      type="string||email"
     *                 ),
     *              @OA\Property(
     *                     property="phone_number",
     *                      type="int"
     *                 ),
     *              @OA\Property(
     *                     property="address",
     *                      type="string"
     *                 ),
     *                 example={"name": "Testing", "email": "Testing@testing.com", "phone_number": "testing","address": "testing"}
     *             )
     *         )
     *      ),
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent()
     *      ),
     *     
     *  )
     */
    public function update(Request $request)
    {
        AddressBook::where("id", $request->id)->update($request->all());
        return response()->json($this->show()->original, 200);
    }

    /**
     *    @OA\Delete(
     *       path="/api/delete",
     *       tags={"Address Book"},
     *       security={{"bearer_token": {} }},
     *       operationId="delete addressbook",
     *       summary="Delete Address Book",
     *       description="Delete Data Address Books",
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Add Id Address Book",
     *         required=true,
     *      ),
     *       @OA\Response(
     *           response="200",
     *           description="Ok",
     *           @OA\JsonContent()
     *      ),
     *     
     *  )
     */
    public function destroy(request $req)
    {
        AddressBook::destroy($req->id);
        return response()->json($this->show()->original, 200);
    }
}
