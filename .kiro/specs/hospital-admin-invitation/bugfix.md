# Bugfix Requirements Document

## Introduction

When a platform admin uses the "Add Admin" form (Hospital, First Name, Last Name, Email, Phone) to create a hospital admin account, the current `store` action in `HospitalStaffController` requires a `password` field and immediately hashes and saves it. This blocks the intended invitation flow where:

1. The platform admin creates the account without setting a password.
2. The system creates the user with `password = NULL` and `status = pending`.
3. The system generates a time-limited invitation token and records an audit log entry.
4. The system sends an invitation email containing the activation link to the new admin's email address.
5. The hospital admin opens the link, sets their own password on the activation page.
6. The password is hashed and saved, and the account status becomes `active`.
7. The hospital admin can then log in normally.

The fix must also ensure that the login guard rejects accounts in `pending` status, so that an un-activated account cannot authenticate.

---

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN a platform admin submits the "Add Admin" form without a `password` field THEN the system returns a validation error (`password` is required) and the hospital admin account is not created.

1.2 WHEN a platform admin submits the "Add Admin" form with a manually supplied `password` THEN the system creates the account with that password already active and `is_active = true`, bypassing the invitation flow entirely — no invitation email is sent and no activation step is required.

1.3 WHEN a user account exists with `is_active = false` (or a pending status) THEN the system still attempts authentication normally, applying no status-aware guard to prevent login of un-activated invitation accounts.

### Expected Behavior (Correct)

2.1 WHEN a platform admin submits the "Add Admin" form with Hospital, First Name, Last Name, Email, and Phone (no password) THEN the system SHALL create a user record with `password = NULL` and `is_active = false` (pending), assign the `hospital_admin` role, create the `hospital_staff` record, generate a unique invitation token with a 72-hour expiry, persist the token to the `password_reset_tokens` table (or a dedicated `invitation_tokens` table), record an audit log entry, and return a 201 response.

2.2 WHEN a pending hospital admin account has been created THEN the system SHALL send an invitation email to the registered email address containing a secure, time-limited activation URL of the form `{FRONTEND_URL}/activate?token={token}&email={email}`.

2.3 WHEN a hospital admin opens the activation link and submits a new password on the activation page THEN the system SHALL validate the token (exists, not expired, matches email), hash and save the password, set `is_active = true`, invalidate/delete the token, and return a success response.

2.4 WHEN an activation token has expired or does not exist THEN the system SHALL return an appropriate error response so the frontend can prompt the admin to request a new invitation.

2.5 WHEN a user account has `is_active = false` THEN the system SHALL reject login attempts with an `Account not yet activated` error (HTTP 403) so that pending accounts cannot authenticate before completing activation.

### Unchanged Behavior (Regression Prevention)

3.1 WHEN a platform admin creates a `receptionist` account via the same staff store endpoint THEN the system SHALL CONTINUE TO apply the same password-free invitation flow (no regression for other staff roles).

3.2 WHEN a fully activated hospital admin (with a valid hashed password and `is_active = true`) attempts to log in THEN the system SHALL CONTINUE TO authenticate them successfully using their email and password.

3.3 WHEN the password reset flow is used by an already-active user THEN the system SHALL CONTINUE TO send a reset email and allow the user to update their password without affecting the invitation token flow.

3.4 WHEN a hospital admin submits the store form with a duplicate email THEN the system SHALL CONTINUE TO return a validation error (email must be unique).

3.5 WHEN a platform admin creates a hospital admin without supplying a `hospital_id` THEN the system SHALL CONTINUE TO return a 422 error requiring hospital selection.

---

## Bug Condition Pseudocode

### Bug Condition Function

```pascal
FUNCTION isBugCondition(request)
  INPUT: request — a POST /api/hospital-staff request
  OUTPUT: boolean

  RETURN request.role IN ('hospital_admin', 'receptionist')
     AND request.password IS NULL
END FUNCTION
```

### Fix Checking Property

```pascal
// Property: Fix Checking — Invitation Flow Triggered
FOR ALL request WHERE isBugCondition(request) DO
  result ← storeHospitalStaff'(request)
  ASSERT result.status_code = 201
  ASSERT User.where(email=request.email).password IS NULL
  ASSERT User.where(email=request.email).is_active = false
  ASSERT invitationToken EXISTS for User
  ASSERT invitationEmail WAS SENT to request.email
END FOR
```

### Preservation Checking Property

```pascal
// Property: Preservation Checking
FOR ALL request WHERE NOT isBugCondition(request) DO
  ASSERT storeHospitalStaff(request) = storeHospitalStaff'(request)
END FOR
```
