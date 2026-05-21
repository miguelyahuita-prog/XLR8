/**
 * Seed inicial de usuarios — Sistema Florería Alesli
 *
 * Crea:
 *   - 1 administrador  (admin@alesli.bo / admin12345)
 *   - 1 vendedor       (vendedor@alesli.bo / vendedor12345)
 *
 * Asume que la tabla `roles` ya tiene los 3 roles (administrador, vendedor, cliente),
 * insertados por el script SQL de la BD.
 *
 * Ejecutar con:
 *   pnpm dlx tsx scripts/seed-users.ts
 */
import "dotenv/config"
import { eq } from "drizzle-orm"

import { db, pool } from "../src/db"
import { appUsers, roles } from "../src/db/schema"
import { hashPassword } from "../src/lib/auth/password"

type UserSeed = {
  fullName: string
  email: string
  phone: string
  password: string
  roleName: "administrador" | "vendedor"
}

const SEED: UserSeed[] = [
  {
    fullName: "Administrador Alesli",
    email: "admin@alesli.bo",
    phone: "70000000",
    password: "admin12345",
    roleName: "administrador",
  },
  {
    fullName: "Daniel Wilson Flores Aguilar",
    email: "danielwilsonfloresaguilar10@gmail.com",
    phone: "70000002",
    password: "123456789",
    roleName: "administrador",
  },
  {
    fullName: "Vendedor Alesli",
    email: "vendedor@alesli.bo",
    phone: "70000001",
    password: "vendedor12345",
    roleName: "vendedor",
  },
]

async function main() {
  console.log("🌸 Seed de usuarios Alesli")

  for (const u of SEED) {
    const [role] = await db
      .select({ id: roles.id })
      .from(roles)
      .where(eq(roles.name, u.roleName))
      .limit(1)

    if (!role) {
      console.error(
        `   ✗ Rol '${u.roleName}' no existe. Ejecuta el SQL de la BD primero.`,
      )
      continue
    }

    const [existing] = await db
      .select({ id: appUsers.id })
      .from(appUsers)
      .where(eq(appUsers.email, u.email))
      .limit(1)

    if (existing) {
      console.log(`   ↺ Ya existe: ${u.email} — se actualiza contraseña`)
      await db
        .update(appUsers)
        .set({
          passwordHash: hashPassword(u.password),
          status: "activo",
        })
        .where(eq(appUsers.id, existing.id))
      continue
    }

    await db.insert(appUsers).values({
      roleId: role.id,
      fullName: u.fullName,
      email: u.email,
      phone: u.phone,
      passwordHash: hashPassword(u.password),
      status: "activo",
    })
    console.log(`   ✓ Creado: ${u.email} (${u.roleName})`)
  }

  console.log("\n✅ Listo. Credenciales:")
  for (const u of SEED) {
    console.log(`   ${u.roleName.padEnd(14)} → ${u.email}  /  ${u.password}`)
  }

  await pool.end()
}

main().catch((err) => {
  console.error("✗ Falló el seed:", err)
  process.exit(1)
})
