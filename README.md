# EcoRuedas API — U1 EP2

API RESTful en PHP (PDO) sobre MySQL, desplegada en una VM de Oracle Cloud Infrastructure (OCI).

## Endpoints

| Método | Ruta                     | Descripción                     |
|--------|--------------------------|----------------------------------|
| GET    | /api/v1/clientes         | Lista todos los clientes         |
| GET    | /api/v1/clientes/{id}    | Obtiene un cliente por ID        |
| POST   | /api/v1/clientes         | Crea un nuevo cliente            |
| PUT    | /api/v1/clientes/{id}    | Actualiza un cliente existente   |
| DELETE | /api/v1/clientes/{id}    | Elimina un cliente               |

## URL base en producción
http://40.233.9.231/api/v1/clientes

## Configuración
Las credenciales de la base de datos se leen de variables de entorno (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`), nunca se escriben en el código fuente.

## Pruebas
Importa `EcoRuedas_API.postman_collection.json` en Postman o Thunder Client para probar los 5 endpoints.
