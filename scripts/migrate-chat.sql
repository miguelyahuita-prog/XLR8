-- ============================================================
-- Migración: módulo de chat cliente ↔ staff (vendedor/admin)
-- Sistema Florería Alesli
-- ============================================================
-- Ejecutar con: psql -d <db> -f scripts/migrate-chat.sql
-- (o copiar/pegar en pgAdmin / DBeaver)
-- ============================================================

DO $$ BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'chat_sender_type') THEN
    CREATE TYPE chat_sender_type AS ENUM ('cliente', 'staff');
  END IF;
END $$;

CREATE TABLE IF NOT EXISTS chat_threads (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  customer_id uuid NOT NULL UNIQUE REFERENCES customers(id) ON DELETE CASCADE,
  assigned_staff_id uuid REFERENCES app_users(id) ON DELETE SET NULL,
  last_message_at timestamptz DEFAULT now(),
  client_unread integer DEFAULT 0,
  staff_unread integer DEFAULT 0,
  created_at timestamptz DEFAULT now(),
  updated_at timestamptz DEFAULT now()
);

CREATE TABLE IF NOT EXISTS chat_messages (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  thread_id uuid NOT NULL REFERENCES chat_threads(id) ON DELETE CASCADE,
  sender_type chat_sender_type NOT NULL,
  sender_user_id uuid REFERENCES app_users(id) ON DELETE SET NULL,
  body text NOT NULL,
  read_at timestamptz,
  created_at timestamptz DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_chat_messages_thread_created
  ON chat_messages(thread_id, created_at);
CREATE INDEX IF NOT EXISTS idx_chat_threads_last_message
  ON chat_threads(last_message_at DESC);
