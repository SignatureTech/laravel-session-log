<?php

namespace SignatureTech\LaravelSessionLog;

use DateTimeInterface;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Laravel\Sanctum\NewAccessToken;
use SignatureTech\LaravelSessionLog\Models\PersonalAccessToken;

trait SessionLog
{

    /**
     * Create a new personal access token for the user.
     *
     * @param  string  $name
     * @param  array  $abilities
     * @param  \DateTimeInterface|null  $expiresAt
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public function createLoginToken(string $name, array $abilities = ['*'], DateTimeInterface $expiresAt = null): NewAccessToken
    {
        $token = new PersonalAccessToken([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(480)),
            'abilities' => $abilities,
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'expires_at' => $expiresAt,
        ]);

        $token = $this->tokens()->save($token);

        return new NewAccessToken($token, $plainTextToken);
    }

    /**
     * Get the current sessions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    public function sessions()
    {
        return collect(
            $this->tokens()->orderByDesc('id')->get()
        )->map(function ($token) {
            $agent = $this->createAgent($token->user_agent);

            return (object) [
                'id' => $token->id,
                'agent' => [
                    'is_desktop' => $agent->isDesktop(),
                    'is_mobile' => $agent->isMobile(),
                    'is_tablet' => $agent->isTablet(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                ],
                'ip_address' => $token->ip_address,
                'is_current_device' => $token->id === $this->currentAccessToken()->id,
                'last_active' => $token->last_used_at,
            ];
        });
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param  mixed  $session
     * @return \Jenssegers\Agent\Agent
     */
    protected function createAgent($userAgent)
    {
        return tap(new Agent, function ($agent) use ($userAgent) {
            $agent->setUserAgent($userAgent);
        });
    }

    public function logoutOtherDevices()
    {
        return $this->tokens()->whereNot('id', $this->currentAccessToken()->id)->delete();
    }

    public function logoutDevice(int $id)
    {
        return $this->tokens()->where('id', $id)->delete();
    }
}
