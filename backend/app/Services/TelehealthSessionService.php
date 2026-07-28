<?php

namespace App\Services;
use Google\Client;
use Google\Service\Calendar;
use App\Models\Appointment;
use App\Models\TelehealthSession;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Services\NotificationService;
use Illuminate\Support\Str;


class TelehealthSessionService
{   private NotificationService $notificationService;
public function __construct(
    NotificationService $notificationService
) {
    $this->notificationService = $notificationService;
}
    /**
     * Create a telehealth session.
     *
     * @throws ValidationException
     */
    public function createSession(array $data): TelehealthSession
    {
        try {

            return DB::transaction(function () use ($data) {

                /*
                |--------------------------------------------------------------------------
                | Load Appointment
                |--------------------------------------------------------------------------
                */

                $appointment = Appointment::with([
                    'doctor.user',
                    'patient',
                    'hospital',
                    'telehealthSession',
                ])->findOrFail($data['appointment_id']);

                /*
                |--------------------------------------------------------------------------
                | Appointment must be approved
                |--------------------------------------------------------------------------
                */

                if (!in_array($appointment->status, ['confirmed', 'pending'])) {

                    throw ValidationException::withMessages([
                        'appointment' => [
                            'Only confirmed or pending appointments can have a telehealth session.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Must be a telehealth appointment
                |--------------------------------------------------------------------------
                */

                if (! $appointment->is_telehealth) {

                    throw ValidationException::withMessages([
                        'appointment' => [
                            'This appointment is not a telehealth appointment.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent duplicate session
                |--------------------------------------------------------------------------
                */

                if ($appointment->telehealthSession) {

                    throw ValidationException::withMessages([
                        'appointment' => [
                            'A telehealth session already exists for this appointment.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Verify doctor ownership
                |--------------------------------------------------------------------------
                */

                if ($appointment->doctor_id !== $data['doctor_id']) {

                    throw ValidationException::withMessages([
                        'doctor' => [
                            'You are not assigned to this appointment.',
                        ],
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Create Session
                |--------------------------------------------------------------------------
                */

                $session = TelehealthSession::create([

                    'appointment_id' => $appointment->id,

                    'session_url' => $data['session_url'],

                    'platform' => $data['platform'],

                    'room_id' => $data['room_id'] ?? null,

                    'meeting_id' => $data['meeting_id'] ?? null,

                    'recording_url' => $data['recording_url'] ?? null,

                    'recording_consent' => $data['recording_consent'] ?? false,

                    'status' => 'scheduled',

                ]);

               $session = $this->loadRelations($session);

$this->notificationService->sendTelehealthNotification(
    $session,
    'Telehealth Session Scheduled',
    'Your telehealth session has been scheduled. Please use the meeting link at the appointment time.',
    true
);

return $session;
            });

        } catch (ModelNotFoundException $e) {

            throw ValidationException::withMessages([
                'appointment' => [
                    'Appointment not found.',
                ],
            ]);

        }
    }

    /**
     * Find a telehealth session.
     *
     * @throws ValidationException
     */
    public function findSession(string $sessionId): TelehealthSession
    {
        try {

            $session = $this->findSessionOrFail($sessionId);

            return $this->loadRelations($session);

        } catch (ModelNotFoundException $e) {

            throw ValidationException::withMessages([
                'session' => [
                    'Telehealth session not found.',
                ],
            ]);

        }
    }

    /**
     * Find session or fail.
     */
    private function findSessionOrFail(string $sessionId): TelehealthSession
    {
        return TelehealthSession::findOrFail($sessionId);
    }

    /**
     * Ensure session can still be modified.
     *
     * @throws ValidationException
     */
    private function ensureEditable(TelehealthSession $session): void
    {
        if (in_array($session->status, ['completed', 'cancelled'])) {

            throw ValidationException::withMessages([
                'session' => [
                    'Completed or cancelled telehealth sessions cannot be modified.',
                ],
            ]);

        }
    }

    /**
     * Load relationships.
     */
    private function loadRelations(
        TelehealthSession $session
    ): TelehealthSession {

        return $session->load([
            'appointment.patient',
            'appointment.doctor.user',
            'appointment.hospital',
        ]);
    }
    /**
 * Update an existing telehealth session.
 *
 * @throws ValidationException
 */
public function updateSession(
    string $sessionId,
    array $data
): TelehealthSession
{
    try {

        return DB::transaction(function () use (
            $sessionId,
            $data
        ) {

            /*
            |--------------------------------------------------------------------------
            | Find Session
            |--------------------------------------------------------------------------
            */

            $session = $this->findSessionOrFail(
                $sessionId
            );

            /*
            |--------------------------------------------------------------------------
            | Prevent Updating Completed/Cancelled Session
            |--------------------------------------------------------------------------
            */

            $this->ensureEditable(
                $session
            );

            /*
            |--------------------------------------------------------------------------
            | Update Session Information
            |--------------------------------------------------------------------------
            */

            $session->fill([

                'session_url' =>
                    $data['session_url']
                        ?? $session->session_url,

                'platform' =>
                    $data['platform']
                        ?? $session->platform,

                'room_id' =>
                    $data['room_id']
                        ?? $session->room_id,

                'meeting_id' =>
                    $data['meeting_id']
                        ?? $session->meeting_id,

                'recording_url' =>
                    $data['recording_url']
                        ?? $session->recording_url,

                'recording_consent' =>
                    $data['recording_consent']
                        ?? $session->recording_consent,

            ]);

            $session->save();

            /*
            |--------------------------------------------------------------------------
            | Return Updated Session
            |--------------------------------------------------------------------------
            */

            return $this->loadRelations(
                $session->fresh()
            );

        });

    } catch (ModelNotFoundException $e) {

        throw ValidationException::withMessages([
            'session' => [
                'Telehealth session not found.'
            ]
        ]);

    }
}
/**
 * Complete a telehealth session.
 *
 * Changes:
 * active → completed
 * ended_at = now()
 * duration_min = difference in minutes
 * appointment.status = completed
 *
 * @throws ValidationException
 */
public function completeSession(
    string $sessionId
): TelehealthSession {

    try {
        return DB::transaction(function () use ($sessionId) {

            /*
            |-------------------------------------------------------------------------- 
            | Find Session
            |-------------------------------------------------------------------------- 
            */
            $session = $this->findSessionOrFail($sessionId);

            /*
            |-------------------------------------------------------------------------- 
            | Validate Session Status
            |-------------------------------------------------------------------------- 
            */
            if ($session->status !== 'active') {
                throw ValidationException::withMessages([
                    'session' => [
                        'Only active telehealth sessions can be completed.',
                    ],
                ]);
            }

            /*
            |-------------------------------------------------------------------------- 
            | Complete Session
            |-------------------------------------------------------------------------- 
            */
            $endedAt = now();
            $duration = $session->started_at
                ? $session->started_at->diffInMinutes($endedAt)
                : null;

            $session->update([
                'ended_at' => $endedAt,
                'duration_min' => $duration,
                'status' => 'completed',
            ]);

            /*
            |-------------------------------------------------------------------------- 
            | Update Appointment
            |-------------------------------------------------------------------------- 
            */
            $session->appointment->update([
                'status' => 'completed',
            ]);

            /*
            |-------------------------------------------------------------------------- 
            | Complete Medical Encounter (if exists)
            |-------------------------------------------------------------------------- 
            */
            if ($session->appointment->medicalEncounter &&
                $session->appointment->medicalEncounter->status === 'in_progress') {
                $session->appointment->medicalEncounter->update([
                    'status' => 'completed',
                ]);
            }

           $session = $this->loadRelations(
    $session->fresh()
);

$this->notificationService->sendTelehealthNotification(
    $session,
    'Telehealth Session Completed',
    'Your telehealth consultation has been completed.',
    true
);

return $session;
        });
    } catch (ModelNotFoundException $e) {
        throw ValidationException::withMessages([
            'session' => [
                'Telehealth session not found.',
            ],
        ]);
    }
}

/**
 * Cancel a telehealth session.
 *
 * Changes:
 * scheduled/active → cancelled
 * appointment.status = cancelled
 *
 * @throws ValidationException
 */
public function cancelSession(
    string $sessionId
): TelehealthSession {

    try {
        return DB::transaction(function () use ($sessionId) {

            /*
            |-------------------------------------------------------------------------- 
            | Find Session
            |-------------------------------------------------------------------------- 
            */
            $session = $this->findSessionOrFail($sessionId);

            /*
            |-------------------------------------------------------------------------- 
            | Validate Session Status
            |-------------------------------------------------------------------------- 
            */
            if ($session->status === 'completed') {
                throw ValidationException::withMessages([
                    'session' => [
                        'Completed telehealth sessions cannot be cancelled.',
                    ],
                ]);
            }

            /*
            |-------------------------------------------------------------------------- 
            | Cancel Session
            |-------------------------------------------------------------------------- 
            */
            $session->update([
                'status' => 'cancelled',
            ]);

            /*
            |-------------------------------------------------------------------------- 
            | Update Appointment
            |-------------------------------------------------------------------------- 
            */
            $session->appointment->update([
                'status' => 'cancelled',
            ]);

            /*
            |-------------------------------------------------------------------------- 
            | Return Updated Session
            |-------------------------------------------------------------------------- 
            */
            $session = $this->loadRelations(
    $session->fresh()
);

$this->notificationService->sendTelehealthNotification(
    $session,
    'Telehealth Session Cancelled',
    'Your telehealth session has been cancelled.',
    true
);

return $session;
        });
    } catch (ModelNotFoundException $e) {
        throw ValidationException::withMessages([
            'session' => [
                'Telehealth session not found.',
            ],
        ]);
    }
}
/**
 * Start a telehealth session.
 *
 * Changes:
 * scheduled → active
 * started_at = now()
 *
 * @throws ValidationException
 */
public function startSession(
    string $sessionId
): TelehealthSession {

    try {

        return DB::transaction(function () use ($sessionId) {

            /*
            |--------------------------------------------------------------------------
            | Find Session
            |--------------------------------------------------------------------------
            */

            $session = $this->findSessionOrFail($sessionId);


            /*
            |--------------------------------------------------------------------------
            | Validate Session Status
            |--------------------------------------------------------------------------
            */

            if ($session->status !== 'scheduled') {

                throw ValidationException::withMessages([
                    'session' => [
                        'Only scheduled telehealth sessions can be started.',
                    ],
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Start Session
            |--------------------------------------------------------------------------
            */

            $session->update([

                'started_at' => now(),

                'status' => 'active',

            ]);


            /*
            |--------------------------------------------------------------------------
            | Return Updated Session
            |--------------------------------------------------------------------------
            */

          $session = $this->loadRelations(
    $session->fresh()
);

$this->notificationService->sendTelehealthNotification(
    $session,
    'Telehealth Session Started',
    'Your telehealth consultation has started. You can now join the meeting.',
    false
);

return $session;

        });


    } catch (ModelNotFoundException $e) {


        throw ValidationException::withMessages([

            'session' => [
                'Telehealth session not found.',
            ],

        ]);

    }
}
public function createSessionWithPlatform(array $data): TelehealthSession
{
    if ($data['platform'] === 'google_meet') {
        return $this->createGoogleMeetSession($data);
    }

    if ($data['platform'] === 'zoom') {
        return $this->createZoomSession($data);
    }

    if ($data['platform'] === 'microsoft_teams') {
        return $this->createMicrosoftTeamsSession($data);
    }

    if ($data['platform'] === 'custom') {
        return $this->createSession($data);
    }

    throw ValidationException::withMessages([
        'platform' => ['Unsupported platform selected.'],
    ]);
}
public function createZoomSession(array $data): TelehealthSession
{
    $accountId    = config('services.zoom.account_id');
    $clientId     = config('services.zoom.client_id');
    $clientSecret = config('services.zoom.client_secret');
    $zoomUserId   = config('services.zoom.user_id');

    // Check if any key is missing or still a placeholder
    $isPlaceholder = static fn($v) => empty($v)
        || stripos((string) $v, 'your_zoom') !== false
        || stripos((string) $v, 'your-zoom') !== false;

    if ($isPlaceholder($accountId) || $isPlaceholder($clientId) || $isPlaceholder($clientSecret)) {
        throw ValidationException::withMessages([
            'zoom' => [
                'Zoom integration is not configured. '
                . 'Please create a Server-to-Server OAuth app at https://marketplace.zoom.us, '
                . 'then set ZOOM_ACCOUNT_ID, ZOOM_CLIENT_ID, ZOOM_CLIENT_SECRET, and ZOOM_USER_ID in your .env file. '
                . 'Note: the old JWT API Key/Secret credentials are no longer supported.',
            ],
        ]);
    }

    // Get Server-to-Server OAuth token
    $tokenResponse = Http::withBasicAuth($clientId, $clientSecret)
        ->asForm()
        ->post('https://zoom.us/oauth/token', [
            'grant_type' => 'account_credentials',
            'account_id' => $accountId,
        ]);

    if ($tokenResponse->failed()) {
        $zoomError = $tokenResponse->json('reason') ?? $tokenResponse->json('message') ?? 'Unknown error';
        throw ValidationException::withMessages([
            'zoom' => [
                'Failed to authenticate with Zoom: ' . $zoomError . '. '
                . 'Make sure you are using Server-to-Server OAuth credentials (not the old JWT API Key/Secret). '
                . 'Create a Server-to-Server OAuth app at https://marketplace.zoom.us.',
            ],
        ]);
    }

    $accessToken = $tokenResponse->json('access_token');

    $meetingResponse = Http::withToken($accessToken)
        ->post("https://api.zoom.us/v2/users/{$zoomUserId}/meetings", [
            'topic'      => 'Telehealth Appointment',
            'type'       => 2,
            'start_time' => Carbon::parse($data['start_time'])->toIso8601String(),
            'duration'   => Carbon::parse($data['start_time'])->diffInMinutes(Carbon::parse($data['end_time'])),
            'timezone'   => 'Africa/Addis_Ababa',
            'settings'   => [
                'join_before_host' => true,
                'waiting_room'     => false,
            ],
        ]);

    if ($meetingResponse->failed()) {
        $msg = $meetingResponse->json('message') ?? 'Unknown error';
        throw ValidationException::withMessages([
            'zoom' => ['Failed to create Zoom meeting: ' . $msg],
        ]);
    }

    $meeting = $meetingResponse->json();

    $session = TelehealthSession::create([
        'appointment_id'    => $data['appointment_id'],
        'session_url'       => $meeting['join_url'],
        'platform'          => 'zoom',
        'meeting_id'        => (string) $meeting['id'],
        'recording_consent' => $data['recording_consent'] ?? false,
        'status'            => 'scheduled',
    ]);

    $session = $this->loadRelations($session);

    $this->notificationService->sendTelehealthNotification(
        $session,
        'Telehealth Session Scheduled',
        'Your telehealth session has been scheduled. Please use the meeting link at the appointment time.',
        true
    );

    return $session;
}

public function createGoogleMeetSession(array $data)
    {
        // Step 1: Check that credentials.json exists
        $credentialsPath = storage_path('app/google/credentials.json');
        if (! file_exists($credentialsPath)) {
            throw ValidationException::withMessages([
                'google_meet' => [
                    'Google Calendar credentials are not configured. Please upload credentials.json to storage/app/google.',
                ],
            ]);
        }

        // Step 2: Check that the OAuth token exists (user must have completed OAuth)
        $tokenPath = storage_path('app/google/token.json');
        if (! file_exists($tokenPath)) {
            throw ValidationException::withMessages([
                'google_oauth_required' => [
                    'Google Calendar is not connected. Please authorize your Google account before creating a Google Meet session.',
                ],
            ]);
        }

        try {
            // Step 3: Load Google client
            $client = new Client();

            $clientId     = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
            $redirectUri  = config('services.google.redirect');

            if ($clientId && $clientSecret) {
                $client->setClientId($clientId);
                $client->setClientSecret($clientSecret);
                $client->setRedirectUri($redirectUri);
            } else {
                $client->setAuthConfig($credentialsPath);
            }

            $client->addScope(Calendar::CALENDAR);

            // Ensure SSL verification uses the system CA bundle (fixes cURL error 60 on Windows)
            $caBundle = ini_get('curl.cainfo') ?: ini_get('openssl.cafile');
            if ($caBundle && file_exists($caBundle)) {
                $client->setHttpClient(new \GuzzleHttp\Client([
                    'verify' => $caBundle,
                ]));
            }

            // Step 4: Load and apply saved token
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);

            // Step 5: Refresh token if expired
            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    // Save the refreshed token
                    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
                } else {
                    // Token expired and no refresh token — OAuth must be re-done
                    @unlink($tokenPath);
                    throw ValidationException::withMessages([
                        'google_oauth_required' => [
                            'Your Google Calendar authorization has expired. Please re-authorize your Google account.',
                        ],
                    ]);
                }
            }

            $service = new Calendar($client);

            // Step 6: Build event with Meet link


$event = new Calendar\Event([

    'summary' => 'Telehealth Appointment',

    'description' => 'Online medical consultation',

    'start' => [
        'dateTime' => Carbon::parse($data['start_time'])
            ->format('c'),

        'timeZone' => 'Africa/Addis_Ababa',
    ],

    'end' => [
        'dateTime' => Carbon::parse($data['end_time'])
            ->format('c'),

        'timeZone' => 'Africa/Addis_Ababa',
    ],

    'conferenceData' => [

        'createRequest' => [

            'requestId' => Str::uuid()->toString(),

            'conferenceSolutionKey' => [

                'type' => 'hangoutsMeet'

            ],
        ],
    ],
]);

            // Step 7: Insert event into Google Calendar
            $createdEvent = $service->events->insert('primary', $event, ['conferenceDataVersion' => 1]);

            // Step 8: Save in telehealth_sessions
            $session = TelehealthSession::create([
                'appointment_id'    => $data['appointment_id'],
                'session_url'       => $createdEvent->hangoutLink,
                'platform'          => 'google_meet',
                'meeting_id'        => $createdEvent->id,
                'recording_consent' => $data['recording_consent'] ?? false,
                'status'            => 'scheduled',
            ]);

            $session = $this->loadRelations($session);

            $this->notificationService->sendTelehealthNotification(
                $session,
                'Telehealth Session Scheduled',
                'Your telehealth session has been scheduled. Please use the meeting link at the appointment time.',
                true
            );

            return $session;

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Google\Service\Exception $e) {
            $googleErrors = $e->getErrors();
            $reason = $googleErrors[0]['reason'] ?? '';
            if ($e->getCode() === 401 || $reason === 'required' || $reason === 'authError') {
                // Token is invalid — remove it so next request triggers re-auth
                @unlink($tokenPath);
                throw ValidationException::withMessages([
                    'google_oauth_required' => [
                        'Your Google Calendar authorization is invalid or has been revoked. Please re-authorize your Google account.',
                    ],
                ]);
            }
            throw ValidationException::withMessages([
                'google_meet' => ['Google Calendar API error: ' . $e->getMessage()],
            ]);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'google_meet' => ['Failed to create Google Meet session: ' . $e->getMessage()],
            ]);
        }
    }

    /**
     * Create a Microsoft Teams session placeholder.
     * Teams meetings must be created via the Microsoft Graph API;
     * until that integration is configured this stores a session without a URL.
     */
    public function createMicrosoftTeamsSession(array $data): TelehealthSession
    {
        $session = TelehealthSession::create([
            'appointment_id' => $data['appointment_id'],
            'session_url' => null,
            'platform' => 'microsoft_teams',
            'recording_consent' => $data['recording_consent'] ?? false,
            'status' => 'scheduled',
        ]);

        $session = $this->loadRelations($session);

        $this->notificationService->sendTelehealthNotification(
            $session,
            'Telehealth Session Scheduled',
            'Your telehealth session has been scheduled.',
            true
        );

        return $session;
    }

    /**
     * Auto-create a telehealth session when an appointment is confirmed.
     * Uses a Jitsi-based URL so no third-party credentials are required.
     * The doctor can update the session URL later if they prefer Google Meet or Zoom.
     */
    public function autoCreateSession(Appointment $appointment): TelehealthSession
    {
        // Generate a Jitsi room URL — works with zero configuration
        $roomId  = 'tena-' . \Illuminate\Support\Str::random(12);
        $meetUrl = "https://meet.jit.si/{$roomId}";

        $session = TelehealthSession::create([
            'appointment_id'    => $appointment->id,
            'session_url'       => $meetUrl,
            'platform'          => 'custom',
            'room_id'           => $roomId,
            'recording_consent' => false,
            'status'            => 'scheduled',
        ]);

        $session = $this->loadRelations($session);

        // Notify patient with the meeting link
        $this->notificationService->sendTelehealthNotification(
            $session,
            'Telehealth Session Scheduled',
            "Your telehealth appointment has been confirmed. Join via: {$meetUrl}",
            true
        );

        // Notify doctor with the meeting link
        try {
            $this->notificationService->createNotification([
                'user_id'      => $appointment->doctor->user->id,
                'type'         => 'in_app',
                'channel'      => 'telehealth',
                'reference_id' => (string) $session->id,
                'subject'      => 'Telehealth Session Created',
                'content'      => "A telehealth session has been auto-created for your confirmed appointment. Join via: {$meetUrl}",
            ]);
        } catch (\Throwable) { /* silent */ }

        return $session;
    }

    /**
     * Reschedule a telehealth session by adjusting the appointment time.
     * Used when a doctor needs to push the session by a given number of minutes
     * (e.g. +10 min because a prior call is running long).
     *
     * @throws ValidationException
     */
    public function rescheduleSession(string $sessionId, int $addMinutes): TelehealthSession
    {
        return DB::transaction(function () use ($sessionId, $addMinutes) {

            $session = $this->findSessionOrFail($sessionId);
            $this->ensureEditable($session);

            $appointment = $session->appointment;
            if (! $appointment) {
                throw ValidationException::withMessages([
                    'session' => ['Session is not linked to an appointment.'],
                ]);
            }

            $newTime = Carbon::parse($appointment->scheduled_time)->addMinutes($addMinutes);
            $appointment->update(['scheduled_time' => $newTime]);

            // Notify patient of the time change
            try {
                $this->notificationService->sendAppointmentNotification(
                    $appointment->patient->user,
                    'Telehealth Session Rescheduled',
                    "Your telehealth session has been rescheduled to {$newTime->format('M d, Y H:i')}.",
                    false
                );
            } catch (\Throwable) { /* silent */ }

            return $this->loadRelations($session->fresh());
        });
    }

}