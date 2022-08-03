<?php

namespace App\Application;

use App\Domain\Models\Account;
use App\Domain\Models\AccountType;
use App\Domain\Resources\CustomerResponse;
use App\Infrastructure\Laravel\Controller;
use App\Infrastructure\Laravel\Filters\CustomerFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomersController extends Controller
{
    public function search(CustomerFilter $filter): JsonResponse
    {
        /** @var Account $user */
        $user = $filter->request->user();
        Log::info("Starting list customers as {$user->type} {$user->id}");

        if (!$user->isReseller() && !$user->isDistributor()) {
            return response()->json([
                'message' => 'Only Resellers and Distributors can find a customer.',
                'errors' => [
                    'forbidden' => 'Only Resellers and Distributors can find a customer.'
                ]
            ], 403);
        }

        $queryFilter = Account::filter($filter)
            ->where('type', '=', 'customer')
            ->where('parent_id', '=', $user->id);

        if ($filter->request->get('parent')) {
            try {
                $parentId = $this->parentId($filter->request);
                $queryFilter->where('parent_id', '=', $parentId);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Invalid parent ID',
                    'errors' => [
                        'invalid_parent_id' => 'Invalid parent ID'
                    ]
                ]);
            }
        }

        return response()->json(
            CustomerResponse::collection($queryFilter->get())
        );
    }

    public function find(Request $request, $id): JsonResponse
    {
        /** @var Account $user */
        $user = $request->user();
        Log::info("Starting find customer as {$user->type} {$user->id}");

        if (!$user->isReseller() && !$user->isDistributor()) {
            return response()->json([
                'message' => 'Only Resellers and Distributors can find a customer.',
                'errors' => [
                    'forbidden' => 'Only Resellers and Distributors can find a customer.'
                ]
            ], 403);
        }

        $query = Account::where('type', '=', 'customer')
            ->where('id', '=', $id);

        if ($user->isReseller()) {
            $query->where('parent_id', '=', $user->id);
        }

        $customer = $query->first();

        if (!$customer) {
            return response()->json([
                'message' => "Customer {$id} not found",
                'errors' => [
                    'reseller_not_found' => "Customer {$id} not found"
                ]
            ], 404);
        }

        return response()->json(
            new CustomerResponse($customer)
        );
    }

    public function create(Request $request): JsonResponse
    {
        /** @var Account $user */
        $user = $request->user();
        Log::info("Starting customer creation as {$user->type} {$user->id}");

        if (!$user->isReseller() && !$user->isDistributor()) {
            return response()->json([
                'message' => 'Only Resellers and Distributors can create a customer.',
                'errors' => [
                    'forbidden' => 'Only Resellers and Distributors can create a customer.'
                ]
            ], 403);
        }

        $parentId = $request->get('parent') ? $request->get('parent') : $user->id;

        if ($request->get('parent')) {
            if ($user->isDistributor() && $parentId !== $user->id) {
                // Find a reseller under the distributor with the given parentId.
                $reseller = Account::where('type', '=', 'reseller')
                    ->where('id', '=', $parentId)
                    ->where('parent_id', '=', $user->id)
                    ->first();

                if (!$reseller) {
                    return response()->json([
                        'message' => 'Invalid parent account.',
                        'errors' => [
                            'forbidden' => 'Only Resellers and Distributors can create a customer.'
                        ]
                    ], 403);
                }
            }

            if ($user->type == 'reseller' && $parentId !== $user->id) {
                return response()->json([
                    'message' => 'Insufficient privileges. Can only create customers belonging to you.',
                    'errors' => [
                        'forbidden' => 'Insufficient privileges. Can only create customers belonging to you.'
                    ]
                ], 403);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:accounts'
        ]);

        $customer = new Account();
        $customer->type = AccountType::Customer;
        $customer->name = $request->get('name');
        $customer->email = $request->get('email');
        $customer->password = Hash::make('password');
        $customer->save();

        $parent = Account::where('id', '=', $parentId)->first();
        $customer->parent()->associate($parent)->save();

        return response()->json(new CustomerResponse($customer));
    }

    public function update(Request $request, $id): JsonResponse
    {
        /** @var Account $user */
        $user = $request->user();

        if (!$user->isDistributor() && !$user->isReseller()) {
            return response()->json([
                'message' => 'Only distributors or resellers can update customers.',
                'errors' => [
                    'forbidden' => 'Only distributors or resellers can update customers.'
                ]
            ], 403);
        }

        $customer = $user->children()->where('type', '=', 'customer')
            ->where('id', '=', $id)
            ->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found',
                'errors' => [
                    "reseller_not_found" => 'Customer not found'
                ]
            ], 404);
        }

        $customer->update($request->all());

        return response()->json(new CustomerResponse($customer));
    }

    /**
     * Get the parent ID to list customers, if no children with that parent id was found throw exception
     * (also if the same parentId as the user is given, just use it)
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    private function parentId(Request $request): string
    {
        $user = $request->user();

        if ($request->get('parent') == $user->id) {
            return $user->id;
        }

        $children = $user->children()
            ->where('id', '=', $request->get('parent'))
            ->first();

        if ($children) {
            return $children->id;
        }

        throw new \Exception('Invalid parent ID');
    }
}
