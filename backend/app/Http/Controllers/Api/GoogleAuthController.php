<?php

namespace App\Http\Controllers\Api;

use Google\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoogleAuthController extends Controller
{
    /**
     * Build a Google Client using config/services.php (env vars).
     * Falls back to credentials.json if env vars are not set.
     */
    private function buildClient(): Client
    {
        $client = new Client();

        $clientId     = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri  = config('services.google.redirect');

        if ($clientId && $clientSecret) {
            $client->setClientId($clientId);
            $client->setClientSecret($clientSecret);
            $client->setRedirectUri($redirectUri);
        } else {
            // Fallback to credentials.json
            $client->setAuthConfig(storage_path('app/google/credentials.json'));
        }

        $client->addScope('https://www.googleapis.com/auth/calendar');
        $client->setAccessType('offline');   // needed to get a refresh token
        $client->setPrompt('consent');        // force consent screen so refresh token is always issued

        $this->applySSLFix($client);

        return $client;
    }

    public function redirectToGoogle()
    {
        $client  = $this->buildClient();
        $authUrl = $client->createAuthUrl();

        return redirect()->away($authUrl);
    }

    public function handleCallback(Request $request)
    {
        if ($request->has('error')) {
            $errorMsg = $request->get('error_description', $request->get('error'));
            return response(
                '<html><body><script>
                    window.opener && window.opener.postMessage({ google_auth: "error", message: "' . addslashes($errorMsg) . '" }, "*");
                    window.close();
                </script><p>Authorization failed: ' . htmlspecialchars($errorMsg) . '. You can close this window.</p></body></html>',
                400
            )->header('Content-Type', 'text/html');
        }

        $client = $this->buildClient();
        $token  = $client->fetchAccessTokenWithAuthCode($request->code);

        if (isset($token['error'])) {
            $errorMsg = $token['error_description'] ?? $token['error'];
            return response(
                '<html><body><script>
                    window.opener && window.opener.postMessage({ google_auth: "error", message: "' . addslashes($errorMsg) . '" }, "*");
                    window.close();
                </script><p>Authorization failed. You can close this window.</p></body></html>',
                400
            )->header('Content-Type', 'text/html');
        }

        // Ensure the storage directory exists
        $dir = storage_path('app/google');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Save token securely
        file_put_contents(storage_path('app/google/token.json'), json_encode($token));

        return response(
            '<html><body><script>
                window.opener && window.opener.postMessage({ google_auth: "success" }, "*");
                window.close();
            </script><p>Google Calendar connected successfully! You can close this window.</p></body></html>'
        )->header('Content-Type', 'text/html');
    }

    /**
     * Apply SSL CA bundle to the Google Client's HTTP client.
     * Fixes cURL error 60 on Windows (missing local issuer certificate).
     */
    private function applySSLFix(Client $client): void
    {
        $caBundle = ini_get('curl.cainfo') ?: ini_get('openssl.cafile');
        if ($caBundle && file_exists($caBundle)) {
            $client->setHttpClient(new \GuzzleHttp\Client([
                'verify' => $caBundle,
            ]));
        }
    }
}
