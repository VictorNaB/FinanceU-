# FinanceU

Aplicación web en **PHP + MySQL** para la gestión financiera de estudiantes universitarios. Permite registrar ingresos y gastos, definir **metas** de ahorro, crear **recordatorios**, visualizar **dashboard** con gráficas y consultar un **análisis semanal** del comportamiento de gastos.

> Estructura tipo MVC ligera (modelo/, controlador/, vista/) y frontend con HTML/CSS/JS. Usa **Chart.js** y **Font Awesome** en la interfaz.

---

## ✨ Funcionalidades

- **Autenticación de usuarios** (registro, login, cierre de sesión).
- **Dashboard** con indicadores y gráficas (Chart.js).
- **Transacciones**: crear, listar, modificar y eliminar _ingresos/gastos_ por categoría/tipo.
- **Metas**: crear metas con monto objetivo, fecha límite y avance acumulado.
- **Recordatorios**: creación y gestión de recordatorios (con tipos y recurrencia).
- **Análisis semanal**: cálculo/resumen con apoyo del modelo `AnalisisSemanal`.
- **Panel de administración** (usuarios, roles, etc.).

---

## 🧱 Estructura del proyecto

```text
FinanceU-/
├── config/
│   └── config.php
├── controlador/
│   ├── ControladorAdministrador.php
│   ├── ControladorMetas.php
│   ├── ControladorPerfil.php
│   ├── ControladorRecordatorios.php
│   ├── ControladorTransaccion.php
│   └── controladorUsuario.php
├── Database/
│   └── financeu.sql
├── modelo/
│   ├── AnalisisSemanal.php
│   ├── conexion.php
│   ├── Dashboard.php
│   ├── Meta(s).php
│   ├── Recordatorios.php
│   ├── Transaccion.php
│   └── (otros modelos)
├── vista/
│   ├── css/styles.css
│   ├── js/script.js
│   ├── img/...
│   ├── administrador.php
│   ├── analisis.php
│   ├── app.php
│   ├── calendario.php
│   ├── dashboard.php
│   ├── index.php
│   ├── Login.php
│   ├── metas.php
│   ├── perfil.php
│   ├── RecordatorioVista.php
│   ├── Registrar.php
│   └── transacciones.php
└── index.php
```

Archivos/Carpetas clave:
- `config/config.php` → Configuración de conexión (host, usuario, contraseña, base).
- `modelo/conexion.php` → Clase `Conexion` (mysqli) que centraliza la conexión a MySQL.
- `Database/financeu.sql` → Script SQL con el esquema y tablas.
- `vista/` → Vistas PHP + assets (`css/`, `js/`, `img/`).
- `controlador/` → Lógica de casos de uso (Transacciones, Metas, Recordatorios, Perfil, etc.).
- `index.php` y `vista/index.php` → Entrada pública y landing.

---

## 🗄️ Base de datos

Importa el script `Database/financeu.sql` en tu servidor MySQL. Crea las tablas principales:

```
Universidad, roles, Usuarios, Accesos, TiposRecordatorio, Recurrente, TiposTransaccion, CategoriaTransaccion, Metas, Recordatorios, Transaccion, EstadisticasUso, AnalisisSemanal
```

> **Nombre por defecto de la base** en `config/config.php`: `financeu2`. Cámbialo si usas otro nombre.

---

## 🚀 Requisitos

- PHP 8.x (recomendado) con extensiones `mysqli` habilitadas.
- Servidor web (Apache, Nginx) configurado para servir PHP.
- MySQL 5.7+ o MariaDB 10.4+.
- Acceso a Internet para CDNs de **Chart.js** y **Font Awesome** (o instálalos localmente).

---

## 🔧 Instalación y puesta en marcha

1. **Clona o copia** este repositorio dentro del _document root_ de tu servidor (por ejemplo, `htdocs/FinanceU-`).  
2. Crea una **base de datos** en MySQL (ej. `financeu2`).  
3. **Importa** `Database/financeu.sql` en la base creada.  
4. Asegúrate de que tu servidor ejecute el **directorio raíz** del proyecto y que PHP tenga permisos para **sesiones** y escritura si lo requieres.
5. Abre en el navegador: `http://localhost/FinanceU-/`

> **Rutas útiles** (según vistas/controladores):
- Landing/Login/Registro: `vista/index.php` (o `/?action=mostrarLogin`, `/?action=mostrarRegistro`).
- App principal (sidebar + contenido): `vista/app.php?page=dashboard`  
  Páginas permitidas: `dashboard`, `transacciones`, `perfil`, `metas`, `analisis`, `calendario`.

---

## 📚 Tecnologías

- **Backend**: PHP (mysqli).
- **BD**: MySQL/MariaDB.
- **Frontend**: HTML5, CSS3, JavaScript, **Chart.js**, **Font Awesome**.
- **Patrón**: MVC simplificado.

---

## 🧪 Datos de prueba (opcional)

- Revisa `Database/financeu.sql` para insertar **roles**, **tipos de transacción**, **categorías** y, si aplica, un usuario inicial de pruebas.
- Si no existen, crea uno desde `Registrar.php` y luego inicia sesión desde `Login.php`.

---

## 🛠️ Desarrollo

- Controladores principales:
  - `ControladorTransaccion.php`: CRUD de transacciones, soporte a _amount/monto_, _date/fecha_ y helpers.
  - `ControladorMetas.php`: creación, actualización de progreso y estadística de metas.
  - `ControladorRecordatorios.php`: gestión de recordatorios con tipos y recurrencia.
- Modelos relacionados en `modelo/`: `Transaccion.php`, `Metas.php`, `Recordatorios.php`, `AnalisisSemanal.php`, `Dashboard.php`, etc.
- Vistas en `vista/` con **Chart.js** embebido y estilos en `vista/css/styles.css`.

---

## 🔒 Seguridad básica (sugerida)

- Sanitiza entradas (`$_POST`, `$_GET`) y valida tipos.
- Usa **sentencias preparadas** (ya presentes en varios modelos) para evitar SQLi.
- Protege rutas de la app detrás de **sesiones** y valida el rol del usuario.

---
