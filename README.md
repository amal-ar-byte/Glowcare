# GlowCare – Personal Skincare Assistant 🌿
> **Master of Computer Applications (MCA) Academic Project**  
> Integrated across:
> 1. **24INMCAL391** – RDBMS Lab  
> 2. **24INMCAL393** – User Interface and Design Lab  
> 3. **24INMCAT307** – Software Engineering  

---

## 🌟 Live Application URL
The application is currently running locally at:
👉 **[http://localhost:3000](http://localhost:3000)**

---

## 📖 1. Project Overview

**GlowCare** is a modern, responsive, full-stack web application designed to help users understand their personal skin type, establish healthy morning and evening skincare routines, explore curated skincare formulations, and maintain an ongoing visual consistency diary.

### Key Objectives:
- Provide an interactive, questionnaire-based **Skin Assessment** that evaluates oiliness, tightness, and barrier reactivity to suggest 1 of 5 skin profiles (*Oily, Dry, Combination, Normal, Sensitive*).
- Support separate **Morning (AM) & Evening (PM)** routines with interactive step completion, ordering, and product linkage.
- Feature a **Product Catalogue** with real-time search, multi-criteria filtering (category, price, skin profile), and an active wishlist.
- Include a **Progress Tracker** with a weekly consistency chart and daily skin sensation notes.
- Provide a secure **Admin Dashboard** for product catalogue CRUD operations and user directory oversight.

---

## 🛠️ 2. Technology Stack

| Layer | Technology | Purpose |
|---|---|---|
| **Frontend** | React.js (v19 functional components & hooks) | Dynamic, single-page application UI |
| **Routing** | React Router v6 | Client-side routing with protected and admin guards |
| **Styling** | Custom CSS3 (CSS Variables, Flexbox, CSS Grid) | Soft cream, pastel pink, sage-green aesthetic |
| **State Management** | React Context API (`AuthContext`) | Global authentication & session persistence |
| **Backend API** | PHP 8.x | RESTful API endpoints with JSON responses |
| **Database** | MySQL 8.0+ | Relational schema with 8 normalized tables |
| **Connection** | PHP PDO (PHP Data Objects) | Prepared SQL statements preventing SQL injection |
| **Dev Environment** | VS Code, XAMPP (Apache + MySQL), Node.js / npm | Local development and demonstration |

---

## 🎓 3. Implementation of the Three MCA Subjects

### A. 24INMCAL391 – RDBMS Lab
- **Normalized Schema (3NF):** 8 relational tables (`users`, `skin_assessments`, `categories`, `products`, `routines`, `routine_steps`, `progress_logs`, `wishlists`).
- **Referential Integrity:** Primary keys, foreign key constraints with `ON DELETE CASCADE` and `ON DELETE SET NULL`.
- **Advanced SQL Queries:**
  - `CREATE`, `INSERT`, `UPDATE`, `DELETE` operations.
  - Multi-table `JOIN` queries (linking users, routines, routine steps, products, categories).
  - Aggregate functions: `COUNT()`, `AVG()`, `MIN()`, `MAX()`.
  - `GROUP BY` and `HAVING` clauses for user progress and skin distribution metrics.
  - Subqueries and Correlated Subqueries (e.g., fetching a user's latest assessment).
  - Database views (`vw_user_profile`, `vw_product_catalogue`, `vw_weekly_progress`).
  - Prepared statements with parameterized bindings (`:email`, `:user_id`, etc.) against SQL injection.
- Reference: See [`database/schema.sql`](file:///d:/Skincare/database/schema.sql) and [`docs/SQL_QUERIES_DEMO.md`](file:///d:/Skincare/docs/SQL_QUERIES_DEMO.md).

### B. 24INMCAL393 – User Interface and Design Lab
- **Visual Aesthetic:** Soft cream (`#FFF9F3`), white (`#FFFFFF`), pastel pink (`#FDE8ED`, `#E88B9E`), sage-green (`#B2C9AD`, `#6E8869`), and gold accents.
- **Typography:** Serif headings (`Playfair Display`) paired with clean sans-serif body typography (`Poppins`).
- **Responsive Layouts:** Desktop (1200px), Tablet (768px-900px), and Mobile (<600px) grid layouts.
- **Interactive Micro-interactions:** Smooth hover transitions, floating hero visual badges, animated modal overlays, and custom CSS-based bar charts.
- **Consistent Navigation:** Glassmorphism sticky navbar, responsive hamburger menu, and clear visual hierarchy.

### C. 24INMCAT307 – Software Engineering
- **Agile Scrum Methodology:** 4 sprints covering requirement analysis, backend design, frontend development, and verification.
- **Requirements Engineering:** 10 Functional Requirements (`FR-01` to `FR-10`) and 6 Non-Functional Requirements (`NFR-01` to `NFR-06`).
- **User Stories & Acceptance Criteria:** Complete user stories for all core user and administrator actions.
- **Visual Modeling:**
  - Use-Case Diagram: [`docs/diagrams/USE_CASE_DIAGRAM.md`](file:///d:/Skincare/docs/diagrams/USE_CASE_DIAGRAM.md)
  - Sequence Diagrams (Login, Assessment, Routine Tracking): [`docs/diagrams/SEQUENCE_DIAGRAMS.md`](file:///d:/Skincare/docs/diagrams/SEQUENCE_DIAGRAMS.md)
  - Entity-Relationship Diagram: [`docs/diagrams/ER_DIAGRAM.md`](file:///d:/Skincare/docs/diagrams/ER_DIAGRAM.md)
- **Test Engineering:** Comprehensive test suite with 28 structured test cases in [`docs/test_cases/TEST_CASES.md`](file:///d:/Skincare/docs/test_cases/TEST_CASES.md).
- **Risk Analysis:** Analysis of security, DB failure, and requirement changes with mitigation plans.

---

## 🔑 4. Demo Test Credentials (Viva Quick Login)

For demonstration and viva presentation, test accounts are pre-configured:

| Account Type | Email Address | Password | Role / Access |
|---|---|---|---|
| **Administrator** | `admin@glowcare.com` | `Password123!` | Full Admin Panel, Product CRUD, User Analytics |
| **Demo Student** | `demo@glowcare.com` | `Password123!` | Combination Skin, Active Routines & History |
| **User (Priya)** | `priya@example.com` | `Password123!` | Combination Skin Profile |
| **User (Rahul)** | `rahul@example.com` | `Password123!` | Oily Skin Profile |

*Note: On the login page, you can also click the quick demo autofill chips to populate the credentials with one click.*

---

## 🖥️ 5. All Pages Included

1. **Home Page (`/`)**: Hero section with floating cards, core feature matrix, dermatological principles, and call-to-action.
2. **Login Page (`/login`)**: Secure authentication with input validation, viva demo autofill buttons, and password hashing check.
3. **Register Page (`/register`)**: Account creation with validation, confirmation matching, and auto-redirect to assessment.
4. **Dashboard (`/dashboard`)**: Welcome banner, identified skin profile avatar, today's AM/PM routine checklist, and weekly progress chart.
5. **Skin Assessment Quiz (`/assessment`)**: 4-question interactive questionnaire evaluating oiliness, post-wash tightness, and sensitivity, with immediate personalized recommendations.
6. **Skincare Routine (`/routine`)**: AM & PM tabs, checklist with real-time toggle, add/delete steps, product linkage, and application hierarchy guide.
7. **Product Catalogue (`/products`)**: Real-world brands (CeraVe, The Ordinary, La Roche-Posay, COSRX, Paula's Choice, etc.) with search, filters, ratings, and instant "Add to Cart 🛒".
8. **Product Details (`/products/:id`)**: High-res product photography, certified ingredients, usage instructions, quantity selector, "Add to Cart", and "Buy Now ⚡".
9. **Shopping Cart & Checkout (`/cart`)**: Complete cart table with quantity modifiers, promo discount coupons (`GLOW20` for 20% off), shipping calculation, and multi-step checkout (UPI, Card, COD) with confirmed order receipts.
10. **Progress Tracker (`/progress`)**: Date selector, completion status buttons, skin feeling emojis, daily notes, and past log history.
11. **Profile Page (`/profile`)**: Personal details editor, skin type overview, and preference settings.
12. **Admin Dashboard (`/admin`)**: Metric statistic cards, live products inventory table with deletion and product addition modal, and registered users list.

---

## 🚀 6. How to Run in Terminal (`npm run dev`)

The development server is currently live on:
👉 **[http://localhost:3000](http://localhost:3000)**

### Method A: Run directly from the project root (Recommended)
Open any terminal (PowerShell, Command Prompt, or VS Code terminal) in `d:\Skincare`:
```powershell
npm run dev
```
*(Or `npm start` — both work instantly and launch the interactive development server with hot-reloading!)*

---

### Method B: Run from the `frontend` directory
Alternatively, you can navigate into `frontend`:
```powershell
cd d:\Skincare\frontend
npm run dev
```

---

### 📦 Optional: Production Build Mode
If you ever want to test or deploy the production build:
```powershell
cd d:\Skincare\frontend
npm run build
npx serve -s build -l 3000
```

---

### 🗄️ Optional: Full XAMPP + PHP + MySQL Integration
1. **Start XAMPP:**
   - Open **XAMPP Control Panel**.
   - Click **Start** for **Apache** and **MySQL**.

2. **Import the Database:**
   - Open phpMyAdmin at `http://localhost/phpmyadmin`.
   - Click on the **Import** tab.
   - Choose file: `d:\Skincare\database\schema.sql` and click **Go**.
   - Choose file: `d:\Skincare\database\sample_data.sql` and click **Go**.

3. **Deploy Backend Endpoints:**
   - The backend files are located at `d:\Skincare\backend\` and can be served under `C:\xampp\htdocs\glowcare\`.
   - Verify that `http://localhost/glowcare/api/products/list.php` returns JSON data.

4. **Run React Frontend:**
   ```powershell
   npm run dev
   ```
   The browser will open `http://localhost:3000` connected directly to the PHP/MySQL backend (with automatic client-side mock fallback if offline)!

---

## 📁 7. Project File Structure

```
d:\Skincare\
├── README.md                          # Comprehensive project guide
├── database\
│   ├── schema.sql                     # Full MySQL schema (8 tables, views, constraints)
│   └── sample_data.sql                # Demonstration data & SQL concept queries
├── backend\                           # PHP API Endpoints (deployed to C:\xampp\htdocs\glowcare\)
│   ├── .htaccess                      # Rewrite rules and headers
│   ├── config\
│   │   ├── database.php               # PDO database connection class
│   │   └── cors.php                   # CORS headers for React frontend
│   └── api\
│       ├── auth\                      # login, register, logout, session
│       ├── users\                     # profile management
│       ├── assessment\                # skin quiz processing & results
│       ├── routines\                  # routine CRUD & step toggle
│       ├── products\                  # product list, details, CRUD
│       ├── categories\                # category listing
│       ├── wishlist\                  # wishlist toggle & list
│       ├── progress\                  # daily habit log & weekly stats
│       └── admin\                     # user list, stats, category management
├── frontend\                          # React.js Frontend Application
│   ├── public\
│   │   └── index.html                 # Google fonts (Playfair Display, Poppins) & icons
│   └── src\
│       ├── App.js                     # Root router & route guards
│       ├── index.js                   # React 19 entry point
│       ├── context\
│       │   └── AuthContext.js         # Authentication state provider
│       ├── services\
│       │   └── api.js                 # Centralized API service with offline fallback
│       ├── components\                # Reusable UI components
│       │   ├── Navbar.js
│       │   ├── Footer.js
│       │   ├── ProductCard.js
│       │   ├── ProgressChart.js
│       │   ├── ProtectedRoute.js
│       │   ├── AdminRoute.js
│       │   ├── Modal.js
│       │   └── LoadingSpinner.js
│       ├── pages\                     # All 11 application pages
│       │   ├── Home.js
│       │   ├── Login.js
│       │   ├── Register.js
│       │   ├── Dashboard.js
│       │   ├── SkinAssessment.js
│       │   ├── SkincareRoutine.js
│       │   ├── ProductCatalogue.js
│       │   ├── ProductDetails.js
│       │   ├── ProgressTracker.js
│       │   ├── Profile.js
│       │   └── AdminDashboard.js
│       └── styles\                    # Custom CSS stylesheets
│           ├── global.css
│           ├── navbar.css
│           ├── footer.css
│           ├── components.css
│           ├── home.css
│           ├── auth.css
│           ├── dashboard.css
│           ├── assessment.css
│           ├── routine.css
│           ├── products.css
│           ├── progress.css
│           ├── profile.css
│           └── admin.css
└── docs\                              # Software Engineering Documentation
    ├── PROJECT_DOCUMENTATION.md       # SRS, functional requirements, agile sprints
    ├── SQL_QUERIES_DEMO.md            # RDBMS demonstration queries (DDL, DML, JOINs)
    ├── diagrams\
    │   ├── USE_CASE_DIAGRAM.md        # Mermaid use case diagram
    │   ├── SEQUENCE_DIAGRAMS.md       # Mermaid sequence diagrams (Login, Quiz, Routine)
    │   └── ER_DIAGRAM.md              # Mermaid ER diagram showing all 8 entities
    └── test_cases\
        └── TEST_CASES.md              # 28 formal test cases with steps and expected results
```

---

## 🏆 Viva Presentation Tips

1. **RDBMS Lab Viva:**
   - Explain the 8 tables and why foreign keys (`ON DELETE CASCADE`) maintain referential integrity.
   - Show the SQL queries in `docs/SQL_QUERIES_DEMO.md` demonstrating subqueries, joins, and aggregates.
   - Point out that passwords in `users` table are hashed using `PASSWORD_BCRYPT` via `password_hash()` in PHP.

2. **UI/UX Lab Viva:**
   - Emphasize the soft cream, pastel pink, and sage-green palette suited for wellness.
   - Demonstrate the interactive CSS Bar Chart in the Progress Tracker (`components/ProgressChart.js`).
   - Showcase responsiveness by resizing the browser or toggling mobile dev tools.

3. **Software Engineering Viva:**
   - Walk through the Mermaid sequence diagrams in `docs/diagrams/SEQUENCE_DIAGRAMS.md`.
   - Show the Agile 4-sprint breakdown and the 28 test cases covering positive, negative, and edge-case inputs in `docs/test_cases/TEST_CASES.md`.
