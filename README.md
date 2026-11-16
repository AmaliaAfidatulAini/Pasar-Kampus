# Pasar Kampus

A local E-Commerce App to help college student in particular to sell, search, and buy a prelove item.

## Admin dashboard database setup

Admin CRUD uses the current PostgreSQL schema. New database installs should run `database/schema.sql` followed by `database/seed.sql`; existing databases must apply the `user_preferences` junction-table definition and remove the legacy `user_profiles.preferences` column before using category preferences.

Dashboard pages require an authenticated session with the `admin` role. The admin users table uses the HTTP `QUERY` method for server-side pagination, search, and edit-user lookup; create, update, and delete actions remain POST/redirect flows.
The admin category page follows the users-page shell: shared header/footer props, a server-side DataTables table, a matching modal form, POST CRUD actions, and GET query endpoints for pagination, search, and edit lookup.
The admin dashboard includes a responsive campus-user pie chart with a readable count-and-percentage legend; when no users are available, it shows an empty state instead.
The admin user form requires email, password, and role on create and edit; passwords must contain at least 8 characters and are stored as hashes.
User edits validate NIM as text before checking its 50-character limit, so numeric-only NIMs remain valid.
User create and edit forms include optional profile details for name, phone, campus, student number, subdistrict, and district. Category preferences use the `user_preferences` junction table and can be selected as multiple categories. The profile inputs use two columns on desktop and one on mobile.