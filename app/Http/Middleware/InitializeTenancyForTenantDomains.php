<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedOnDomainException;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyForTenantDomains
{
    public function __construct(protected DomainTenantResolver $resolver) {}

    /**
     * Initialize tenancy before the router instantiates controllers so early
     * dependency resolution (e.g. Fortify's StatefulGuard + session) uses the
     * tenant connection instead of the central one.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->getHost(), config('tenancy.central_domains'), true)) {
            return $next($request);
        }

        try {
            $tenant = $this->resolver->resolve($request->getHost());
        } catch (TenantCouldNotBeIdentifiedOnDomainException) {
            return $next($request);
        }

        tenancy()->initialize($tenant);

        return $next($request);
    }
}
