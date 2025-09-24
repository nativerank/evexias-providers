CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "practices"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "address" varchar not null,
  "lng" numeric,
  "lat" numeric,
  "external_id" integer not null,
  "unique_name" varchar not null,
  "name" varchar not null,
  "phone" varchar,
  "status" varchar,
  "tenant_id" integer not null default '1',
  foreign key("tenant_id") references "tenants"("id")
);
CREATE UNIQUE INDEX "practices_external_id_unique" on "practices"(
  "external_id"
);
CREATE UNIQUE INDEX "practices_unique_name_unique" on "practices"(
  "unique_name"
);
CREATE TABLE IF NOT EXISTS "practitioners"(
  "id" integer primary key autoincrement not null,
  "external_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  "external_last_modified_at" datetime,
  "active" tinyint(1) not null default '0',
  "first_name" varchar not null,
  "last_name" varchar not null,
  "email" varchar,
  "specialization" varchar,
  "practitioner_type" varchar
);
CREATE UNIQUE INDEX "practitioners_external_id_unique" on "practitioners"(
  "external_id"
);
CREATE TABLE IF NOT EXISTS "practice_practitioner"(
  "id" integer primary key autoincrement not null,
  "practice_id" integer not null,
  "practitioner_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "marketing_emails"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "email" varchar not null
);
CREATE UNIQUE INDEX "marketing_emails_email_unique" on "marketing_emails"(
  "email"
);
CREATE TABLE IF NOT EXISTS "practice_marketing_email"(
  "id" integer primary key autoincrement not null,
  "practice_id" integer not null,
  "marketing_email_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "sales_reps"(
  "id" integer primary key autoincrement not null,
  "external_id" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "external_last_modified_at" datetime not null,
  "name" varchar not null,
  "email" varchar
);
CREATE UNIQUE INDEX "sales_reps_external_id_unique" on "sales_reps"(
  "external_id"
);
CREATE TABLE IF NOT EXISTS "practice_sales_rep"(
  "id" integer primary key autoincrement not null,
  "practice_id" integer not null,
  "sales_rep_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "endpoints"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "root" varchar not null,
  "user" varchar,
  "token" varchar,
  "type" varchar not null,
  "target" varchar not null,
  "group_type" varchar not null,
  "group_id" integer not null
);
CREATE INDEX "endpoints_group_type_group_id_index" on "endpoints"(
  "group_type",
  "group_id"
);
CREATE UNIQUE INDEX "endpoints_group_id_group_type_root_unique" on "endpoints"(
  "group_id",
  "group_type",
  "root"
);
CREATE INDEX "endpoints_root_index" on "endpoints"("root");
CREATE INDEX "endpoints_type_index" on "endpoints"("type");
CREATE INDEX "endpoints_target_index" on "endpoints"("target");
CREATE TABLE IF NOT EXISTS "endpoint_items"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "source_hash" varchar not null,
  "target_hash" varchar,
  "external_id" integer,
  "item_type" varchar not null,
  "item_id" integer not null,
  "endpoint_id" integer not null,
  "modified_at" datetime,
  "synced_at" datetime,
  foreign key("endpoint_id") references "endpoints"("id") on delete cascade
);
CREATE INDEX "endpoint_items_item_type_item_id_index" on "endpoint_items"(
  "item_type",
  "item_id"
);
CREATE INDEX "endpoint_items_source_hash_target_hash_index" on "endpoint_items"(
  "source_hash",
  "target_hash"
);
CREATE TABLE IF NOT EXISTS "tenants"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "personal_access_tokens"(
  "id" integer primary key autoincrement not null,
  "tokenable_type" varchar not null,
  "tokenable_id" integer not null,
  "name" text not null,
  "token" varchar not null,
  "abilities" text,
  "last_used_at" datetime,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" on "personal_access_tokens"(
  "tokenable_type",
  "tokenable_id"
);
CREATE UNIQUE INDEX "personal_access_tokens_token_unique" on "personal_access_tokens"(
  "token"
);
CREATE INDEX "personal_access_tokens_expires_at_index" on "personal_access_tokens"(
  "expires_at"
);
CREATE TABLE IF NOT EXISTS "third_party_connections"(
  "id" integer primary key autoincrement not null,
  "created_at" datetime,
  "updated_at" datetime,
  "provider" varchar not null,
  "class" varchar,
  "external_id" varchar not null,
  "connectable_type" varchar not null,
  "connectable_id" integer not null
);
CREATE INDEX "third_party_connections_connectable_type_connectable_id_index" on "third_party_connections"(
  "connectable_type",
  "connectable_id"
);
CREATE UNIQUE INDEX "third_party_unique" on "third_party_connections"(
  "connectable_type",
  "connectable_id",
  "provider",
  "external_id"
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2025_08_12_220045_create_practices_table',1);
INSERT INTO migrations VALUES(5,'2025_08_12_220055_create_practitioners_table',1);
INSERT INTO migrations VALUES(6,'2025_08_12_230354_practice_practitioner',1);
INSERT INTO migrations VALUES(7,'2025_08_12_231412_create_marketing_emails_table',1);
INSERT INTO migrations VALUES(8,'2025_08_12_231525_practice_marketing_email',1);
INSERT INTO migrations VALUES(9,'2025_08_12_231657_create_sales_reps_table',1);
INSERT INTO migrations VALUES(10,'2025_08_12_231746_practice_sales_rep',1);
INSERT INTO migrations VALUES(11,'2025_09_13_193030_create_endpoints',1);
INSERT INTO migrations VALUES(12,'2025_09_13_193035_create_endpoint_items',1);
INSERT INTO migrations VALUES(13,'2025_09_13_193553_create_tenants_table',1);
INSERT INTO migrations VALUES(14,'2025_09_13_201708_create_personal_access_tokens_table',2);
INSERT INTO migrations VALUES(15,'2025_09_23_235640_create_third_party_connections_table',2);
