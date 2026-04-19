# User CRUD + Business Logic Reference Guide

This folder is a learning sandbox for User actor tasks in viva/labs.

It demonstrates two common exam tasks:

1. Add a new field to a form + add a DB column + save/update it.
2. Implement CRUD and business logic with SQL joins in model methods.

## Files in this guide

- controllers/user/crud_reference_controller.php
- models/user/crud_reference_model.php
- views/user/crud_reference_view.php
- assets/javascript/user/crud_reference.js
- assets/css/user/crud_reference.css

## How to run this reference

Open this controller URL in browser (adjust localhost if needed):

/2nd-Year-Group-Project/FixLanka/dev-reference/user-crud/controllers/user/crud_reference_controller.php

The same file also handles API-style actions with query params:

- ?action=list_notes
- ?action=create_note
- ?action=update_note
- ?action=delete_note
- ?action=insights

## Task 1 example: Add a new form field + DB column

Example field: priority_level in jobrequest

### SQL (run in phpMyAdmin SQL tab)

ALTER TABLE jobrequest
ADD COLUMN priority_level ENUM('low','medium','high') NOT NULL DEFAULT 'medium' AFTER budget;

### Rollback SQL (optional)

ALTER TABLE jobrequest
DROP COLUMN priority_level;

### What to change in code in real modules

1. View:

- Add a select input name=priority_level

2. JS:

- Include priority_level in request payload

3. Controller:

- Validate allowed values low/medium/high

4. Model:

- Include priority_level in INSERT and UPDATE query

## Task 2 example: CRUD + business logic join

This reference CRUD uses a practice table user_practice_note.

### SQL for practice table

CREATE TABLE IF NOT EXISTS user_practice_note (
note_id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
title VARCHAR(120) NOT NULL,
body TEXT NULL,
priority_level ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NULL,
INDEX idx_user_practice_note_user (user_id)
);

The model also contains a business logic method that joins:

- jobrequest
- repairerquote
- companyquotation
- review

It returns an insight list for user jobs.

## Viva speaking template

1. Purpose: What this function/page does.
2. Input: What data comes from form/query/body.
3. Validation: How invalid input is blocked.
4. Data action: SQL insert/update/select/delete with prepared statements.
5. Output: JSON or rendered HTML.
6. Safety: auth check, role check, try/catch, transactions if needed.

## Important

This folder is for learning and practice.
You can copy patterns into your actual User modules when implementing real features.
