<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCompanyAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user || !$user->company_id) {
            return redirect()->route('login')->with('error', 'Access denied. No company associated.');
        }

        // Check if company is active
        if (!$user->company->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Company account is suspended.');
        }

        // For paid plans, check subscription expiry
        if ($user->company->plan === 'paid' && 
            $user->company->subscription_expires_at && 
            $user->company->subscription_expires_at->isPast()) {
            
            // Downgrade to free plan
            $user->company->update([
                'plan' => 'free',
                'max_users' => 5,
                'max_storage_mb' => 100,
                'subscription_expires_at' => null,
            ]);
            
            return redirect()->route('dashboard')->with('warning', 'Subscription expired. Downgraded to free plan.');
        }

        return $next($request);
    }
}