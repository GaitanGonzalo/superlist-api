# AGENTS.md

## Proyecto

Nombre: Comparador de Precios API

Tecnologías:

* Laravel 12
* PHP 8.3+
* MySQL
* JWT Authentication
* Cron Jobs / Scheduler
* Android Client
* Web Admin Panel

## Descripción

Esta API centraliza información de productos, precios y comercios.

La API es consumida por:

1. Aplicación Android para usuarios finales.
2. Panel Web Administrativo.

La API debe diseñarse para soportar múltiples provincias, ciudades, comercios y millones de registros históricos de precios.

---

# Principios Generales

## Objetivos

* Código limpio.
* Escalable.
* Seguro.
* Fácil de mantener.
* Optimizado para consultas masivas.

## Reglas

* Aplicar principios SOLID.
* Evitar duplicación de código (DRY).
* Priorizar seguridad sobre conveniencia.
* Mantener consistencia arquitectónica.
* Utilizar tipado estricto siempre que sea posible.

---

# Arquitectura

Estructura recomendada:

app/
├── Http/
│ ├── Controllers/
│ ├── Requests/
│ ├── Resources/
│ └── Middleware/
│
├── Models/
├── Services/
├── Actions/
├── Jobs/
├── DTOs/
├── Policies/
├── Enums/
├── Console/
│ └── Commands/
└── Utils/

---

# Flujo de responsabilidades

Controller
↓
Request
↓
Action
↓
Service
↓
Model

## Controllers

Responsabilidades:

* Recibir requests.
* Invocar Requests.
* Invocar Actions.
* Retornar Resources.

Los controladores NO deben contener:

* Lógica de negocio.
* Consultas complejas.
* Validaciones manuales.
* Procesamiento masivo.

---

# Requests

Toda validación debe realizarse mediante FormRequest.

Ejemplos:

* LoginRequest
* RegisterPriceRequest
* CreateProductRequest
* UpdateStoreRequest

No utilizar Validator::make() dentro de controladores salvo casos excepcionales.

---

# Actions

Cada caso de uso importante debe implementarse mediante una Action.

Ejemplos:

* RegisterPriceAction
* VerifyPriceAction
* NormalizeProductAction
* MergeProductsAction
* ReportPriceAction
* VerifyStoreAction

Las Actions representan casos de negocio.

---

# Services

Los Services contienen lógica reutilizable.

Ejemplos:

* ProductService
* StoreService
* PriceService
* LocationService
* StatisticsService

---

# Modelos del Dominio

Dominios principales:

* Auth
* Users
* Products
* Stores
* Prices
* Locations
* Reports
* Administration

---

# Productos

Los productos son entidades maestras compartidas.

Antes de crear un producto:

1. Buscar coincidencia por código de barras.
2. Buscar coincidencia por nombre normalizado.
3. Buscar coincidencia por marca.
4. Detectar posibles duplicados.

Nunca crear productos duplicados deliberadamente.

---

# Precios

Los precios son registros históricos.

Reglas:

* Nunca sobrescribir precios históricos.
* Cada modificación debe generar un nuevo registro.
* Mantener trazabilidad completa.

Correcto:

Producto X

$100
$120
$130

Incorrecto:

UPDATE prices SET value = 130

---

# Comercios

Los comercios son entidades maestras.

Reglas:

* Deben estar asociados a una ciudad.
* Pueden tener múltiples sucursales.
* Deben poder ser verificados desde administración.

---

# Ubicaciones

Jerarquía:

Provincia
↓
Ciudad
↓
Comercio

Nunca duplicar información geográfica innecesariamente.

Utilizar relaciones Eloquent.

---

# Autenticación

Sistema:

JWT

Headers esperados:

Authorization: Bearer {token}

Reglas:

* Nunca almacenar contraseñas en texto plano.
* Utilizar Hash::make().
* Validar permisos mediante middleware.
* Validar permisos mediante Policies cuando corresponda.

---

# Roles

Posibles roles:

## Android

* User
* Moderator

## Panel Web

* Admin
* Supervisor

Toda acción sensible debe validar permisos.

Nunca confiar en datos enviados por el cliente.

---

# Eloquent

Buenas prácticas:

Utilizar eager loading.

Correcto:

User::with('roles')->paginate();

Evitar:

foreach ($users as $user) {
$user->roles;
}

Evitar N+1 queries.

---

# API Resources

Nunca retornar modelos directamente.

Incorrecto:

return $product;

Correcto:

return ProductResource::make($product);

---

# Formato de Respuestas

Éxito:

{
"success": true,
"message": "Operation successful",
"data": {}
}

Error:

{
"success": false,
"message": "Validation error",
"errors": {}
}

Mantener formato consistente en toda la API.

---

# Scheduler y Cron

Toda tarea pesada debe ejecutarse mediante:

* Jobs
* Queues
* Scheduler

Nunca procesar tareas masivas durante requests HTTP.

Ejemplos:

* Normalización de productos.
* Verificación de duplicados.
* Limpieza de registros temporales.
* Recalculo de estadísticas.
* Procesamiento de reportes.

---

# Rendimiento

La aplicación debe soportar crecimiento masivo.

Reglas:

* Utilizar índices adecuados.
* Evitar consultas innecesarias.
* Utilizar paginación.
* Utilizar eager loading.
* Evitar get() sobre tablas masivas.
* Preferir paginate() o cursorPaginate().

---

# Base de Datos

## Convenciones

Tablas:

snake_case plural.

Ejemplos:

users
products
stores
prices

Campos:

snake_case

Ejemplos:

created_at
updated_at
product_id

---

# Migraciones

Reglas:

* Nunca modificar migraciones ejecutadas.
* Crear nuevas migraciones para cambios.
* Mantener compatibilidad hacia atrás cuando sea posible.

---

# Seguridad

Obligatorio:

* Validar todas las entradas.
* Sanitizar datos cuando aplique.
* Utilizar consultas parametrizadas.
* Utilizar middleware de autenticación.
* Utilizar autorización por roles.

Prohibido:

* SQL dinámico.
* Concatenación insegura.
* Exponer datos sensibles.
* Deshabilitar controles de seguridad sin justificación.

---

# Logging

Utilizar:

Log::info()
Log::warning()
Log::error()
$request->user()

No utilizar:

dd()
dump()
var_dump()
auth('api')->id()
auth()->id()
en producción.

---

# Testing

Framework:

PHPUnit

Toda nueva funcionalidad importante debe incluir pruebas.

Tipos:

* Feature Tests
* Unit Tests

---

# Convenciones para Agentes IA

Antes de generar código:

1. Buscar implementaciones existentes.
2. Reutilizar Services existentes.
3. Reutilizar Actions existentes.
4. Mantener consistencia arquitectónica.
5. No crear archivos innecesarios.
6. No introducir dependencias sin justificación.
7. Respetar Laravel 12.
8. Respetar JWT existente.
9. Considerar compatibilidad Android.
10. Considerar compatibilidad Panel Web.
11. Priorizar seguridad.
12. Priorizar escalabilidad.

Cuando existan varias alternativas:

* Elegir la más mantenible.
* Elegir la más segura.
* Elegir la más escalable.

---

# Regla Fundamental del Negocio

Los productos son entidades maestras.

Los comercios son entidades maestras.

Los precios son eventos históricos.

Los usuarios generan contribuciones sobre esos datos.

Toda nueva funcionalidad debe respetar este principio.
