# Bugfix Requirements Document

## Introduction

When a hospital admin approves a doctor's leave request, the system already identifies confirmed appointments that fall on the leave date and returns a warning. However, there is no mechanism for the hospital admin to act on that warning — they cannot reassign those confirmed appointments to another available doctor in the same hospital. This leaves affected patients with a confirmed appointment but no treating doctor, creating a care gap that must be resolved manually outside the system.

This bugfix introduces the ability for the hospital admin to reschedule a confirmed appointment to a different available doctor within the same hospital and department, specifically to handle the leave-conflict scenario.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN a hospital admin approves a doctor's leave AND that doctor has confirmed appointments on the leave date THEN the system returns a warning and the count of affected appointments but provides no action to reassign those appointments to another doctor

1.2 WHEN a hospital admin attempts to reschedule a confirmed appointment to a different doctor THEN the system rejects the request because the existing `reschedule` method enforces that the new slot must belong to the same doctor as the original appointment

1.3 WHEN a doctor's leave is approved THEN the system blocks only the doctor's available slots but leaves the already-booked (confirmed) slots untouched, meaning affected appointments remain in `confirmed` status with no resolution path

### Expected Behavior (Correct)

2.1 WHEN a hospital admin approves a doctor's leave AND that doctor has confirmed appointments on the leave date THEN the system SHALL allow the hospital admin to view the list of affected confirmed appointments and reassign each one to an available doctor within the same hospital and department

2.2 WHEN a hospital admin reschedules a leave-affected appointment to a different doctor THEN the system SHALL accept the new doctor's available slot, update the appointment's `doctor_id` and `slot_id` and `scheduled_time` accordingly, and mark the old slot as available

2.3 WHEN a hospital admin selects a replacement doctor for a leave-affected appointment THEN the system SHALL only present doctors who belong to the same hospital and department as the original doctor and who have available slots on the leave date

### Unchanged Behavior (Regression Prevention)

3.1 WHEN a patient reschedules their own appointment to a different time slot of the same doctor THEN the system SHALL CONTINUE TO enforce that the new slot belongs to the same doctor as the original appointment

3.2 WHEN a doctor reschedules an appointment to another slot of their own THEN the system SHALL CONTINUE TO reject slots belonging to a different doctor

3.3 WHEN a hospital admin approves a leave request where the doctor has no confirmed appointments on the leave date THEN the system SHALL CONTINUE TO approve the leave and block available slots without any rescheduling step required

3.4 WHEN a hospital admin approves a leave request THEN the system SHALL CONTINUE TO block the doctor's available (unbooked) slots for the leave date

3.5 WHEN a leave-affected appointment is reassigned to a replacement doctor THEN the system SHALL CONTINUE TO enforce that the replacement doctor's slot is available (not already booked or blocked) and is not in the past

3.6 WHEN a leave-affected appointment is reassigned THEN the system SHALL CONTINUE TO validate that the patient does not already have a conflicting appointment at the same time with another doctor

---

## Bug Condition

### Bug Condition Function

```pascal
FUNCTION isBugCondition(X)
  INPUT: X of type AppointmentRescheduleRequest
  OUTPUT: boolean

  // Returns true when the admin tries to reassign to a different doctor
  RETURN X.new_slot.doctor_id ≠ X.appointment.doctor_id
    AND X.actor.role = 'hospital_admin'
    AND X.appointment.status = 'confirmed'
END FUNCTION
```

### Fix Checking Property

```pascal
// Property: Fix Checking - Cross-Doctor Reschedule by Hospital Admin
FOR ALL X WHERE isBugCondition(X) DO
  result ← adminReschedule'(X)
  ASSERT result.status = 'success'
    AND result.appointment.doctor_id = X.new_slot.doctor_id
    AND result.appointment.slot_id   = X.new_slot.id
    AND result.old_slot.status       = 'available'
    AND result.new_slot.status       = 'booked'
END FOR
```

### Preservation Checking Property

```pascal
// Property: Preservation Checking - Same-Doctor Reschedule Unchanged
FOR ALL X WHERE NOT isBugCondition(X) DO
  ASSERT reschedule(X) = reschedule'(X)
END FOR
```
