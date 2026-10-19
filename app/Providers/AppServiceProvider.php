<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-users', fn (User $user): bool => $user->isAdmin());

        $this->configureRateLimiting();
    }

    /**
     * Define the named API rate limiters used by the throttle middleware.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request): Limit {
            return Limit::perMinute($this->attempts('api'))
                ->by($this->limiterKey($request));
        });

        RateLimiter::for('auth', function (Request $request): Limit {
            $email = Str::transliterate(Str::lower($request->string('email')->toString()));

            return Limit::perMinute($this->attempts('auth'))
                ->by($email.'|'.$this->limiterKey($request, byIp: true));
        });

        RateLimiter::for('sensitive', function (Request $request): Limit {
            return Limit::perMinute($this->attempts('sensitive'))
                ->by($this->limiterKey($request));
        });
    }

    private function attempts(string $limiter): int
    {
        return (int) config("rate-limiting.{$limiter}");
    }

    private function limiterKey(Request $request, bool $byIp = false): string
    {
        if ($byIp || $request->user() === null) {
            return (string) $request->ip();
        }

        return (string) $request->user()->id;
    }
}
