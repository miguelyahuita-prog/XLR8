<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/base_de_datos.php';

$pdo = conectarBaseDatos();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$usuarioActual = $_SESSION['usuario'];
$rolActual = $usuarioActual['rol'] ?? 'personal';

$modules = [
    'clientes' => [
        'title' => 'Clientes',
        'table' => 'clientes',
        'icon' => '👤',
        'fields' => [
            'nombre' => ['label' => 'Nombre', 'type' => 'text', 'required' => true],
            'telefono' => ['label' => 'Telefono', 'type' => 'text'],
            'email' => ['label' => 'Email', 'type' => 'email'],
            'direccion' => ['label' => 'Direccion', 'type' => 'textarea'],
        ],
    ],
    'personal' => [
        'title' => 'Personal',
        'table' => 'personal',
        'icon' => '🧑‍💼',
        'fields' => [
            'nombre' => ['label' => 'Nombre', 'type' => 'text', 'required' => true],
            'cargo' => ['label' => 'Cargo', 'type' => 'text'],
            'telefono' => ['label' => 'Telefono', 'type' => 'text'],
            'fecha_ingreso' => ['label' => 'Fecha de ingreso', 'type' => 'date'],
            'salario' => ['label' => 'Salario', 'type' => 'number', 'step' => '0.01'],
        ],
    ],
    'usuarios' => [
        'title' => 'Usuarios',
        'table' => 'usuarios',
        'icon' => '🔐',
        'fields' => [
            'nombre' => ['label' => 'Nombre', 'type' => 'text', 'required' => true],
            'usuario' => ['label' => 'Usuario', 'type' => 'text', 'required' => true],
            'clave' => ['label' => 'Contrasena', 'type' => 'password'],
            'rol' => ['label' => 'Rol', 'type' => 'select_static', 'options' => ['admin', 'personal']],
            'estado' => ['label' => 'Estado', 'type' => 'select_static', 'options' => ['Activo', 'Inactivo']],
        ],
    ],
    'categorias' => [
        'title' => 'Categorias',
        'table' => 'categorias',
        'icon' => '🏷️',
        'fields' => [
            'nombre' => ['label' => 'Nombre', 'type' => 'text', 'required' => true],
            'descripcion' => ['label' => 'Descripcion', 'type' => 'textarea'],
        ],
    ],
    'productos' => [
        'title' => 'Productos',
        'table' => 'productos',
        'icon' => '🌷',
        'fields' => [
            'categoria_id' => ['label' => 'Categoria', 'type' => 'select', 'source' => 'categorias'],
            'nombre' => ['label' => 'Nombre', 'type' => 'text', 'required' => true],
            'descripcion' => ['label' => 'Descripcion', 'type' => 'textarea'],
            'precio' => ['label' => 'Precio', 'type' => 'number', 'step' => '0.01', 'required' => true],
            'imagen_url' => ['label' => 'URL de imagen', 'type' => 'url'],
            'estado' => ['label' => 'Estado', 'type' => 'select_static', 'options' => ['Activo', 'Agotado', 'Inactivo']],
        ],
    ],
    'inventario' => [
        'title' => 'Inventario',
        'table' => 'inventario',
        'icon' => '📦',
        'fields' => [
            'producto_id' => ['label' => 'Producto', 'type' => 'select', 'source' => 'productos'],
            'stock' => ['label' => 'Stock', 'type' => 'number', 'required' => true],
            'stock_minimo' => ['label' => 'Stock minimo', 'type' => 'number'],
            'ubicacion' => ['label' => 'Ubicacion', 'type' => 'text'],
            'fecha_actualizacion' => ['label' => 'Fecha actualizacion', 'type' => 'date'],
        ],
    ],
    'pedidos' => [
        'title' => 'Pedidos',
        'table' => 'pedidos',
        'icon' => '🧾',
        'fields' => [
            'cliente_id' => ['label' => 'Cliente', 'type' => 'select', 'source' => 'clientes'],
            'fecha_pedido' => ['label' => 'Fecha pedido', 'type' => 'date', 'required' => true],
            'fecha_entrega' => ['label' => 'Fecha entrega', 'type' => 'date'],
            'direccion_entrega' => ['label' => 'Direccion entrega', 'type' => 'textarea'],
            'estado' => ['label' => 'Estado', 'type' => 'select_static', 'options' => ['Nuevo', 'Preparando', 'Entregado', 'Cancelado']],
            'total' => ['label' => 'Total', 'type' => 'number', 'step' => '0.01'],
        ],
    ],
    'detalle_pedido' => [
        'title' => 'Detalle del pedido',
        'table' => 'detalle_pedido',
        'icon' => '🛒',
        'fields' => [
            'pedido_id' => ['label' => 'Pedido', 'type' => 'select', 'source' => 'pedidos'],
            'producto_id' => ['label' => 'Producto', 'type' => 'select', 'source' => 'productos'],
            'cantidad' => ['label' => 'Cantidad', 'type' => 'number', 'required' => true],
            'precio_unitario' => ['label' => 'Precio unitario', 'type' => 'number', 'step' => '0.01'],
            'subtotal' => ['label' => 'Subtotal', 'type' => 'number', 'step' => '0.01'],
        ],
    ],
    'eventos' => [
        'title' => 'Eventos',
        'table' => 'eventos',
        'icon' => '🎉',
        'fields' => [
            'cliente_id' => ['label' => 'Cliente', 'type' => 'select', 'source' => 'clientes'],
            'nombre_evento' => ['label' => 'Nombre evento', 'type' => 'text', 'required' => true],
            'tipo_evento' => ['label' => 'Tipo evento', 'type' => 'select_static', 'options' => ['Boda', 'Cumpleanos', 'Graduacion', 'Corporativo', 'Otro']],
            'fecha_evento' => ['label' => 'Fecha evento', 'type' => 'date', 'required' => true],
            'lugar' => ['label' => 'Lugar', 'type' => 'text'],
            'estado' => ['label' => 'Estado', 'type' => 'select_static', 'options' => ['Cotizado', 'Confirmado', 'En preparacion', 'Realizado', 'Cancelado']],
            'total' => ['label' => 'Total', 'type' => 'number', 'step' => '0.01'],
        ],
    ],
    'detalle_evento' => [
        'title' => 'Detalle del evento',
        'table' => 'detalle_evento',
        'icon' => '💐',
        'fields' => [
            'evento_id' => ['label' => 'Evento', 'type' => 'select', 'source' => 'eventos'],
            'producto_id' => ['label' => 'Producto', 'type' => 'select', 'source' => 'productos'],
            'descripcion_servicio' => ['label' => 'Servicio', 'type' => 'textarea'],
            'cantidad' => ['label' => 'Cantidad', 'type' => 'number'],
            'precio' => ['label' => 'Precio', 'type' => 'number', 'step' => '0.01'],
        ],
    ],
    'agenda' => [
        'title' => 'Agenda',
        'table' => 'agenda',
        'icon' => '📅',
        'fields' => [
            'evento_id' => ['label' => 'Evento', 'type' => 'select', 'source' => 'eventos'],
            'personal_id' => ['label' => 'Responsable', 'type' => 'select', 'source' => 'personal'],
            'fecha' => ['label' => 'Fecha', 'type' => 'date', 'required' => true],
            'hora' => ['label' => 'Hora', 'type' => 'time'],
            'actividad' => ['label' => 'Actividad', 'type' => 'text', 'required' => true],
            'estado' => ['label' => 'Estado', 'type' => 'select_static', 'options' => ['Pendiente', 'En proceso', 'Completado']],
        ],
    ],
    'pagos' => [
        'title' => 'Pagos QR',
        'table' => 'pagos',
        'icon' => '▣',
        'roles' => ['admin', 'personal'],
        'fields' => [
            'cliente_id' => ['label' => 'Cliente', 'type' => 'select', 'source' => 'clientes'],
            'pedido_id' => ['label' => 'Pedido', 'type' => 'select', 'source' => 'pedidos'],
            'evento_id' => ['label' => 'Evento', 'type' => 'select', 'source' => 'eventos'],
            'fecha_pago' => ['label' => 'Fecha pago', 'type' => 'date', 'required' => true],
            'monto' => ['label' => 'Monto', 'type' => 'number', 'step' => '0.01', 'required' => true],
            'metodo' => ['label' => 'Metodo', 'type' => 'select_static', 'options' => ['QR', 'Efectivo', 'Transferencia']],
            'referencia_qr' => ['label' => 'Referencia QR', 'type' => 'text'],
            'estado' => ['label' => 'Estado', 'type' => 'select_static', 'options' => ['Pendiente', 'Pagado', 'Anulado']],
        ],
    ],
    'ventas' => [
        'title' => 'Ventas',
        'table' => 'ventas',
        'icon' => '$',
        'roles' => ['admin', 'personal'],
        'fields' => [
            'personal_id' => ['label' => 'Vendedor', 'type' => 'select', 'source' => 'personal'],
            'cliente_id' => ['label' => 'Cliente', 'type' => 'select', 'source' => 'clientes'],
            'fecha_venta' => ['label' => 'Fecha venta', 'type' => 'date', 'required' => true],
            'concepto' => ['label' => 'Concepto', 'type' => 'text', 'required' => true],
            'monto' => ['label' => 'Monto', 'type' => 'number', 'step' => '0.01', 'required' => true],
            'metodo_pago' => ['label' => 'Metodo pago', 'type' => 'select_static', 'options' => ['QR', 'Efectivo', 'Transferencia', 'Tarjeta']],
            'observacion' => ['label' => 'Observacion', 'type' => 'textarea'],
        ],
    ],
    'chat_reservas' => [
        'title' => 'Chat reservas',
        'table' => 'chat_reservas',
        'icon' => '💬',
        'roles' => ['admin', 'personal'],
        'fields' => [
            'cliente_nombre' => ['label' => 'Cliente', 'type' => 'text', 'required' => true],
            'telefono' => ['label' => 'Telefono', 'type' => 'text'],
            'tipo' => ['label' => 'Tipo', 'type' => 'select_static', 'options' => ['Evento', 'Pedido']],
            'fecha_reserva' => ['label' => 'Fecha reserva', 'type' => 'date', 'required' => true],
            'hora_reserva' => ['label' => 'Hora', 'type' => 'time'],
            'mensaje_cliente' => ['label' => 'Mensaje del cliente', 'type' => 'textarea', 'required' => true],
            'respuesta_personal' => ['label' => 'Respuesta del personal', 'type' => 'textarea'],
            'estado' => ['label' => 'Estado', 'type' => 'select_static', 'options' => ['Pendiente', 'Confirmada', 'Rechazada']],
        ],
    ],
];

