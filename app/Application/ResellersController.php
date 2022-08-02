<?php

namespace App\Application;

use App\Domain\Models\Account;
use App\Domain\Models\AccountType;
use App\Domain\Resources\ResellerResponse;
use App\Infrastructure\Laravel\Controller;
use App\Infrastructure\Laravel\Filters\ResellerFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResellersController extends Controller
{
    public function search(ResellerFilter $filter): JsonResponse
    {
        $user = request()->user();

        if ($user->type !== 'distributor') {
            return response()->json([
                'message' => 'Only distributors can list resellers.',
                'errors' => [
                    'forbidden' => 'Only distributors can list resellers.'
                ]
            ], 403);
        }

        return response()->json(ResellerResponse::collection(
            Account::filter($filter)
                ->where('type', '=', 'reseller')
                ->get()
        ));
    }

    public function create(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->type !== 'distributor') {
            return response()->json([
                'message' => 'Only distributors can create resellers.',
                'errors' => [
                    'forbidden' => 'Only distributors can create resellers.'
                ]
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:accounts'
        ]);

        $account = new Account();
        $account->type = AccountType::Reseller;
        $account->name = $request->get('name');
        $account->email = $request->get('email');
        $account->password = Hash::make('password');
        $account->save();

        $parent = Account::where('type', '=', 'distributor')->first();
        $account->parent()->associate($parent)->save();
        return response()->json(new ResellerResponse($account));
    }

    public function find(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        if ($user->type !== 'distributor' && $id != $user->id) {
            return response()->json([
                'message' => 'Only distributors can find resellers.',
                'errors' => [
                    'forbidden' => 'Only distributors can find resellers.'
                ]
            ], 403);
        }

        $reseller = Account::where('type', '=', 'reseller')
            ->where('id', '=', $id)
            ->first();

        if (!$reseller) {
            return response()->json([
                'message' => 'Reseller not found',
                'errors' => [
                    'reseller_not_found' => 'Reseller not found'
                ]
            ], 404);
        }

        return response()->json(
            new ResellerResponse($reseller)
        );
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();

        if ($user->type !== 'distributor') {
            return response()->json([
                'message' => 'Only distributors can update resellers.',
                'errors' => [
                    'forbidden' => 'Only distributors can update resellers.'
                ]
            ], 403);
        }

        $reseller = Account::where('type', '=', 'reseller')
            ->where('id', '=', $id)
            ->first();

        if (!$reseller) {
            return response()->json([
                'message' => 'Reseller not found',
                'errors' => [
                    "reseller_not_found" => 'Reseller not found'
                ]
            ], 404);
        }

        $reseller->update($request->all());

        return response()->json(new ResellerResponse($reseller));
    }
}
