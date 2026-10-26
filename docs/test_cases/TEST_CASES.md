# GlowCare – Test Cases Document

## Overview

This document contains detailed test cases for the GlowCare – Personal Skincare Assistant application, organised by functional module. Each test case follows a standardised format for traceability and execution.

**Test Case Format:**

| Column | Description |
|--------|-------------|
| Test ID | Unique identifier (format: TC-MOD-##) |
| Test Case | Brief description of the test |
| Pre-conditions | Required state before executing the test |
| Test Steps | Numbered steps to reproduce |
| Expected Result | What should happen when the test is executed correctly |
| Priority | High / Medium / Low |

---

## 1. User Registration Test Cases

| Test ID | Test Case | Pre-conditions | Test Steps | Expected Result | Priority |
|---------|-----------|----------------|------------|-----------------|----------|
| TC-REG-01 | **Valid Registration** | User is on the registration page. No account exists with test email. | 1. Enter full name: "Jane Doe" <br> 2. Enter email: "jane@example.com" <br> 3. Enter password: "Secure@123" <br> 4. Enter confirm password: "Secure@123" <br> 5. Enter phone: "9876543210" <br> 6. Select gender: "Female" <br> 7. Click "Register" | Account is created successfully. User is redirected to login page with success message "Registration successful! Please log in." Password is stored as bcrypt hash in the database. | High |
| TC-REG-02 | **Duplicate Email Registration** | An account with email "jane@example.com" already exists. | 1. Enter full name: "Jane Smith" <br> 2. Enter email: "jane@example.com" <br> 3. Enter password: "Another@456" <br> 4. Enter confirm password: "Another@456" <br> 5. Click "Register" | Registration fails. Error message "This email is already registered" is displayed. No duplicate record is created in the database. | High |
| TC-REG-03 | **Weak Password** | User is on the registration page. | 1. Enter full name: "John Doe" <br> 2. Enter email: "john@example.com" <br> 3. Enter password: "123" <br> 4. Enter confirm password: "123" <br> 5. Click "Register" | Registration fails. Error message "Password must be at least 8 characters with one uppercase letter, one number, and one special character" is displayed. | High |
| TC-REG-04 | **Missing Required Fields** | User is on the registration page. | 1. Leave full name empty <br> 2. Enter email: "test@example.com" <br> 3. Leave password empty <br> 4. Click "Register" | Registration fails. Validation errors are displayed for each required field: "Full name is required", "Password is required". | High |
| TC-REG-05 | **Invalid Email Format** | User is on the registration page. | 1. Enter full name: "Test User" <br> 2. Enter email: "not-an-email" <br> 3. Enter password: "Valid@1234" <br> 4. Enter confirm password: "Valid@1234" <br> 5. Click "Register" | Registration fails. Error message "Please enter a valid email address" is displayed. The form does not submit to the backend. | Medium |
| TC-REG-06 | **SQL Injection Attempt** | User is on the registration page. | 1. Enter full name: `'; DROP TABLE users; --` <br> 2. Enter email: "hacker@test.com" <br> 3. Enter password: "Attack@123" <br> 4. Enter confirm password: "Attack@123" <br> 5. Click "Register" | The input is treated as a literal string due to prepared statements. If registration succeeds, the full name is stored as `'; DROP TABLE users; --`. The `users` table is NOT dropped. Database integrity is maintained. | High |

---

## 2. User Login Test Cases

| Test ID | Test Case | Pre-conditions | Test Steps | Expected Result | Priority |
|---------|-----------|----------------|------------|-----------------|----------|
| TC-LOG-01 | **Valid User Login** | A user account exists with email "jane@example.com" and password "Secure@123". | 1. Navigate to login page <br> 2. Enter email: "jane@example.com" <br> 3. Enter password: "Secure@123" <br> 4. Click "Login" | Login is successful. Session is created with user_id and role. User is redirected to the User Dashboard. | High |
| TC-LOG-02 | **Wrong Password** | A user account exists with email "jane@example.com". | 1. Navigate to login page <br> 2. Enter email: "jane@example.com" <br> 3. Enter password: "WrongPass@999" <br> 4. Click "Login" | Login fails. Error message "Invalid email or password" is displayed. No session is created. The error message does not reveal whether the email or password is incorrect. | High |
| TC-LOG-03 | **Non-Existent Email** | No account exists with email "ghost@example.com". | 1. Navigate to login page <br> 2. Enter email: "ghost@example.com" <br> 3. Enter password: "AnyPass@123" <br> 4. Click "Login" | Login fails. Error message "Invalid email or password" is displayed. No session is created. | High |
| TC-LOG-04 | **Empty Fields Submission** | User is on the login page. | 1. Leave email field empty <br> 2. Leave password field empty <br> 3. Click "Login" | Login form does not submit. Validation errors "Email is required" and "Password is required" are displayed. | Medium |
| TC-LOG-05 | **Session Persistence** | User has successfully logged in. | 1. Login with valid credentials <br> 2. Note the session is active <br> 3. Navigate to another page (e.g., Products) <br> 4. Navigate back to Dashboard | User remains logged in across page navigations. Session data persists. User's name and role are displayed consistently in the navbar. | Medium |

---

## 3. Skin Assessment Test Cases

| Test ID | Test Case | Pre-conditions | Test Steps | Expected Result | Priority |
|---------|-----------|----------------|------------|-----------------|----------|
| TC-ASS-01 | **Complete Assessment – Oily Skin** | User is logged in. Assessment page is accessible. | 1. Navigate to Skin Assessment <br> 2. Select "Yes" for oily feeling <br> 3. Select "No" for dry & tight <br> 4. Select "No" for easily irritated <br> 5. Select concerns: "Acne, Large pores" <br> 6. Click "Submit" | Assessment is processed. Result shows "Oily" skin type with description and recommended product types. Record is saved to `skin_assessments` table with correct values. | High |
| TC-ASS-02 | **Partial Submission** | User is logged in. Assessment page is accessible. | 1. Navigate to Skin Assessment <br> 2. Answer only the first question <br> 3. Leave remaining questions unanswered <br> 4. Click "Submit" | Submission fails. Error message "Please answer all questions before submitting" is displayed. No record is saved to the database. | High |
| TC-ASS-03 | **Re-Assessment** | User is logged in. User has completed one previous assessment (skin type: "oily"). | 1. Navigate to Skin Assessment <br> 2. Note previous result is displayed <br> 3. Click "Retake Assessment" <br> 4. Answer questions differently (dry=yes, oily=no, irritated=no) <br> 5. Click "Submit" | New assessment is saved with skin type "Dry". The latest assessment is now displayed as the current skin type. Both assessment records exist in the database with different `assessment_date` values. | Medium |
| TC-ASS-04 | **Result Accuracy – Combination Skin** | User is logged in. | 1. Navigate to Skin Assessment <br> 2. Select "Yes" for oily feeling <br> 3. Select "Yes" for dry & tight <br> 4. Select "No" for easily irritated <br> 5. Select concerns: "Uneven texture" <br> 6. Click "Submit" | Assessment correctly identifies skin type as "Combination" since both oily and dry symptoms are present. | High |
| TC-ASS-05 | **Assessment Saved to Database** | User is logged in. Database is accessible. | 1. Complete a full assessment <br> 2. Note the response (assessment_id, skin_type) <br> 3. Query database: `SELECT * FROM skin_assessments WHERE assessment_id = [returned_id]` | Database record matches the submitted data exactly. `user_id` matches the logged-in user. `assessment_date` is within 1 minute of submission time. All ENUM values are valid. | High |

---

## 4. Product Search & Filter Test Cases

| Test ID | Test Case | Pre-conditions | Test Steps | Expected Result | Priority |
|---------|-----------|----------------|------------|-----------------|----------|
| TC-PRD-01 | **Search by Product Name** | User is on the Products page. Products exist in the database with names containing "Cleanser". | 1. Enter "Cleanser" in the search bar <br> 2. Press Enter or click Search | Only products with "Cleanser" in their name are displayed. Products without "Cleanser" in the name are hidden. Result count updates. | High |
| TC-PRD-02 | **Filter by Category** | User is on the Products page. Categories "Moisturiser" and "Sunscreen" exist with associated products. | 1. Click the category filter dropdown <br> 2. Select "Moisturiser" | Only products belonging to the "Moisturiser" category are displayed. Products from other categories are filtered out. | High |
| TC-PRD-03 | **Filter by Skin Type** | User is on the Products page. Products exist with skin types "oily", "dry", and "all". | 1. Click the skin type filter <br> 2. Select "Oily" | Products with `skin_type = 'oily'` or `skin_type = 'all'` are displayed. Products specific to other skin types are hidden. | High |
| TC-PRD-04 | **Sort by Price (Low to High)** | User is on the Products page. Products with various prices exist. | 1. Click the sort dropdown <br> 2. Select "Price: Low to High" | Products are reordered in ascending order by price. The cheapest product appears first. | Medium |
| TC-PRD-05 | **Pagination** | More than 12 products exist in the database (assuming page size of 12). | 1. Navigate to Products page <br> 2. Observe the first page shows 12 products <br> 3. Click "Next" or page 2 | Page 2 displays the next set of products. No products from page 1 are repeated. Total product count is shown. | Medium |

---

## 5. Routine Management Test Cases

| Test ID | Test Case | Pre-conditions | Test Steps | Expected Result | Priority |
|---------|-----------|----------------|------------|-----------------|----------|
| TC-RUT-01 | **Create Morning Routine** | User is logged in. Routine Management page is accessible. | 1. Click "New Routine" <br> 2. Enter routine name: "Morning Glow" <br> 3. Select type: "Morning" <br> 4. Click "Create" | Routine is created successfully. "Morning Glow" appears in the routine list with type "Morning". Record is saved to `routines` table. | High |
| TC-RUT-02 | **Add Steps to Routine** | A routine "Morning Glow" exists for the user. | 1. Open "Morning Glow" routine <br> 2. Click "Add Step" <br> 3. Enter step name: "Cleanser" <br> 4. Set order: 1 <br> 5. Optionally select a product <br> 6. Click "Save Step" <br> 7. Repeat for "Toner" (order 2) and "Moisturiser" (order 3) | Three steps are added and displayed in order: 1. Cleanser, 2. Toner, 3. Moisturiser. All steps are saved to `routine_steps` table with correct `routine_id`. | High |
| TC-RUT-03 | **Complete a Routine Step** | A routine with steps exists. Steps are currently uncompleted. | 1. Open the routine <br> 2. Click the checkbox next to "Cleanser" step <br> 3. Observe the UI update | The step is marked as completed (checkbox checked, visual indicator changes). Database `is_completed` is set to 1 and `completed_date` is today's date. | High |
| TC-RUT-04 | **Edit Routine Name** | A routine "Morning Glow" exists. | 1. Click "Edit" on the routine <br> 2. Change name to "AM Skincare" <br> 3. Click "Save" | Routine name is updated to "AM Skincare" in the UI and database. Steps remain unchanged. | Medium |
| TC-RUT-05 | **Delete Routine** | A routine "AM Skincare" exists with 3 steps. | 1. Click "Delete" on the routine <br> 2. Confirm deletion in the confirmation dialog | Routine is deleted from the database. All associated steps are also deleted (CASCADE). The routine no longer appears in the list. | High |

---

## 6. Progress Tracking Test Cases

| Test ID | Test Case | Pre-conditions | Test Steps | Expected Result | Priority |
|---------|-----------|----------------|------------|-----------------|----------|
| TC-PRG-01 | **Log Daily Progress** | User is logged in. User has at least one routine with steps. | 1. Navigate to Progress page <br> 2. Select date: today <br> 3. Select completion status: "Completed" <br> 4. Select skin feeling: "Good" <br> 5. Enter notes: "Skin feels hydrated today" <br> 6. Click "Save Progress" | Progress log is saved. Success message "Progress logged successfully" is displayed. Record appears in the progress history. Database entry in `progress_logs` matches all submitted values. | High |
| TC-PRG-02 | **View Weekly Summary Chart** | User has progress logs spanning the current week (at least 3 days). | 1. Navigate to Progress page <br> 2. View the weekly summary section | A visual chart/summary displays: total logs for the week, number completed, number skipped. Data matches the `vw_weekly_progress` view results. | Medium |
| TC-PRG-03 | **Add Notes to Progress Entry** | User is on the Progress logging form. | 1. Fill in all required fields <br> 2. Enter notes: "Noticed slight redness after using new serum. Will monitor." <br> 3. Click "Save Progress" | Notes are saved correctly to the `notes` field in `progress_logs`. Notes are visible when viewing the progress history. Special characters in notes are preserved correctly. | Medium |
| TC-PRG-04 | **View Progress History** | User has logged progress on at least 5 different dates. | 1. Navigate to Progress History <br> 2. Observe the list of progress entries | All progress entries are displayed in reverse chronological order (newest first). Each entry shows: date, completion status, skin feeling, and notes. Pagination is applied if entries exceed the page limit. | Medium |

---

## Test Execution Summary

| Module | Total Tests | High Priority | Medium Priority | Low Priority |
|--------|:-----------:|:-------------:|:---------------:|:------------:|
| User Registration | 6 | 5 | 1 | 0 |
| User Login | 5 | 3 | 2 | 0 |
| Skin Assessment | 5 | 4 | 1 | 0 |
| Product Search & Filter | 5 | 3 | 2 | 0 |
| Routine Management | 5 | 4 | 1 | 0 |
| Progress Tracking | 4 | 1 | 3 | 0 |
| **Total** | **30** | **20** | **10** | **0** |

---

> [!IMPORTANT]
> All test cases should be executed in a clean test environment with known seed data. Database should be reset between test runs using the schema script at `database/schema.sql`.

---

*GlowCare – Personal Skincare Assistant © 2026*