$moduleKey = $_GET['modulo'] ?? 'inicio';
$action = $_GET['accion'] ?? 'listar';
$message = '';

function canAccess(string $moduleKey, array $modules, string $rol): bool
{
    if ($rol === 'admin') {
        return true;
    }

    if (in_array($moduleKey, ['inicio', 'reportes', 'calendario'], true)) {
        return true;
    }

    return isset($modules[$moduleKey]) && in_array($rol, $modules[$moduleKey]['roles'] ?? ['admin'], true);
}

$visibleModules = array_filter($modules, fn($module) => in_array($rolActual, $module['roles'] ?? ['admin'], true));

if (!canAccess($moduleKey, $modules, $rolActual)) {
    $moduleKey = 'inicio';
    $message = 'Tu usuario no tiene permiso para entrar a ese modulo.';
}

function fetchOptions(PDO $pdo, string $table): array
{
    $labelColumns = [
        'clientes' => 'nombre',
        'personal' => 'nombre',
        'categorias' => 'nombre',
        'productos' => 'nombre',
        'pedidos' => 'id',
        'eventos' => 'nombre_evento',
        'ventas' => 'concepto',
        'chat_reservas' => 'cliente_nombre',
        'usuarios' => 'nombre',
    ];
    $label = $labelColumns[$table] ?? 'id';
    return $pdo->query("SELECT id, $label AS etiqueta FROM $table ORDER BY id DESC")->fetchAll();
}

