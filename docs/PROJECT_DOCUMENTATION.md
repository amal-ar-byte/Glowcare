# GlowCare – Personal Skincare Assistant

## Project Documentation

> **Course:** Master of Computer Applications (MCA)  
> **Technology Stack:** React.js · PHP · MySQL  
> **Version:** 1.0  
> **Last Updated:** October 2026

---

## Table of Contents

1. [Problem Statement](#1-problem-statement)
2. [Project Objectives](#2-project-objectives)
3. [Functional Requirements](#3-functional-requirements)
4. [Non-Functional Requirements](#4-non-functional-requirements)
5. [User Stories](#5-user-stories)
6. [Software Development Methodology](#6-software-development-methodology)
7. [Development Phases & Task Breakdown](#7-development-phases--task-breakdown)
8. [Risk Analysis](#8-risk-analysis)
9. [Subject Integration](#9-subject-integration)

---

## 1. Problem Statement

Maintaining a consistent and effective skincare routine is a challenge that millions of people face daily. Key issues include:

- **Lack of skin-type awareness:** Most individuals are unaware of whether their skin is oily, dry, combination, normal, or sensitive, leading them to use products that may cause irritation, breakouts, or dryness.
- **Information overload:** The skincare market is saturated with thousands of products, making it overwhelming for consumers to choose the right ones without professional guidance.
- **Inconsistent routines:** Without a structured system, people frequently skip steps, forget products, or fail to follow morning and evening routines consistently.
- **No progress visibility:** Users have no way to objectively track how their skin responds to a particular routine over time, making it difficult to evaluate what works and what doesn't.
- **Fragmented tools:** Currently, users rely on a combination of spreadsheets, reminder apps, and handwritten notes — none of which are purpose-built for skincare management.

**GlowCare – Personal Skincare Assistant** addresses these problems by providing a unified, web-based platform where users can assess their skin type through a guided questionnaire, receive personalised product suggestions, build structured morning and evening routines, and track their daily progress over time. An administrative panel allows content managers to maintain an up-to-date product catalogue and monitor platform usage.

---

## 2. Project Objectives

| # | Objective |
|---|-----------|
| O-01 | Develop a **skin assessment module** that determines the user's skin type (oily, dry, combination, normal, sensitive) through a structured questionnaire and stores results for future reference. |
| O-02 | Build a **product catalogue** with advanced search and filter capabilities (by category, skin type, price range) so users can discover products suited to their assessed skin type. |
| O-03 | Implement a **routine management system** allowing users to create, customise, and maintain separate morning and evening skincare routines with ordered steps. |
| O-04 | Create a **progress tracking dashboard** where users log daily routine completion, record skin feelings, and visualise weekly improvement trends. |
| O-05 | Provide a **wishlist feature** enabling users to bookmark products they intend to purchase later. |
| O-06 | Develop a **secure authentication system** with hashed passwords, session management, and role-based access control for users and administrators. |
| O-07 | Build an **admin dashboard** for managing products, categories, viewing registered users, and monitoring platform statistics. |

---

## 3. Functional Requirements

| Req ID | Requirement | Description | Priority |
|--------|-------------|-------------|----------|
| FR-01 | User Registration | The system shall allow new users to register with full name, email, password, phone, date of birth, and gender. | High |
| FR-02 | User Login & Authentication | The system shall authenticate users using email and password, create a session, and redirect based on role (user/admin). | High |
| FR-03 | Profile Management | The system shall allow users to view and update their profile information, including uploading a profile image. | Medium |
| FR-04 | Skin Assessment | The system shall present a skin assessment questionnaire and determine the user's skin type based on their answers. | High |
| FR-05 | Re-Assessment | The system shall allow users to retake the skin assessment at any time; the latest assessment is used for recommendations. | Medium |
| FR-06 | Product Browsing & Filtering | The system shall display products and allow filtering by category, skin type, price range, and keyword search. | High |
| FR-07 | Routine Creation | The system shall allow users to create morning and evening routines, add ordered steps, and optionally link products to each step. | High |
| FR-08 | Routine Step Completion | The system shall allow users to mark individual routine steps as completed and record the completion date. | High |
| FR-09 | Progress Logging | The system shall allow users to log daily progress with completion status (completed/skipped/partial), skin feeling, and notes. | High |
| FR-10 | Wishlist Management | The system shall allow users to add/remove products from a personal wishlist with duplicate prevention. | Medium |
| FR-11 | Admin Product Management | The system shall allow administrators to create, read, update, and deactivate products in the catalogue. | High |
| FR-12 | Admin Category Management | The system shall allow administrators to create, read, update, and delete product categories. | Medium |
| FR-13 | Admin User View | The system shall allow administrators to view a list of all registered users with their registration date and role. | Low |
| FR-14 | Admin Statistics Dashboard | The system shall display aggregate statistics: total users, total products, assessments completed, and weekly active users. | Low |

---

## 4. Non-Functional Requirements

| Req ID | Category | Requirement |
|--------|----------|-------------|
| NFR-01 | **Performance** | API responses shall load within 2 seconds for 95% of requests under normal load (≤ 100 concurrent users). |
| NFR-02 | **Security** | All passwords shall be stored using bcrypt hashing (cost factor ≥ 10). SQL injection shall be prevented using PDO prepared statements for every database query. |
| NFR-03 | **Usability** | The user interface shall be responsive and functional across devices with screen widths from 320 px (mobile) to 1920 px (desktop). |
| NFR-04 | **Reliability** | The system shall have 99% uptime during academic demonstration periods. Database transactions shall ensure data integrity for all write operations. |
| NFR-05 | **Portability** | The application shall run on any system with PHP 8.0+, MySQL 8.0+, and Node.js 18+. No OS-specific dependencies shall be introduced. |
| NFR-06 | **Maintainability** | Code shall follow a modular architecture with clear separation of frontend (React components) and backend (PHP API endpoints). Each module shall have fewer than 300 lines of code. |
| NFR-07 | **Scalability** | The database schema shall be normalised to 3NF to support future expansion of products, users, and feature modules without structural changes. |

---

## 5. User Stories

### US-01: User Registration

> **As a** new user,  
> **I want to** register an account with my personal details,  
> **So that** I can save my skincare data and access personalised features.

**Acceptance Criteria:**
- [ ] Registration form collects: full name, email, password, confirm password, phone (optional), date of birth (optional), gender (optional).
- [ ] Email must be unique; duplicate emails show an appropriate error message.
- [ ] Password must be at least 8 characters with one uppercase letter, one number, and one special character.
- [ ] Successful registration redirects to the login page with a success message.
- [ ] Password is stored as a bcrypt hash, never in plain text.

---

### US-02: Skin Assessment

> **As a** registered user,  
> **I want to** complete a skin assessment questionnaire,  
> **So that** I can understand my skin type and receive relevant product suggestions.

**Acceptance Criteria:**
- [ ] The assessment asks about oily feeling, dryness/tightness, irritation sensitivity, and specific concerns.
- [ ] The system determines the skin type (oily, dry, combination, normal, sensitive) based on the answers.
- [ ] Results are displayed immediately with a description of the skin type.
- [ ] The assessment result is saved to the database with a timestamp.
- [ ] Users can retake the assessment; the latest result is always used.

---

### US-03: Morning & Evening Routines

> **As a** user,  
> **I want to** create separate morning and evening skincare routines with ordered steps,  
> **So that** I can organise my daily skincare regimen systematically.

**Acceptance Criteria:**
- [ ] User can create a routine by providing a name and selecting the type (morning/evening).
- [ ] Steps can be added with a name, order number, and an optional linked product.
- [ ] Steps can be reordered, edited, and deleted.
- [ ] Each routine displays its steps in the defined order.
- [ ] Deleting a routine also deletes all its associated steps (cascade).

---

### US-04: Browse & Filter Products

> **As a** user,  
> **I want to** browse and filter the product catalogue,  
> **So that** I can find skincare products that are suitable for my skin type.

**Acceptance Criteria:**
- [ ] Products are displayed in a card/grid layout with image, name, price, and skin type badge.
- [ ] Users can filter by category (e.g., Cleanser, Moisturiser, Sunscreen).
- [ ] Users can filter by skin type compatibility.
- [ ] Users can sort products by price (low-to-high, high-to-low) or name (A–Z).
- [ ] A search bar allows keyword search across product names.

---

### US-05: Track Daily Progress

> **As a** user,  
> **I want to** log my daily routine completion and track how my skin feels,  
> **So that** I can monitor improvement over time.

**Acceptance Criteria:**
- [ ] User can log a progress entry with: date, completion status (completed/skipped/partial), skin feeling (great/good/okay/bad/terrible), and optional notes.
- [ ] Progress entries are linked to specific routine steps when applicable.
- [ ] A weekly summary view shows completed vs. skipped counts.
- [ ] Historical progress entries are viewable in a list sorted by date.

---

### US-06: Wishlist Management

> **As a** user,  
> **I want to** save products to a wishlist,  
> **So that** I can keep track of products I want to purchase in the future.

**Acceptance Criteria:**
- [ ] A "Add to Wishlist" button appears on each product card.
- [ ] Clicking the button adds the product; clicking again removes it (toggle behaviour).
- [ ] The same product cannot be added to a user's wishlist more than once (enforced at the database level).
- [ ] A dedicated wishlist page shows all saved products with an option to remove.

---

### US-07: Admin Product Management

> **As an** administrator,  
> **I want to** add, edit, and deactivate products in the catalogue,  
> **So that** the product catalogue remains accurate and up-to-date.

**Acceptance Criteria:**
- [ ] Admin can add a product with: name, category, price, description, ingredients, usage instructions, precautions, skin type, and image.
- [ ] Admin can edit any field of an existing product.
- [ ] Admin can deactivate a product (soft delete via `is_active` flag) rather than hard-deleting it.
- [ ] Deactivated products are hidden from the user-facing catalogue but remain in the database.
- [ ] Product price must be ≥ 0 (enforced by validation and database CHECK constraint).

---

### US-08: Admin Statistics Dashboard

> **As an** administrator,  
> **I want to** view platform usage statistics,  
> **So that** I can monitor user engagement and application health.

**Acceptance Criteria:**
- [ ] Dashboard displays: total registered users, total active products, total assessments completed, and total progress logs.
- [ ] Statistics update in real time (on page load).
- [ ] Only users with the `admin` role can access this dashboard.
- [ ] Unauthorised access attempts are redirected to the login page.

---

## 6. Software Development Methodology

### Chosen Methodology: Agile (Scrum)

GlowCare follows the **Agile Scrum** methodology with iterative development across four two-week sprints. This approach was chosen because:

- **Incremental delivery:** Each sprint delivers a working increment that can be demonstrated and evaluated.
- **Flexibility:** Requirements can be refined between sprints based on feedback.
- **Risk reduction:** High-priority features are developed first, ensuring that core functionality is always available.
- **Academic suitability:** Sprint boundaries align well with project review milestones.

### Scrum Artefacts

| Artefact | Description |
|----------|-------------|
| Product Backlog | Complete list of features, ranked by priority |
| Sprint Backlog | Subset of items selected for each sprint |
| Increment | Working software produced at the end of each sprint |
| Burndown Chart | Visual tracking of remaining work within a sprint |

### Scrum Ceremonies

| Ceremony | Frequency | Duration |
|----------|-----------|----------|
| Sprint Planning | Start of each sprint | 1–2 hours |
| Daily Stand-up | Daily | 15 minutes |
| Sprint Review | End of each sprint | 1 hour |
| Sprint Retrospective | End of each sprint | 30 minutes |

---

## 7. Development Phases & Task Breakdown

### Sprint 1 — Foundation (Weeks 1–2)

> **Goal:** Project setup, database design, and user authentication

| Task ID | Task | Story Points | Status |
|---------|------|:------------:|--------|
| T-1.1 | Set up React.js project with routing (React Router) | 3 | ✅ Done |
| T-1.2 | Set up PHP backend folder structure and CORS configuration | 2 | ✅ Done |
| T-1.3 | Design and implement MySQL database schema (8 tables) | 5 | ✅ Done |
| T-1.4 | Implement User Registration API (POST /api/register) | 3 | ✅ Done |
| T-1.5 | Implement User Login API with session management | 3 | ✅ Done |
| T-1.6 | Build Registration and Login UI components | 5 | ✅ Done |
| T-1.7 | Implement password hashing (bcrypt) and input validation | 2 | ✅ Done |
| T-1.8 | Create protected route middleware (frontend & backend) | 3 | ✅ Done |

### Sprint 2 — Core Features (Weeks 3–4)

> **Goal:** Skin assessment, routines, and product catalogue

| Task ID | Task | Story Points | Status |
|---------|------|:------------:|--------|
| T-2.1 | Build Skin Assessment questionnaire UI | 5 | ✅ Done |
| T-2.2 | Implement skin type determination logic (backend) | 3 | ✅ Done |
| T-2.3 | Create Assessment API endpoints (POST, GET) | 3 | ✅ Done |
| T-2.4 | Build Product Catalogue UI with card layout | 5 | ✅ Done |
| T-2.5 | Implement product search, filter, and sort API | 5 | ✅ Done |
| T-2.6 | Build Routine Management UI (create, add steps, reorder) | 8 | ✅ Done |
| T-2.7 | Implement Routine & Routine Steps API (CRUD) | 5 | ✅ Done |
| T-2.8 | Admin: Build Category Management UI & API | 3 | ✅ Done |

### Sprint 3 — Enhanced Features (Weeks 5–6)

> **Goal:** Progress tracking, wishlist, and admin dashboard

| Task ID | Task | Story Points | Status |
|---------|------|:------------:|--------|
| T-3.1 | Build Progress Logging UI (daily log form) | 5 | ✅ Done |
| T-3.2 | Implement Progress Logging API (POST, GET) | 3 | ✅ Done |
| T-3.3 | Create weekly progress summary view/chart | 5 | ✅ Done |
| T-3.4 | Build Wishlist UI (add/remove toggle, wishlist page) | 5 | ✅ Done |
| T-3.5 | Implement Wishlist API (POST, DELETE, GET) | 3 | ✅ Done |
| T-3.6 | Admin: Build Product Management UI (CRUD) | 5 | ✅ Done |
| T-3.7 | Admin: Build Statistics Dashboard with aggregate queries | 5 | ✅ Done |
| T-3.8 | Admin: Build User List View | 2 | ✅ Done |

### Sprint 4 — Quality & Delivery (Weeks 7–8)

> **Goal:** Testing, bug fixes, documentation, and deployment

| Task ID | Task | Story Points | Status |
|---------|------|:------------:|--------|
| T-4.1 | Write and execute unit test cases | 5 | ✅ Done |
| T-4.2 | Perform integration testing across modules | 5 | ✅ Done |
| T-4.3 | Fix bugs identified during testing | 5 | ✅ Done |
| T-4.4 | Responsive design testing across devices | 3 | ✅ Done |
| T-4.5 | Security audit (SQL injection, XSS, CSRF) | 3 | ✅ Done |
| T-4.6 | Write project documentation (this document) | 5 | ✅ Done |
| T-4.7 | Create UML diagrams (use-case, sequence, ER) | 3 | ✅ Done |
| T-4.8 | Final deployment and presentation preparation | 3 | ✅ Done |

---

## 8. Risk Analysis

| Risk ID | Risk Description | Probability | Impact | Mitigation Strategy |
|---------|------------------|:-----------:|:------:|---------------------|
| R-01 | **Database failure or data loss** — MySQL server crashes or data becomes corrupted, resulting in loss of user data. | Medium | High | Implement daily automated database backups. Use InnoDB engine with transaction support. Test restore procedures periodically. |
| R-02 | **SQL injection attacks** — Malicious users inject SQL code through input fields to access or modify database records. | High | Critical | Use PDO prepared statements exclusively for all database queries. Validate and sanitise all user inputs on both frontend and backend. |
| R-03 | **Cross-Site Scripting (XSS)** — Attackers inject malicious scripts through text inputs (notes, product descriptions) that execute in other users' browsers. | Medium | High | Escape all user-generated content before rendering. Use React's built-in XSS protection (JSX auto-escaping). Set `HttpOnly` and `Secure` flags on session cookies. |
| R-04 | **Scope creep / changing requirements** — New features requested mid-sprint that delay core development. | High | Medium | Adhere strictly to the sprint backlog. New requests are added to the product backlog and prioritised for the next sprint. Use the MoSCoW method for prioritisation. |
| R-05 | **Performance degradation under load** — Slow page loads when product catalogue grows or many users log progress simultaneously. | Low | Medium | Add database indexes on frequently queried columns (already defined in schema). Implement pagination for list endpoints. Use React lazy loading for images. |
| R-06 | **Session hijacking** — Attacker steals a user's session token and impersonates them. | Medium | High | Use HTTPS in production. Regenerate session IDs after login. Set short session timeouts. Validate session against user-agent and IP where feasible. |
| R-07 | **Browser compatibility issues** — UI renders incorrectly or features break on older browsers. | Low | Low | Test on Chrome, Firefox, Safari, and Edge. Use CSS feature queries and polyfills where needed. Target browsers with > 1% global usage. |
| R-08 | **Third-party dependency vulnerabilities** — npm or Composer packages contain known security flaws. | Medium | Medium | Run `npm audit` and `composer audit` regularly. Keep dependencies updated. Pin major versions to avoid breaking changes. |

---

## 9. Subject Integration

This section describes how each MCA subject's concepts and learning outcomes are applied within the GlowCare project.

### 9.1 RDBMS Lab

| Concept | Implementation in GlowCare |
|---------|---------------------------|
| **Database Design** | A MySQL 8.0 database (`glowcare`) with **8 normalised tables** (users, skin_assessments, categories, products, routines, routine_steps, progress_logs, wishlists) designed in **Third Normal Form (3NF)**. |
| **DDL Operations** | `CREATE TABLE` statements with data types (`INT`, `VARCHAR`, `TEXT`, `DECIMAL`, `DATE`, `DATETIME`, `ENUM`, `TINYINT`), `PRIMARY KEY`, `FOREIGN KEY`, `UNIQUE`, `CHECK`, and `INDEX` constraints. |
| **DML Operations** | `INSERT` for creating records, `UPDATE` for modifying profiles and products, `DELETE` for removing wishlist entries and routines. |
| **Queries** | `SELECT` with `WHERE`, `ORDER BY`, `DISTINCT`, `LIMIT` for pagination. Complex queries use `INNER JOIN`, `LEFT JOIN`, aggregate functions (`COUNT`, `AVG`, `MIN`, `MAX`), `GROUP BY`, `HAVING`, and subqueries. |
| **Views** | Three database views (`vw_user_profile`, `vw_product_catalogue`, `vw_weekly_progress`) demonstrate derived tables for common reporting needs. |
| **Prepared Statements** | All PHP database access uses **PDO prepared statements** with parameter binding to prevent SQL injection. |
| **Indexing** | Strategic indexes on `email`, `role`, `category_id`, `skin_type`, `price`, `user_id`, `log_date`, and `step_order` for query optimisation. |
| **Referential Integrity** | Foreign key constraints with `ON DELETE CASCADE`, `ON DELETE SET NULL`, and `ON DELETE RESTRICT` enforce data consistency. |

### 9.2 UI/UX Design Lab

| Concept | Implementation in GlowCare |
|---------|---------------------------|
| **Responsive Design** | React.js frontend with CSS media queries and flexbox/grid layouts supporting mobile (320 px), tablet (768 px), and desktop (1920 px) breakpoints. |
| **Component-Based UI** | Reusable React components: `ProductCard`, `RoutineStep`, `AssessmentForm`, `ProgressChart`, `WishlistButton`, `Navbar`, `Footer`. |
| **Visual Hierarchy** | Consistent typography scale, colour coding for skin types, clear call-to-action buttons, and card-based layouts for product browsing. |
| **Navigation Design** | Top navigation bar with role-based menu items. Breadcrumbs for deep pages. Protected routes redirect unauthorised users to login. |
| **Form Design** | Structured forms with inline validation, error messages, placeholder text, and progress indicators (e.g., assessment steps). |
| **Accessibility** | Semantic HTML elements (`<main>`, `<nav>`, `<section>`), ARIA labels on interactive elements, and sufficient colour contrast ratios. |

### 9.3 Software Engineering

| Concept | Implementation in GlowCare |
|---------|---------------------------|
| **Methodology** | Agile Scrum with 4 sprints, sprint planning, daily stand-ups, sprint reviews, and retrospectives. |
| **Requirements Engineering** | 14 functional requirements and 7 non-functional requirements documented with unique IDs, descriptions, and priorities. |
| **User Stories** | 8 user stories following the "As a… I want to… So that…" format with detailed acceptance criteria. |
| **UML Diagrams** | Use-case diagram (actors and system boundaries), sequence diagrams (login, assessment, progress), and ER diagram (8 tables with relationships). |
| **Testing** | 30+ detailed test cases covering registration, login, assessment, product search, routine management, and progress tracking. |
| **Risk Management** | 8 identified risks with probability, impact, and mitigation strategies documented in a risk register. |
| **Version Control** | Git with feature branches, descriptive commit messages, and pull request reviews before merging to main. |
| **Documentation** | Comprehensive project documentation including this document, SQL query demos, test cases, and diagram specifications. |

---

> [!NOTE]
> This documentation is a living document and will be updated as the project evolves through subsequent sprints.

---

*GlowCare – Personal Skincare Assistant © 2026. MCA Academic Project.*
