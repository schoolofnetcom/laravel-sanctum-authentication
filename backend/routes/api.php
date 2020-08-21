<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $token_can_all = $user->createToken('can_all')->plainTextToken;
    $token_can_update = $user->createToken('can_update', ['system:update'])->plainTextToken;
    $token_can_create = $user->createToken('can_create', ['system:create'])->plainTextToken;

    $abilities = [$token_can_all, $token_can_update, $token_can_create];
    return $abilities;
});

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('users', function(Request $request){
        return User::all();
    });

    Route::get('user', function(Request $request){
        return $request->user();
    });

    Route::get('list_tokens', function(Request $request){
        return $request->user()->tokens;
    });

    Route::get('token_abilities', function(Request $request){

        $abilities = [];

        if ($request->user()->tokenCan('system:update')) {
            array_push($abilities, 'posso atualizar');
        }

        if ($request->user()->tokenCan('system:create')) {
            array_push($abilities, 'posso criar');
        }

        if ($request->user()->tokenCan('system:deletar')) {
            array_push($abilities, 'posso deletar porque eu posso tudo');
        }

        return $abilities;
    });

    Route::delete('revoke_all_tokens', function(Request $request){
        // Revoke all tokens...
        $request->user()->tokens()->delete();
        return response()->json([], 204);
    });

    Route::delete('revoke_current_token', function(Request $request){
        // Revoke the user's current token...
        $request->user()->currentAccessToken()->delete();
        return response()->json([], 204);
    });

    Route::delete('revoke_specific_token', function(Request $request){
        // Revoke a specific token...
        $request->user()->tokens()->where('id', 6)->delete();
        return response()->json([], 204);
    });

});
