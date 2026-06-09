-- SharingIsCaring database backup
-- Generated at 2026-06-09 05:46:37

DROP TABLE IF EXISTS "cache";
CREATE TABLE "cache" ("key" varchar not null, "value" text not null, "expiration" integer not null, primary key ("key"));

DROP TABLE IF EXISTS "cache_locks";
CREATE TABLE "cache_locks" ("key" varchar not null, "owner" varchar not null, "expiration" integer not null, primary key ("key"));

DROP TABLE IF EXISTS "categories";
CREATE TABLE "categories" ("id" integer primary key autoincrement not null, "name" varchar not null, "slug" varchar not null, "description" text, "icon" varchar, "created_at" datetime, "updated_at" datetime);

DROP TABLE IF EXISTS "equipment";
CREATE TABLE "equipment" ("id" integer primary key autoincrement not null, "club_id" integer not null, "category_id" integer not null, "name" varchar not null, "description" text not null, "image" varchar, "price_per_day" numeric not null, "condition" varchar check ("condition" in ('new', 'excellent', 'good', 'fair', 'poor')) not null default 'good', "availability_status" varchar check ("availability_status" in ('available', 'rented', 'maintenance')) not null default 'available', "pickup_location" varchar not null, "created_at" datetime, "updated_at" datetime, foreign key("club_id") references "users"("id") on delete cascade, foreign key("category_id") references "categories"("id") on delete cascade);

DROP TABLE IF EXISTS "failed_jobs";
CREATE TABLE "failed_jobs" ("id" integer primary key autoincrement not null, "uuid" varchar not null, "connection" text not null, "queue" text not null, "payload" text not null, "exception" text not null, "failed_at" datetime not null default CURRENT_TIMESTAMP);

DROP TABLE IF EXISTS "job_batches";
CREATE TABLE "job_batches" ("id" varchar not null, "name" varchar not null, "total_jobs" integer not null, "pending_jobs" integer not null, "failed_jobs" integer not null, "failed_job_ids" text not null, "options" text, "cancelled_at" integer, "created_at" integer not null, "finished_at" integer, primary key ("id"));

DROP TABLE IF EXISTS "jobs";
CREATE TABLE "jobs" ("id" integer primary key autoincrement not null, "queue" varchar not null, "payload" text not null, "attempts" integer not null, "reserved_at" integer, "available_at" integer not null, "created_at" integer not null);

DROP TABLE IF EXISTS "migrations";
CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null);
INSERT INTO "migrations" ("id", "migration", "batch") VALUES ('1', '0001_01_01_000000_create_users_table', '1');
INSERT INTO "migrations" ("id", "migration", "batch") VALUES ('2', '0001_01_01_000001_create_cache_table', '1');
INSERT INTO "migrations" ("id", "migration", "batch") VALUES ('3', '0001_01_01_000002_create_jobs_table', '1');
INSERT INTO "migrations" ("id", "migration", "batch") VALUES ('4', '2026_06_08_014505_create_categories_table', '1');
INSERT INTO "migrations" ("id", "migration", "batch") VALUES ('5', '2026_06_08_014506_create_equipment_table', '1');
INSERT INTO "migrations" ("id", "migration", "batch") VALUES ('6', '2026_06_08_014506_create_rentals_table', '1');
INSERT INTO "migrations" ("id", "migration", "batch") VALUES ('7', '2026_06_08_014509_add_role_and_club_fields_to_users_table', '1');
INSERT INTO "migrations" ("id", "migration", "batch") VALUES ('8', '2026_06_08_120000_add_pending_club_role_and_unique_club_names', '1');
INSERT INTO "migrations" ("id", "migration", "batch") VALUES ('9', '2026_06_09_000000_add_club_status_fields_to_users_table', '1');

DROP TABLE IF EXISTS "password_reset_tokens";
CREATE TABLE "password_reset_tokens" ("email" varchar not null, "token" varchar not null, "created_at" datetime, primary key ("email"));

DROP TABLE IF EXISTS "rentals";
CREATE TABLE "rentals" ("id" integer primary key autoincrement not null, "equipment_id" integer not null, "borrower_id" integer not null, "start_date" date not null, "end_date" date not null, "purpose" text not null, "status" varchar check ("status" in ('pending', 'approved', 'rejected', 'completed', 'cancelled')) not null default 'pending', "total_price" numeric not null, "admin_notes" text, "created_at" datetime, "updated_at" datetime, foreign key("equipment_id") references "equipment"("id") on delete cascade, foreign key("borrower_id") references "users"("id") on delete cascade);

DROP TABLE IF EXISTS "sessions";
CREATE TABLE "sessions" ("id" varchar not null, "user_id" integer, "ip_address" varchar, "user_agent" text, "payload" text not null, "last_activity" integer not null, primary key ("id"));

DROP TABLE IF EXISTS "users";
CREATE TABLE "users" ("id" integer primary key autoincrement not null, "name" varchar not null, "email" varchar not null, "email_verified_at" datetime, "password" varchar not null, "remember_token" varchar, "created_at" datetime, "updated_at" datetime, "role" varchar check ("role" in ('member', 'club_admin', 'super_admin', 'pending_club')) not null default 'member', "club_name" varchar, "student_id" varchar, "club_status" varchar check ("club_status" in ('pending', 'approved', 'rejected', 'suspended')), "rejection_reason" text, "suspended_at" datetime);

