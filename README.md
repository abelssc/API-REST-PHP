
# API RESTFUL
Este proyecto se enfoco en hacer una copia del api REST en GITHUB "JSON Server".
La diferencia principal es que este **proyecto se baso en PHP**.

* <a href="#configuracion">CONFIGURACION</a>
* <a href="#rutas">RUTAS</a>
    * <a href="#include">INCLUDE</a>
    * <a href="#exclude">EXCLUDE</a>
    * <a href="#like">LIKE</a>
    * <a href="#operator">OPERATOR</a>
    * <a href="#sort">SORT</a>
    * <a href="#paginate">PAGINATE</a>
    * <a href="#relation">RELATION</a>


## <span id="configuracion">CONFIGURACION</span>
* Usar un servidor apache (`xampp, lampp, laragon`)
* Crear una BBDD con el gestior de su preferencia

* En el archivo `chatApp/config/conexion.php` debe editar las siguientes constantes
```php
const HOST="localhost";
const USER="root";
const PASSWORD="";
const DATABASE="yourdatabase";
```
* LA BBDD por defecto es mysql, puede editarla en el archivo `chatApp/config/conexion.php`
```php 
$dsn= "mysql:host=..."
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
SELECT * FROM posts WHERE id=1 and id=2
```
La segunda sentencia varia respecto a JSON.SERVER (JSON SERVER USA EL OPERADOR or para la sintaxis SQL cuando hay variables iguales)
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


## <span id="sort">SORT</span>
Add `_sort` and `_order` (ascending order by default)
```
GET /posts?_sort=views&_order=asc
GET /posts/1/comments?_sort=votes&_order=asc
```
La sentencia SQL para SORT es
```sql
SELECT * FROM posts ORDER BY views asc
SELECT * FROM posts WHERE id='1' ORDER BY votes asc
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

## <span id="relation">RELACION</span>
To include children resources, add `_embed`
```
GET /posts?_embed=comments
GET /posts/1?_embed=comments
```
La sentencia SQL para `_embed` es
```sql
SELECT * FROM posts LEFT JOIN comments ON posts.id=comments.postsId
SELECT * FROM posts LEFT JOIN comments ON posts.id=comments.postsId WHERE posts.id='1'
```
