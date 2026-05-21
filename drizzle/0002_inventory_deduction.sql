-- =====================================================
-- Migración: Descuento automático de inventario al
-- confirmar/preparar pedidos. Idempotente.
-- =====================================================

ALTER TABLE "orders"
  ADD COLUMN IF NOT EXISTS "inventory_deducted" boolean NOT NULL DEFAULT false,
  ADD COLUMN IF NOT EXISTS "inventory_deducted_at" timestamp with time zone;

CREATE INDEX IF NOT EXISTS "orders_inventory_deducted_idx"
  ON "orders" ("inventory_deducted");

-- product_components ya existe; reforzamos índice de búsqueda por producto.
CREATE INDEX IF NOT EXISTS "product_components_product_id_idx"
  ON "product_components" ("product_id");
