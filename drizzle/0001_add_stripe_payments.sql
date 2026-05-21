-- =====================================================
-- Migración: Soporte de pagos con Stripe + anticipos
-- =====================================================

-- 1) Nuevo valor en el enum payment_method
DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_enum
    WHERE enumlabel = 'tarjeta'
      AND enumtypid = (SELECT oid FROM pg_type WHERE typname = 'payment_method')
  ) THEN
    ALTER TYPE "payment_method" ADD VALUE 'tarjeta';
  END IF;
END$$;

-- 2) Nuevo enum payment_kind (total | anticipo)
DO $$
BEGIN
  IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'payment_kind') THEN
    CREATE TYPE "payment_kind" AS ENUM ('total', 'anticipo');
  END IF;
END$$;

-- 3) Nuevas columnas en payments
ALTER TABLE "payments"
  ADD COLUMN IF NOT EXISTS "kind" "payment_kind" DEFAULT 'total',
  ADD COLUMN IF NOT EXISTS "stripe_session_id" varchar(200),
  ADD COLUMN IF NOT EXISTS "stripe_payment_intent_id" varchar(200),
  ADD COLUMN IF NOT EXISTS "stripe_metadata" jsonb,
  ADD COLUMN IF NOT EXISTS "currency" varchar(8) DEFAULT 'BOB';

-- 4) Índices para búsquedas por id de Stripe
CREATE INDEX IF NOT EXISTS "payments_stripe_session_id_idx"
  ON "payments" ("stripe_session_id");
CREATE INDEX IF NOT EXISTS "payments_stripe_payment_intent_id_idx"
  ON "payments" ("stripe_payment_intent_id");
