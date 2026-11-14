# Pasar Kampus

A local E-Commerce App to help college student in particular to sell, search, and buy a prelove item.
## Admin dashboard database setup

Admin CRUD adds persisted moderation fields to the existing PostgreSQL schema. For an existing database, run `psql -U <user> -d <database> -v ON_ERROR_STOP=1 -f database/admin-migration.sql` before opening `/dashboard/`. The migration is additive and can be rerun. New database installs should use the updated `database/schema.sql`.

Dashboard pages require an authenticated session with the `admin` role. The admin users table uses the HTTP `QUERY` method for server-side pagination, search, and edit-user lookup; create, update, and delete actions remain POST/redirect flows.
The admin category page follows the users-page shell: shared header/footer props, a server-side DataTables table, a matching modal form, POST CRUD actions, and GET query endpoints for pagination, search, and edit lookup.
The admin dashboard includes a responsive campus-user pie chart with a readable count-and-percentage legend; when no users are available, it shows an empty state instead.
Password tetap terlihat saat mengedit akun. Jika dibiarkan kosong, password lama dipertahankan; password baru minimal 8 karakter dan disimpan dalam bentuk hash.
User edits validate NIM as text before checking its 50-character limit, so numeric-only NIMs remain valid.