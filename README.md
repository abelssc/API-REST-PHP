
# API RESTFUL
Este proyecto se enfoco en hacer una copia del api REST en GITHUB "JSON Server".
La diferencia principal es que este **proyecto se baso en PHP**.

* <a href="#configuracion">CONFIGURACION</a>
* <a href="#rutas">RUTAS</a>
    * <a href="#include">INCLUDE</a>
    * <a href="#exclude">EXCLUDE</a>
    * <a href="#like">LIKE</a>
    * <a href="#operator">OPERATOR</a>
    * <a href="#order">ORDER</a>
    * <a href="#paginate">PAGINATE</a>
    * <a href="#relation">RELATION</a>


## <span id="configuracion">CONFIGURACION</span>
* Usar un servidor apache (`xampp, lampp, laragon`)
* Crear una BBDD MySql

* En el archivo `API-REST-PHP/config/conexion.php` debe editar las siguientes constantes
```php
const HOST="localhost";
const USER="root";
const PASSWORD="";
const DATABASE="yourdatabase";
```
* LA BBDD por defecto es mysql, puede colocar otro gestor pero debe configurar las peticiones PHP. puede editarla en el archivo `API-REST-PHP/models/[get|post|put|delete].php`
* El nombre del servidor por defecto es `API-REST-PHP` si la editas deberas cambiar las siguientes rutas
```php
## linea 09 del archivo index.php 
require $_SERVER["DOCUMENT_ROOT"]."/API-REST-PHP/config/dirs.php";
## linea 02 del archivo config/dirs.php
define("SERVER_NAME","API-REST-PHP");
```
## <span id="rutas">RUTAS</span>
```
GET    /posts
GET    /posts/1
POST   /posts
PUT    /posts/1
PATCH  /posts/1
DELETE /posts/1
```
## <span id="include">INCLUDE</span>
```
GET /posts?title=json-server&author=typicode
GET /posts?id=1&id=2
```
La sentencia SQL ara include es
```sql
SELECT * FROM posts WHERE title='json-server' and author='typicode';
SELECT * FROM posts WHERE id=1 or id=2
```
<!-- La segunda sentencia varia respecto a JSON.SERVER (JSON SERVER USA EL OPERADOR or para la sintaxis SQL cuando hay variables iguales) -->
## <span id="exclude">EXCLUDE</span>
Agregar `_ne` para excuir un valor
```
GET /posts?id_ne=1
```
La sentencia SQL para exclude es
```sql
SELECT * FROM posts WHERE id<>'1'
```
## <span id="like">LIKE</span>
Agregar `_like` para busquedas like en sql
```
GET /posts?title_like=server
```
La sentencia SQL para like es
```sql
SELECT * FROM posts WHERE title LIKE '%server%'
```

## <span id="operator">OPERADOR ">=" and "=<"</span>
Add `_gte` or `_lte` for getting a range.
* gte: Greater Than Equal
* lte: Low Than Equal

```
GET /posts?views_gte=10&views_lte=20
```
La sentencia SQL para los operadores es
```sql
SELECT * FROM posts WHERE views>10 and views<20
```


## <span id="order">ORDER</span>
Add  `_order`
```
GET /posts?_order=views,desc
GET /posts/?_order=views,desc&_order=id,asc
```
La sentencia order funciona diferente a JSON SERVER

La columna y el orden son requeridos.

La sentencia SQL para order es
```sql
SELECT * FROM posts ORDER BY views desc
SELECT * FROM posts ORDER BY views desc,id asc
```

## <span id="paginate">PAGINATE</span>
Use `_page` and optionally `_limit` to paginate returned data.

```
GET /posts?_page=7
GET /posts?_page=7&_limit=20
```
La sentencia SQL para PAGINATE es
```sql
SELECT * FROM posts LIMIT 70,10
SELECT * FROM posts LIMIT 140,20
```
* El primer argumento de LIMIT es page*limit (que indica el inicio desde donde tomara los registros).
* El segundo argumento es limit ( que indica la cantidad de argumentos que tomará, iniciando en el inicio registrado en el primer argumento).

10 items are returned by default

## <span id="relation">RELACION "AUN NO DISPONIBLE"</span>
Para hacer union de tablas se necesita seguir un formato especifico al nombrar los primary y foreing key.
```
PRIMARY KEY: id
FOREIGN KEY: nombreTabla_id
```

To include children resources, add `_embed`
```
GET /posts?_embed=comments
GET /posts/1?_embed=comments
```
La sentencia SQL para `_embed` es
```sql
SELECT * FROM posts LEFT JOIN comments ON posts.id=comments.id
SELECT * FROM posts LEFT JOIN comments ON posts.id=comments.id WHERE posts.id='1'
```
