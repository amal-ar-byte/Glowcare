# GlowCare – Entity-Relationship Diagram

## ER Diagram (Mermaid erDiagram Syntax)

The following diagram shows all **8 tables** in the GlowCare database, their attributes, data types, and relationships with cardinality.

```mermaid
erDiagram
    USERS {
        INT user_id PK
        VARCHAR full_name
        VARCHAR email UK
        VARCHAR password_hash
        VARCHAR phone
        DATE date_of_birth
        ENUM gender
        ENUM role
        VARCHAR profile_image
        DATETIME created_at
        DATETIME updated_at
    }

    SKIN_ASSESSMENTS {
        INT assessment_id PK
        INT user_id FK
        ENUM skin_type
        ENUM oily_feeling
        ENUM dry_tight
        ENUM easily_irritated
        VARCHAR concerns
        DATETIME assessment_date
    }

    CATEGORIES {
        INT category_id PK
        VARCHAR category_name UK
        VARCHAR description
        DATETIME created_at
    }

    PRODUCTS {
        INT product_id PK
        VARCHAR name
        INT category_id FK
        DECIMAL price
        TEXT description
        TEXT ingredients
        TEXT usage_instructions
        TEXT precautions
        ENUM skin_type
        VARCHAR image_path
        TINYINT is_active
        DATETIME created_at
        DATETIME updated_at
    }

    ROUTINES {
        INT routine_id PK
        INT user_id FK
        VARCHAR routine_name
        ENUM routine_type
        DATETIME created_at
        DATETIME updated_at
    }

    ROUTINE_STEPS {
        INT step_id PK
        INT routine_id FK
        INT product_id FK
        VARCHAR step_name
        INT step_order
        TINYINT is_completed
        DATE completed_date
    }

    PROGRESS_LOGS {
        INT log_id PK
        INT user_id FK
        INT routine_step_id FK
        ENUM completion_status
        DATE log_date
        TEXT notes
        ENUM skin_feeling
        DATETIME created_at
    }

    WISHLISTS {
        INT wishlist_id PK
        INT user_id FK
        INT product_id FK
        DATETIME date_added
    }

    USERS ||--o{ SKIN_ASSESSMENTS : "takes"
    USERS ||--o{ ROUTINES : "creates"
    USERS ||--o{ PROGRESS_LOGS : "logs"
    USERS ||--o{ WISHLISTS : "saves"
    CATEGORIES ||--o{ PRODUCTS : "contains"
    PRODUCTS ||--o{ ROUTINE_STEPS : "used in"
    PRODUCTS ||--o{ WISHLISTS : "wishlisted"
    ROUTINES ||--o{ ROUTINE_STEPS : "has"
    ROUTINE_STEPS ||--o{ PROGRESS_LOGS : "tracked by"
```

---

## Table Relationships Summary

| Relationship | Cardinality | Foreign Key | ON DELETE | ON UPDATE |
|---|---|---|---|---|
| USERS → SKIN_ASSESSMENTS | One-to-Many | `skin_assessments.user_id` → `users.user_id` | CASCADE | CASCADE |
| USERS → ROUTINES | One-to-Many | `routines.user_id` → `users.user_id` | CASCADE | CASCADE |
| USERS → PROGRESS_LOGS | One-to-Many | `progress_logs.user_id` → `users.user_id` | CASCADE | CASCADE |
| USERS → WISHLISTS | One-to-Many | `wishlists.user_id` → `users.user_id` | CASCADE | CASCADE |
| CATEGORIES → PRODUCTS | One-to-Many | `products.category_id` → `categories.category_id` | RESTRICT | CASCADE |
| PRODUCTS → ROUTINE_STEPS | One-to-Many | `routine_steps.product_id` → `products.product_id` | SET NULL | CASCADE |
| PRODUCTS → WISHLISTS | One-to-Many | `wishlists.product_id` → `products.product_id` | CASCADE | CASCADE |
| ROUTINES → ROUTINE_STEPS | One-to-Many | `routine_steps.routine_id` → `routines.routine_id` | CASCADE | CASCADE |
| ROUTINE_STEPS → PROGRESS_LOGS | One-to-Many | `progress_logs.routine_step_id` → `routine_steps.step_id` | SET NULL | CASCADE |

---

## Key Constraints

| Constraint Type | Table | Column(s) | Details |
|---|---|---|---|
| PRIMARY KEY | All tables | `*_id` columns | Auto-increment integer |
| UNIQUE | `users` | `email` | Prevents duplicate registrations |
| UNIQUE | `categories` | `category_name` | Prevents duplicate category names |
| UNIQUE (Composite) | `wishlists` | `(user_id, product_id)` | Prevents duplicate wishlist entries |
| CHECK | `products` | `price` | `price >= 0` |
| ENUM | `users` | `gender` | Male, Female, Other, Prefer not to say |
| ENUM | `users` | `role` | user, admin |
| ENUM | `skin_assessments` | `skin_type` | oily, dry, combination, normal, sensitive |
| ENUM | `products` | `skin_type` | oily, dry, combination, normal, sensitive, all |
| ENUM | `routines` | `routine_type` | morning, evening |
| ENUM | `progress_logs` | `completion_status` | completed, skipped, partial |
| ENUM | `progress_logs` | `skin_feeling` | great, good, okay, bad, terrible |

---

## Indexes

| Table | Index Name | Column(s) | Purpose |
|---|---|---|---|
| `users` | `idx_users_email` | `email` | Fast login lookups |
| `users` | `idx_users_role` | `role` | Filter users by role |
| `skin_assessments` | `idx_assessment_user` | `user_id` | Retrieve assessments by user |
| `products` | `idx_product_category` | `category_id` | Filter products by category |
| `products` | `idx_product_skin_type` | `skin_type` | Filter products by skin type |
| `products` | `idx_product_price` | `price` | Sort/filter by price |
| `routines` | `idx_routine_user` | `user_id` | Retrieve routines by user |
| `routines` | `idx_routine_type` | `routine_type` | Filter by morning/evening |
| `routine_steps` | `idx_step_routine` | `routine_id` | Retrieve steps by routine |
| `routine_steps` | `idx_step_order` | `step_order` | Order steps correctly |
| `progress_logs` | `idx_progress_user` | `user_id` | Retrieve logs by user |
| `progress_logs` | `idx_progress_date` | `log_date` | Filter/sort by date |
| `wishlists` | `idx_wishlist_user` | `user_id` | Retrieve wishlist by user |

---

*GlowCare – Personal Skincare Assistant © 2026*
