/**
 * Aplica las tablas/enums nuevos para SUCURSALES.
 * Ejecutar con: pnpm dlx tsx scripts/apply-branches.ts
 */
import "dotenv/config"
import { sql } from "drizzle-orm"

import { db, pool } from "../src/db"

async function main() {
  await db.execute(sql`
    DO $$
    BEGIN
      IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'bolivia_department') THEN
        CREATE TYPE bolivia_department AS ENUM (
          'La Paz','Cochabamba','Santa Cruz','Tarija','Chuquisaca',
          'Oruro','Potosi','El Beni','Pando'
        );
      END IF;
    END
    $$;
  `)

  await db.execute(sql`
    CREATE TABLE IF NOT EXISTS branches (
      id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
      name varchar(120) NOT NULL,
      department bolivia_department NOT NULL,
      city varchar(120),
      address text NOT NULL,
      phone varchar(40),
      whatsapp varchar(40),
      map_url text,
      schedule_note varchar(200),
      is_active boolean DEFAULT true,
      created_at timestamptz DEFAULT now(),
      updated_at timestamptz DEFAULT now()
    );
  `)

  console.log("[branches] ✓ tabla y enum listos")
  await pool.end()
}

main().catch((err) => {
  console.error(err)
  process.exit(1)
})