function rowLabel(array $row, array $fields): string
{
    foreach (['nombre', 'nombre_evento', 'actividad', 'fecha_pago', 'fecha_pedido'] as $key) {
        if (isset($row[$key]) && $row[$key] !== '') {
            return (string) $row[$key];
        }
    }
    foreach (array_keys($fields) as $key) {
        if (isset($row[$key]) && $row[$key] !== '') {
            return (string) $row[$key];
        }
    }
    return 'Registro #' . ($row['id'] ?? '');
}

function inputValue(array $record, string $field): string
{
    return isset($record[$field]) ? (string) $record[$field] : '';
}

function saveRecord(PDO $pdo, array $module, ?int $id): void
{
    $fields = array_keys($module['fields']);
    $data = [];

    foreach ($fields as $field) {
        $value = $_POST[$field] ?? null;
        $data[$field] = $value === '' ? null : $value;
    }

    if ($module['table'] === 'usuarios') {
        if (!empty($data['clave'])) {
            $data['clave'] = password_hash((string) $data['clave'], PASSWORD_DEFAULT);
        } elseif ($id) {
            unset($data['clave']);
            $fields = array_values(array_filter($fields, fn($field) => $field !== 'clave'));
        }
    }

    if ($id) {
        $set = implode(', ', array_map(fn($field) => "$field = :$field", $fields));
        $sql = "UPDATE {$module['table']} SET $set WHERE id = :id";
        $data['id'] = $id;
        $pdo->prepare($sql)->execute($data);
        return;
    }

    $columns = implode(', ', $fields);
    $params = ':' . implode(', :', $fields);
    $sql = "INSERT INTO {$module['table']} ($columns) VALUES ($params)";
    $pdo->prepare($sql)->execute($data);
}

