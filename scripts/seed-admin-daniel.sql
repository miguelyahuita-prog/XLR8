-- ============================================================
-- Crear administrador: Daniel Wilson Flores Aguilar
-- Email:    danielwilsonfloresaguilar10@gmail.com
-- Password: 1234556789  (hash scrypt, generado con src/lib/auth/password.ts)
-- ============================================================

INSERT INTO app_users (role_id, full_name, email, phone, password_hash, status)
VALUES (
  (SELECT id FROM roles WHERE name = 'administrador' LIMIT 1),
  'Daniel Wilson Flores Aguilar',
  'danielwilsonfloresaguilar10@gmail.com',
  '70000002',
  'scrypt$be53ee2181efdcf73c4226517bdc0aec$31dcbc4337bd5195e530a3ab2725f3773b82632ad539336be20e7f863744cd4c6b7b8e9498244555f111ce3df62fb07f8567a9b3d8ecf380ed3770d27b8a93e6',
  'activo'
)
ON CONFLICT (email) DO UPDATE
SET password_hash = EXCLUDED.password_hash,
    role_id       = EXCLUDED.role_id,
    full_name     = EXCLUDED.full_name,
    status        = 'activo';
