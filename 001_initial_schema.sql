CREATE TYPE role AS ENUM ('admin', 'cliente');
CREATE TYPE order_status AS ENUM ('pendiente', 'enviado', 'entregado', 'cancelado');
CREATE TYPE payment_status AS ENUM ('pendiente', 'pagado', 'rechazado', 'reembolsado');
CREATE TYPE shipping_status AS ENUM ('preparando', 'en_camino', 'entregado');
CREATE TYPE inventory_type AS ENUM ('entrada', 'salida', 'ajuste');
CREATE TYPE promotion_type AS ENUM ('porcentaje', 'monto');

CREATE TABLE users (
  id serial PRIMARY KEY,
  name varchar(160) NOT NULL,
  email varchar(180) NOT NULL UNIQUE,
  password_hash text NOT NULL,
  role role NOT NULL DEFAULT 'cliente',
  phone varchar(40),
  address text,
  created_at timestamp NOT NULL DEFAULT now(),
  updated_at timestamp NOT NULL DEFAULT now()
);

CREATE TABLE customers (
  id serial PRIMARY KEY,
  user_id integer REFERENCES users(id) ON DELETE SET NULL,
  name varchar(160) NOT NULL,
  email varchar(180) NOT NULL,
  phone varchar(40) NOT NULL,
  address text NOT NULL,
  notes text,
  created_at timestamp NOT NULL DEFAULT now()
);

CREATE TABLE categories (
  id serial PRIMARY KEY,
  name varchar(120) NOT NULL,
  slug varchar(140) NOT NULL UNIQUE,
  description text,
  active boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT now()
);

CREATE TABLE products (
  id serial PRIMARY KEY,
  category_id integer REFERENCES categories(id) ON DELETE SET NULL,
  name varchar(180) NOT NULL,
  slug varchar(200) NOT NULL UNIQUE,
  description text NOT NULL,
  price numeric(10,2) NOT NULL,
  stock integer NOT NULL DEFAULT 0,
  image_url text,
  featured boolean NOT NULL DEFAULT false,
  is_customizable boolean NOT NULL DEFAULT false,
  created_at timestamp NOT NULL DEFAULT now(),
  updated_at timestamp NOT NULL DEFAULT now()
);

CREATE TABLE product_images (
  id serial PRIMARY KEY,
  product_id integer NOT NULL REFERENCES products(id) ON DELETE CASCADE,
  url text NOT NULL,
  alt varchar(180) NOT NULL,
  is_primary boolean NOT NULL DEFAULT false
);

CREATE TABLE promotions (
  id serial PRIMARY KEY,
  code varchar(40) NOT NULL UNIQUE,
  name varchar(140) NOT NULL,
  description text,
  type promotion_type NOT NULL DEFAULT 'porcentaje',
  value numeric(10,2) NOT NULL,
  starts_at timestamp,
  ends_at timestamp,
  active boolean NOT NULL DEFAULT true
);

CREATE TABLE promotion_products (
  promotion_id integer NOT NULL REFERENCES promotions(id) ON DELETE CASCADE,
  product_id integer NOT NULL REFERENCES products(id) ON DELETE CASCADE,
  PRIMARY KEY (promotion_id, product_id)
);

CREATE TABLE orders (
  id serial PRIMARY KEY,
  customer_id integer NOT NULL REFERENCES customers(id) ON DELETE RESTRICT,
  user_id integer REFERENCES users(id) ON DELETE SET NULL,
  promotion_id integer REFERENCES promotions(id) ON DELETE SET NULL,
  status order_status NOT NULL DEFAULT 'pendiente',
  custom_message text,
  delivery_date timestamp,
  subtotal numeric(10,2) NOT NULL,
  discount numeric(10,2) NOT NULL DEFAULT 0,
  total numeric(10,2) NOT NULL,
  created_at timestamp NOT NULL DEFAULT now()
);

CREATE TABLE order_items (
  id serial PRIMARY KEY,
  order_id integer NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
  product_id integer NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
  quantity integer NOT NULL,
  unit_price numeric(10,2) NOT NULL,
  subtotal numeric(10,2) NOT NULL
);

CREATE TABLE payments (
  id serial PRIMARY KEY,
  order_id integer NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
  method varchar(80) NOT NULL,
  status payment_status NOT NULL DEFAULT 'pendiente',
  amount numeric(10,2) NOT NULL,
  transaction_ref varchar(120),
  paid_at timestamp,
  created_at timestamp NOT NULL DEFAULT now()
);

CREATE TABLE shipments (
  id serial PRIMARY KEY,
  order_id integer NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
  address text NOT NULL,
  status shipping_status NOT NULL DEFAULT 'preparando',
  tracking_code varchar(80),
  delivery_notes text,
  created_at timestamp NOT NULL DEFAULT now()
);

CREATE TABLE inventory_movements (
  id serial PRIMARY KEY,
  product_id integer NOT NULL REFERENCES products(id) ON DELETE CASCADE,
  type inventory_type NOT NULL,
  quantity integer NOT NULL,
  reason text NOT NULL,
  created_at timestamp NOT NULL DEFAULT now()
);