if (isset($modules[$moduleKey])) {
    $module = $modules[$moduleKey];

    if ($moduleKey === 'chat_reservas' && $action === 'confirmar' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM chat_reservas WHERE id = ?');
        $stmt->execute([(int) $_GET['id']]);
        $reserva = $stmt->fetch();

        if ($reserva) {
            $stmt = $pdo->prepare('SELECT id FROM clientes WHERE telefono = ? LIMIT 1');
            $stmt->execute([$reserva['telefono']]);
            $clienteId = $stmt->fetchColumn();

            if (!$clienteId) {
                $stmt = $pdo->prepare('INSERT INTO clientes (nombre, telefono, direccion) VALUES (?, ?, ?)');
                $stmt->execute([$reserva['cliente_nombre'], $reserva['telefono'], 'Registrado desde chat']);
                $clienteId = (int) $pdo->lastInsertId();
            }

            if ($reserva['tipo'] === 'Pedido') {
                $stmt = $pdo->prepare("INSERT INTO pedidos (cliente_id, fecha_pedido, fecha_entrega, direccion_entrega, estado, total) VALUES (?, CURDATE(), ?, ?, 'Nuevo', 0)");
                $stmt->execute([$clienteId, $reserva['fecha_reserva'], $reserva['mensaje_cliente']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO eventos (cliente_id, nombre_evento, tipo_evento, fecha_evento, lugar, estado, total) VALUES (?, ?, 'Otro', ?, ?, 'Confirmado', 0)");
                $stmt->execute([$clienteId, 'Reserva de ' . $reserva['cliente_nombre'], $reserva['fecha_reserva'], $reserva['mensaje_cliente']]);
                $eventoId = (int) $pdo->lastInsertId();
                $stmt = $pdo->prepare("INSERT INTO agenda (evento_id, fecha, hora, actividad, estado) VALUES (?, ?, ?, ?, 'Pendiente')");
                $stmt->execute([$eventoId, $reserva['fecha_reserva'], $reserva['hora_reserva'], 'Reserva confirmada desde chat']);
            }

            $stmt = $pdo->prepare("UPDATE chat_reservas SET estado = 'Confirmada', respuesta_personal = COALESCE(NULLIF(respuesta_personal, ''), 'Reserva confirmada y enviada al calendario') WHERE id = ?");
            $stmt->execute([(int) $_GET['id']]);
        }

        header('Location: index.php?modulo=chat_reservas&confirmada=1');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        saveRecord($pdo, $module, isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null);
        header('Location: index.php?modulo=' . urlencode($moduleKey) . '&guardado=1');
        exit;
    }

    if ($action === 'eliminar' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("DELETE FROM {$module['table']} WHERE id = ?");
        $stmt->execute([(int) $_GET['id']]);
        header('Location: index.php?modulo=' . urlencode($moduleKey) . '&eliminado=1');
        exit;
    }

    if (isset($_GET['guardado'])) {
        $message = 'Registro guardado correctamente.';
    }
    if (isset($_GET['eliminado'])) {
        $message = 'Registro eliminado correctamente.';
    }
    if (isset($_GET['confirmada'])) {
        $message = 'Reserva confirmada y enviada al calendario.';
    }
}

$counts = [];
foreach ($modules as $key => $module) {
    try {
        $counts[$key] = (int) $pdo->query("SELECT COUNT(*) FROM {$module['table']}")->fetchColumn();
    } catch (Throwable $e) {
        $counts[$key] = 0;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flores Alesli y Eventos</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<aside class="sidebar">
    <a class="brand" href="index.php">
        <span class="brand-mark">FA</span>
        <span>
            <strong>Flores Alesli</strong>
            <small><?= e($usuarioActual['nombre']) ?> · <?= e($rolActual) ?></small>
        </span>
    </a>
    <nav>
        <a class="<?= $moduleKey === 'inicio' ? 'active' : '' ?>" href="index.php">Inicio</a>
        <?php foreach ($visibleModules as $key => $item): ?>
            <a class="<?= $moduleKey === $key ? 'active' : '' ?>" href="index.php?modulo=<?= e($key) ?>">
                <span><?= e($item['icon']) ?></span><?= e($item['title']) ?>
            </a>
        <?php endforeach; ?>
        <a class="<?= $moduleKey === 'reportes' ? 'active' : '' ?>" href="index.php?modulo=reportes">📊 Reportes</a>
        <a class="<?= $moduleKey === 'calendario' ? 'active' : '' ?>" href="index.php?modulo=calendario">📆 Calendario</a>
        <a href="logout.php">Salir</a>
    </nav>
</aside>

<main class="content">
    <?php if ($moduleKey === 'inicio'): ?>
        <section class="hero">
            <div>
                <p class="eyebrow">Panel administrativo</p>
                <h1>Gestion de floreria, pedidos, eventos y pagos por QR.</h1>
                <p>Controla clientes, productos, inventario, agenda, pagos y reportes desde un solo lugar.</p>
            </div>
        </section>
        <section class="cards">
            <?php foreach ($visibleModules as $key => $item): ?>
                <a class="card" href="index.php?modulo=<?= e($key) ?>">
                    <span><?= e($item['icon']) ?></span>
                    <strong><?= e($item['title']) ?></strong>
                    <small><?= $counts[$key] ?> registros</small>
                </a>
            <?php endforeach; ?>
        </section>
    <?php elseif ($moduleKey === 'reportes'): ?>
        <?php
        $ventas = (float) $pdo->query("SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE estado = 'Pagado'")->fetchColumn();
        $ventasRegistradas = (float) $pdo->query("SELECT COALESCE(SUM(monto), 0) FROM ventas")->fetchColumn();
        $pendientes = (float) $pdo->query("SELECT COALESCE(SUM(monto), 0) FROM pagos WHERE estado = 'Pendiente'")->fetchColumn();
        $ventasPersonal = $pdo->query("SELECT COALESCE(p.nombre, 'Sin vendedor') AS vendedor, COUNT(v.id) AS cantidad, COALESCE(SUM(v.monto), 0) AS total FROM ventas v LEFT JOIN personal p ON p.id = v.personal_id GROUP BY p.nombre ORDER BY total DESC")->fetchAll();
        $stockBajo = $pdo->query("SELECT p.nombre, i.stock, i.stock_minimo FROM inventario i JOIN productos p ON p.id = i.producto_id WHERE i.stock <= i.stock_minimo")->fetchAll();
        $proximosEventos = $pdo->query("SELECT nombre_evento, fecha_evento, lugar, estado FROM eventos WHERE fecha_evento >= CURDATE() ORDER BY fecha_evento ASC LIMIT 8")->fetchAll();
        ?>
        <header class="page-header">
            <div>
                <p class="eyebrow">Analisis</p>
                <h1>Reportes</h1>
            </div>
        </header>
        <section class="metrics">
            <div><span>Ingresos pagados</span><strong>Bs <?= number_format($ventas, 2) ?></strong></div>
            <div><span>Ventas registradas</span><strong>Bs <?= number_format($ventasRegistradas, 2) ?></strong></div>
            <div><span>Pagos pendientes</span><strong>Bs <?= number_format($pendientes, 2) ?></strong></div>
            <div><span>Productos</span><strong><?= $counts['productos'] ?></strong></div>
            <div><span>Eventos</span><strong><?= $counts['eventos'] ?></strong></div>
        </section>
        <section class="grid-two">
            <div class="panel">
                <h2>Stock bajo</h2>
                <?php if (!$stockBajo): ?><p class="muted">No hay productos con stock bajo.</p><?php endif; ?>
                <?php foreach ($stockBajo as $row): ?>
                    <p class="list-row"><strong><?= e($row['nombre']) ?></strong><span><?= e((string) $row['stock']) ?> / min <?= e((string) $row['stock_minimo']) ?></span></p>
                <?php endforeach; ?>
            </div>
            <div class="panel">
                <h2>Proximos eventos</h2>
                <?php if (!$proximosEventos): ?><p class="muted">No hay eventos programados.</p><?php endif; ?>
                <?php foreach ($proximosEventos as $row): ?>
                    <p class="list-row"><strong><?= e($row['nombre_evento']) ?></strong><span><?= e($row['fecha_evento']) ?> · <?= e($row['estado']) ?></span></p>
                <?php endforeach; ?>
            </div>
            <div class="panel">
                <h2>Ventas por personal</h2>
                <?php if (!$ventasPersonal): ?><p class="muted">Todavia no hay ventas registradas.</p><?php endif; ?>
                <?php foreach ($ventasPersonal as $row): ?>
                    <p class="list-row"><strong><?= e($row['vendedor']) ?></strong><span><?= e((string) $row['cantidad']) ?> ventas · Bs <?= number_format((float) $row['total'], 2) ?></span></p>
                <?php endforeach; ?>
            </div>
        </section>
    <?php elseif ($moduleKey === 'calendario'): ?>
        <?php
        $itemsCalendario = [];
        $itemsCalendario = array_merge($itemsCalendario, $pdo->query("SELECT fecha, hora, actividad AS titulo, estado, 'Agenda' AS tipo FROM agenda ORDER BY fecha ASC, hora ASC")->fetchAll());
        $itemsCalendario = array_merge($itemsCalendario, $pdo->query("SELECT fecha_evento AS fecha, NULL AS hora, nombre_evento AS titulo, estado, 'Evento' AS tipo FROM eventos ORDER BY fecha_evento ASC")->fetchAll());
        $itemsCalendario = array_merge($itemsCalendario, $pdo->query("SELECT fecha_entrega AS fecha, NULL AS hora, CONCAT('Pedido #', id) AS titulo, estado, 'Pedido' AS tipo FROM pedidos WHERE fecha_entrega IS NOT NULL ORDER BY fecha_entrega ASC")->fetchAll());
        usort($itemsCalendario, fn($a, $b) => strcmp((string) $a['fecha'] . (string) $a['hora'], (string) $b['fecha'] . (string) $b['hora']));
        ?>
        <header class="page-header">
            <div>
                <p class="eyebrow">Reservas y entregas</p>
                <h1>Calendario</h1>
            </div>
        </header>
        <section class="calendar-list">
            <?php if (!$itemsCalendario): ?><div class="panel muted">Todavia no hay actividades en calendario.</div><?php endif; ?>
            <?php foreach ($itemsCalendario as $item): ?>
                <article class="calendar-item">
                    <time><?= e((string) $item['fecha']) ?> <?= e(substr((string) ($item['hora'] ?? ''), 0, 5)) ?></time>
                    <div>
                        <strong><?= e($item['titulo']) ?></strong>
                        <span><?= e($item['tipo']) ?> · <?= e($item['estado']) ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php elseif (isset($modules[$moduleKey])): ?>
        <?php
        $module = $modules[$moduleKey];
        $record = [];
        if ($action === 'editar' && isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM {$module['table']} WHERE id = ?");
            $stmt->execute([(int) $_GET['id']]);
            $record = $stmt->fetch() ?: [];
        }
        $rows = $pdo->query("SELECT * FROM {$module['table']} ORDER BY id DESC LIMIT 200")->fetchAll();
        ?>
        <header class="page-header">
            <div>
                <p class="eyebrow">Modulo</p>
                <h1><?= e($module['title']) ?></h1>
            </div>
            <a class="button secondary" href="index.php?modulo=<?= e($moduleKey) ?>">Nuevo</a>
        </header>

        <?php if ($message): ?><div class="notice"><?= e($message) ?></div><?php endif; ?>

        <section class="module-layout">
            <form class="panel form" method="post">
                <h2><?= $record ? 'Editar registro' : 'Nuevo registro' ?></h2>
                <input type="hidden" name="id" value="<?= e($record['id'] ?? '') ?>">
                <?php foreach ($module['fields'] as $field => $config): ?>
                    <label>
                        <span><?= e($config['label']) ?></span>
                        <?php if ($config['type'] === 'textarea'): ?>
                            <textarea name="<?= e($field) ?>" <?= !empty($config['required']) ? 'required' : '' ?>><?= e(inputValue($record, $field)) ?></textarea>
                        <?php elseif ($config['type'] === 'select'): ?>
                            <select name="<?= e($field) ?>" <?= !empty($config['required']) ? 'required' : '' ?>>
                                <option value="">Seleccionar</option>
                                <?php foreach (fetchOptions($pdo, $config['source']) as $option): ?>
                                    <option value="<?= e((string) $option['id']) ?>" <?= inputValue($record, $field) === (string) $option['id'] ? 'selected' : '' ?>>
                                        #<?= e((string) $option['id']) ?> - <?= e((string) $option['etiqueta']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ($config['type'] === 'select_static'): ?>
                            <select name="<?= e($field) ?>" <?= !empty($config['required']) ? 'required' : '' ?>>
                                <option value="">Seleccionar</option>
                                <?php foreach ($config['options'] as $option): ?>
                                    <option value="<?= e($option) ?>" <?= inputValue($record, $field) === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input name="<?= e($field) ?>" type="<?= e($config['type']) ?>" value="<?= e($config['type'] === 'password' ? '' : inputValue($record, $field)) ?>" step="<?= e($config['step'] ?? '') ?>" <?= (!empty($config['required']) || ($moduleKey === 'usuarios' && $field === 'clave' && !$record)) ? 'required' : '' ?>>
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
                <button class="button" type="submit">Guardar</button>
            </form>

            <section class="panel table-panel">
                <h2>Registros</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Detalle</th>
                            <?php foreach (array_keys($module['fields']) as $field): ?>
                                <th><?= e($module['fields'][$field]['label']) ?></th>
                            <?php endforeach; ?>
                            <?php if ($moduleKey === 'pagos'): ?><th>QR</th><?php endif; ?>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!$rows): ?>
                            <tr><td colspan="20" class="empty">Todavia no hay registros.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td>#<?= e((string) $row['id']) ?></td>
                                <td><strong><?= e(rowLabel($row, $module['fields'])) ?></strong></td>
                                <?php foreach (array_keys($module['fields']) as $field): ?>
                                    <td><?= $field === 'clave' ? '********' : e((string) ($row[$field] ?? '')) ?></td>
                                <?php endforeach; ?>
                                <?php if ($moduleKey === 'pagos'): ?>
                                    <?php $qrText = 'Flores Alesli | Pago #' . $row['id'] . ' | Bs ' . $row['monto'] . ' | Ref ' . $row['referencia_qr']; ?>
                                    <td><img class="qr" alt="QR pago" src="https://quickchart.io/qr?text=<?= urlencode($qrText) ?>&size=110"></td>
                                <?php endif; ?>
                                <td class="actions">
                                    <a href="index.php?modulo=<?= e($moduleKey) ?>&accion=editar&id=<?= e((string) $row['id']) ?>">Editar</a>
                                    <?php if ($moduleKey === 'chat_reservas' && ($row['estado'] ?? '') !== 'Confirmada'): ?>
                                        <a href="index.php?modulo=chat_reservas&accion=confirmar&id=<?= e((string) $row['id']) ?>">Confirmar</a>
                                    <?php endif; ?>
                                    <a class="danger" href="index.php?modulo=<?= e($moduleKey) ?>&accion=eliminar&id=<?= e((string) $row['id']) ?>" onclick="return confirm('Eliminar este registro?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </section>
    <?php else: ?>
        <section class="panel"><h1>Modulo no encontrado</h1></section>
    <?php endif; ?>
</main>
</body>
</html>