<?php

namespace App\Infrastructure\Laravel\Middleware;

use App\Infrastructure\Laravel\Models\Account;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\Client;
use Lcobucci\JWT\Configuration;

class GetUserFromToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $request->setUserResolver(function () {
            $clientId = Configuration::forUnsecuredSigner()
                ->parser()
                ->parse(request()->bearerToken())
                ->claims()
                ->get('aud');
            $account = Client::find($clientId)->first();
            return Account::find($account->user_id);
        });

        return $next($request);
    }
}
