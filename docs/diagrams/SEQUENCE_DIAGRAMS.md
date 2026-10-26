# GlowCare – Sequence Diagrams

This document contains UML sequence diagrams for three key system interactions, rendered using Mermaid syntax.

---

## 1. User Login Flow

```mermaid
sequenceDiagram
    actor User
    participant Frontend as React Frontend
    participant Backend as PHP Backend
    participant DB as MySQL Database

    User->>Frontend: Enter email & password
    Frontend->>Frontend: Validate non-empty fields
    alt Fields are empty
        Frontend-->>User: Display "Please fill in all fields"
    else Fields are valid
        Frontend->>Backend: POST /api/login {email, password}
        Backend->>Backend: Sanitise input
        Backend->>DB: SELECT user_id, full_name, email, password_hash, role<br/>FROM users WHERE email = ?
        DB-->>Backend: Return user record (or empty)
        alt User not found
            Backend-->>Frontend: 401 {error: "Invalid credentials"}
            Frontend-->>User: Display "Invalid email or password"
        else User found
            Backend->>Backend: password_verify(password, password_hash)
            alt Password mismatch
                Backend-->>Frontend: 401 {error: "Invalid credentials"}
                Frontend-->>User: Display "Invalid email or password"
            else Password matches
                Backend->>Backend: session_start()<br/>$_SESSION[user_id] = user_id<br/>$_SESSION[role] = role<br/>session_regenerate_id(true)
                Backend-->>Frontend: 200 {user_id, full_name, role}
                alt Role is "admin"
                    Frontend-->>User: Redirect to Admin Dashboard
                else Role is "user"
                    Frontend-->>User: Redirect to User Dashboard
                end
            end
        end
    end
```

---

## 2. Skin Assessment Flow

```mermaid
sequenceDiagram
    actor User
    participant Frontend as React Frontend
    participant Backend as PHP Backend
    participant DB as MySQL Database

    User->>Frontend: Navigate to Skin Assessment page
    Frontend-->>User: Display assessment questionnaire
    User->>Frontend: Answer questions:<br/>1. Oily feeling (yes/no/sometimes)<br/>2. Dry & tight (yes/no/sometimes)<br/>3. Easily irritated (yes/no/sometimes)<br/>4. Select skin concerns
    User->>Frontend: Click "Submit Assessment"
    Frontend->>Frontend: Validate all questions answered
    alt Incomplete answers
        Frontend-->>User: Display "Please answer all questions"
    else All questions answered
        Frontend->>Backend: POST /api/assessment<br/>{oily_feeling, dry_tight, easily_irritated, concerns}
        Backend->>Backend: Verify session is active
        alt Session invalid
            Backend-->>Frontend: 401 {error: "Unauthorized"}
            Frontend-->>User: Redirect to Login page
        else Session valid
            Backend->>Backend: Determine skin type using logic:<br/>oily=yes & dry=no → "oily"<br/>oily=no & dry=yes → "dry"<br/>oily=yes & dry=yes → "combination"<br/>irritated=yes → "sensitive"<br/>otherwise → "normal"
            Backend->>DB: INSERT INTO skin_assessments<br/>(user_id, skin_type, oily_feeling,<br/>dry_tight, easily_irritated, concerns)<br/>VALUES (?, ?, ?, ?, ?, ?)
            DB-->>Backend: Confirm insert (assessment_id)
            Backend-->>Frontend: 201 {assessment_id, skin_type, assessment_date}
            Frontend->>Frontend: Display result card with<br/>skin type, description,<br/>and recommended product types
            Frontend-->>User: Show "Your skin type is: [type]"
        end
    end
```

---

## 3. Save Routine Progress Flow

```mermaid
sequenceDiagram
    actor User
    participant Frontend as React Frontend
    participant Backend as PHP Backend
    participant DB as MySQL Database

    User->>Frontend: Open Routine page
    Frontend->>Backend: GET /api/routines?user_id=session.user_id
    Backend->>DB: SELECT * FROM routines<br/>WHERE user_id = ?
    DB-->>Backend: Return user's routines
    Backend->>DB: SELECT * FROM routine_steps<br/>WHERE routine_id IN (...)<br/>ORDER BY step_order ASC
    DB-->>Backend: Return routine steps
    Backend-->>Frontend: 200 {routines with steps}
    Frontend-->>User: Display routines with step checkboxes

    User->>Frontend: Mark step as completed (checkbox click)
    Frontend->>Backend: POST /api/progress<br/>{routine_step_id, completion_status,<br/>log_date, skin_feeling, notes}
    Backend->>Backend: Verify session is active
    alt Session invalid
        Backend-->>Frontend: 401 {error: "Unauthorized"}
        Frontend-->>User: Redirect to Login page
    else Session valid
        Backend->>DB: SELECT rs.step_id, r.user_id<br/>FROM routine_steps rs<br/>JOIN routines r ON rs.routine_id = r.routine_id<br/>WHERE rs.step_id = ?
        DB-->>Backend: Return step with owner user_id
        alt User does not own this step
            Backend-->>Frontend: 403 {error: "Forbidden"}
            Frontend-->>User: Display "Access denied"
        else User owns the step
            Backend->>DB: UPDATE routine_steps<br/>SET is_completed = 1,<br/>completed_date = CURDATE()<br/>WHERE step_id = ?
            DB-->>Backend: Confirm update
            Backend->>DB: INSERT INTO progress_logs<br/>(user_id, routine_step_id,<br/>completion_status, log_date,<br/>skin_feeling, notes)<br/>VALUES (?, ?, ?, ?, ?, ?)
            DB-->>Backend: Confirm insert (log_id)
            Backend-->>Frontend: 201 {log_id, message: "Progress saved"}
            Frontend->>Frontend: Update step checkbox to checked<br/>Show success toast notification
            Frontend-->>User: Display "Step marked as completed ✓"
        end
    end
```

---

## Diagram Legend

| Symbol | Meaning |
|--------|---------|
| `—>>` | Synchronous message (request) |
| `-->>` | Response / return message |
| `alt / else` | Conditional alternative flows |
| `actor` | External user interacting with the system |
| `participant` | System component (frontend, backend, database) |

---

*GlowCare – Personal Skincare Assistant © 2026*
