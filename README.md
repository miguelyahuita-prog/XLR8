# Flores Alesli

Aplicacion web completa para una floreria con Next.js, TypeScript, Drizzle ORM y PostgreSQL.

## Requisitos

- Node.js 20+
- PostgreSQL 14+
- npm

## Instalacion

1. Crea la base de datos:

```sql
CREATE DATABASE flores_alesli;
```

2. Copia variables de entorno:

```bash
cp .env.example .env
```

3. Instala dependencias y prepara la base con Drizzle:

```bash
npm install
npm run db:generate
npm run db:migrate
npm run db:seed
```

Tambien puedes aplicar la migracion SQL incluida:

```bash
psql "$DATABASE_URL" -f migrations/001_initial_schema.sql
npm run db:seed
```

4. Ejecuta el sistema:

```bash
npm run dev
```

Abre `http://localhost:3000`.

## Credenciales semilla

- Admin: `admin@floresalesli.com` / `Admin123!`
- Cliente: `cliente@floresalesli.com` / `Cliente123!`

## Arquitectura

- SSR: home, catalogo, dashboard y detalle de pedidos cargan datos en servidor.
- CSR: carrito, formularios, filtros y acciones de administracion consumen la API.
- SSG: paginas informativas como promociones usan `revalidate`.

## Modulos

Usuarios, clientes, productos, categorias, carrito, pedidos, pagos, envios, promociones, detalle de pedido, inventario e imagenes de producto.
