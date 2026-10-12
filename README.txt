PHOTOLINE REPAIR PATCH
======================

This patch fixes the two runtime problems found in the Photoline project:

1. Staff transaction index was pointing to a non-existent transactions.index view.
   It now correctly uses staff.transactions.index.

2. Login was requiring users.is_active even when an older database did not have
   that column yet. Login now works with the existing database, while still
   enforcing is_active after the column exists.

3. The staff-profile migration was made safe to run against an existing Photoline
   users table. It only adds columns that are actually missing.

COPY INSTRUCTIONS
-----------------
1. BACK UP your current Photoline project first.
2. Extract this ZIP.
3. Copy the extracted app/ and database/ folders into your existing Photoline
   project root and allow files to be replaced.
4. The included staff transaction view is also safe to copy.
5. Start Laravel normally:
      php artisan serve

IMPORTANT
---------
Do NOT delete your database.
Do NOT delete vendor or node_modules.
Do NOT run migrate:fresh.

If you want the new staff profile fields (employee_id, phone, address, position,
 date_hired, emergency contacts, is_active) physically added to MySQL, run:
      php artisan migrate

The migration is designed to avoid adding columns that already exist.
