---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

> Client id: 1 

> Client secret: doJ0zQ1gmhP33BZ9paJZJksZxR5SICYj4NIQGraV

> Access Token example: eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjFiNjM2MWE2MDkzYjRiNzMwZWYxMTY1YzIxODA1ZmYxYWFjNWZhN2JlMjRlMzliZGQ2Yjk2NTA1OTcxZDZjMDgxNjE3Y2Y2YzBlYjUwMWRhIn0.eyJhdWQiOiIxIiwianRpIjoiMWI2MzYxYTYwOTNiNGI3MzBlZjExNjVjMjE4MDVmZjFhYWM1ZmE3YmUyNGUzOWJkZDZiOTY1MDU5NzFkNmMwODE2MTdjZjZjMGViNTAxZGEiLCJpYXQiOjE1NjgwNTAwMDEsIm5iZiI6MTU2ODA1MDAwMSwiZXhwIjoxNTk5NjcyNDAxLCJzdWIiOiIiLCJzY29wZXMiOltdfQ.I-zsnSQ7kvBgwYos9vcvsjsZoRubdhtyxLlSGXhIfO5FVD0qBf2OuxpTrTFaCzZuSt0xaZjBNbRxKC8YfZou4wY0HTFsquz7nfRTBSnyG1O1oI1RkJq3H9MHNdZSASyxd90SzD-hUN_erkQGV2Zx3QJcwBWbBrVrtuxP-VpeeHh2g3X9PnG5GnR5i7mkhFbPSVI6gYQvgbRvXcEMCGHt2ifKYC3cAr43cHUrNDQphYEesD9AxRgdruikVBQ3ZKFSi1Ax80Kr-iPrgaOIQMc17mQZK18x3jfsNNpFgQMWzcaUvJdF60G-DemLQHmnj3CEjSQt42vtwupHMsABGji_HFC0u26F1yuh2FcX1iVQ59UJ2bajYiWuudJt8PawVv0E2OZlb2AWJHa2Hpmt6ZX_TBYMRbuIWdRAU0UOUC5vbsf6tl4dwjAwig36LllWDNGaGZozK4DCyepOHpml35vBn1C9ju5KBKmZygGcULgPN7ehuxMCos8vRleHGx2qaXAZUiwPT55DLI_XjTqQe1R-qxDxqAWws6at0CnM4hiMj5VEl9ptvhzYpVJbd9ytlMV4rVS3woFdAz4APFopHx-nGtno5bbJCheL0NwkLD9JldR--MuZscx2NRhvdiqAkinryqQo3eaBqblTLT9J8z_U4kwwph-X5r_4dNngy1SlUYQ

Agregar en los headers de cada request 

"Authorization" : 'Bearer ' + clientToken
<!-- END_INFO -->

#general


<!-- START_30f6439aadb9bb2fecbba2f85ace6cb8 -->
## /warehouses/getall

> Example request:

```bash
curl -X GET -G "/api/v1/warehouses/getall" 
```

```javascript
const url = new URL("/api/v1/warehouses/getall");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET /api/v1/warehouses/getall`

Obtiene todas las ubicaciones de todas las bodegas
<!-- END_30f6439aadb9bb2fecbba2f85ace6cb8 -->

<!-- START_411c0de0e86583bcccfb9ccfb4fd376f -->
## /warehouselocations/getracks

> Example request:

```bash
curl -X GET -G "/api/v1/warehouselocations/getracks?warehouse_id=1" 
```

```javascript
const url = new URL("/api/v1/warehouselocations/getracks?warehouse_id=1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
{
    "current_page": 1,
    "data": [
        {
            "rack": "1"
        },
        {
            "rack": "2"
        },
        {
            "rack": "3"
        },
        {
            "rack": "4"
        }
    ],
    "first_page_url": "http://ec2-34-219-142-13.us-west-2.compute.amazonaws.com/api/v1/warehouselocations/getracks?page=1",
    "from": 1,
    "last_page": 7,
    "last_page_url": "http://ec2-34-219-142-13.us-west-2.compute.amazonaws.com/api/v1/warehouselocations/getracks?page=7",
    "next_page_url": "http://ec2-34-219-142-13.us-west-2.compute.amazonaws.com/api/v1/warehouselocations/getracks?page=2",
    "path": "http://ec2-34-219-142-13.us-west-2.compute.amazonaws.com/api/v1/warehouselocations/getracks",
    "per_page": 15,
    "prev_page_url": null,
    "to": 4,
    "total": 96
}
```

### HTTP Request
`GET /api/v1/warehouselocations/getracks`

Obtiene la lista de racks de determinada bodega
<!-- END_411c0de0e86583bcccfb9ccfb4fd376f -->

<!-- START_c0e3b739cb2591c076db77575b773ae7 -->
## /warehouselocations/getblocks

> Example request:

```bash
curl -X GET -G "/api/v1/warehouselocations/getblocks?warehouse_id=1&rack=1" 
```

```javascript
const url = new URL("/api/v1/warehouselocations/getblocks?warehouse_id=1&rack=1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
[
    {
        "id": 1,
        "rack": "1",
        "block": "1",
        "level": "1",
        "side": "1",
        "mapped_string": "R1-A1-N1",
        "items_count": 4
    },
    {
        "id": 2,
        "rack": "1",
        "block": "1",
        "level": "1",
        "side": "2",
        "mapped_string": "R1-B1-N1",
        "items_count": 5
    },
    ...
]
```

### HTTP Request
`GET /api/v1/warehouselocations/getblocks`

Obtiene todos los bloques de determinada bodega y determinado rack.
<!-- END_c0e3b739cb2591c076db77575b773ae7 -->

<!-- START_88b6fb6afa695825f2751a803791653b -->
## /warehouselocations/getall

> Example request:

```bash
curl -X GET -G "/api/v1/warehouselocations/getall?warehouse_id=1" 
```

```javascript
const url = new URL("/api/v1/warehouselocations/getall?warehouse_id=1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "warehouse_id": 1,
            "rack": "1",
            "block": "1",
            "level": "1",
            "side": "1",
            "mapped_string": "R1-A1-N1",
            "created_at": "2019-09-02 22:14:56",
            "updated_at": "2019-09-02 22:14:56",
            "warehouse": {
                "id": 1,
                "name": "Bodega1",
                "store_id": 1,
                "created_at": null,
                "updated_at": null,
                "store": {
                    "id": 1,
                    "name": "CENTRO DE DISTRIBUCION",
                    "number": "10060",
                    "created_at": "2018-05-07 22:04:54",
                    "updated_at": "2018-05-07 22:04:54"
                }
            }
        },
        ...
    ],
}
```

### HTTP Request
`GET /api/v1/warehouselocations/getall?warehouse_id=1`

Obtiene las ubicaciones de una bodega.
<!-- END_88b6fb6afa695825f2751a803791653b -->

<!-- START_e8c7529678b88c98b14609d860bab431 -->
## /locationvariation/getall

> Example request:

```bash
curl -X GET -G "/api/v1/locationvariation/getall" 
```

```javascript
const url = new URL("/api/v1/locationvariation/getall");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "warehouselocation_id": 28,
            "variation_id": 15955,
            "created_at": "2019-09-02 17:35:44",
            "updated_at": "2019-09-02 17:35:44",
            "variation": {
                "id": 15955,
                "name": "XGD",
                "sku": "1921176",
                "product_id": 3714,
                "product": {
                    "id": 3714,
                    "name": "Playera hombros descubiertos",
                    "images": [
                        {
                            "id": 24667,
                            "created_at": "2018-04-23 22:02:04",
                            "updated_at": "2018-04-23 22:02:04",
                            "file": "325ade57db14d47.jpg",
                            "product_id": 3714,
                            "order": 1,
                            "color_id": 0
                        },
                        {
                            "id": 24668,
                            "created_at": "2018-04-23 22:02:05",
                            "updated_at": "2018-04-23 22:02:05",
                            "file": "325ade57dc87c65.jpg",
                            "product_id": 3714,
                            "order": 2,
                            "color_id": 0
                        },
                        {
                            "id": 24669,
                            "created_at": "2018-04-23 22:02:07",
                            "updated_at": "2018-04-23 22:02:07",
                            "file": "325ade57deb62d4.jpg",
                            "product_id": 3714,
                            "order": 3,
                            "color_id": 0
                        },
                        {
                            "id": 24670,
                            "created_at": "2018-04-23 22:02:08",
                            "updated_at": "2018-04-23 22:02:08",
                            "file": "325ade57e005765.jpg",
                            "product_id": 3714,
                            "order": 4,
                            "color_id": 0
                        },
                        {
                            "id": 24671,
                            "created_at": "2018-04-23 22:02:10",
                            "updated_at": "2018-04-23 22:02:10",
                            "file": "325ade57e16561d.jpg",
                            "product_id": 3714,
                            "order": 5,
                            "color_id": 0
                        }
                    ]
                }
            },
        },
        ...
    ]

```

### HTTP Request
`GET /api/v1/locationvariation/getall`

Obtiene todas las posiciones de todos los items con sus detalles.

<!-- END_e8c7529678b88c98b14609d860bab431 -->

<!-- START_6535521a9edba040fd8ddc11f6c03c7e -->
## /locationvariation/getitemsinlocation

> Example request:

```bash
curl -X GET -G "/api/v1/locationvariation/getitemsinlocation?warehouse_id=1&mapped_string=R1-A1-N1" 
```

```javascript
const url = new URL("/api/v1/locationvariation/getitemsinlocation?warehouse_id=1&mapped_string=R1-A1-N1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
{
    "current_page": 1,
    "data": [
        {
            "id": 27,
            "warehouselocation_id": 1,
            "variation_id": 25773,
            "created_at": "2019-09-02 17:35:44",
            "updated_at": "2019-09-02 17:35:44",
            "variation": {
                "id": 25773,
                "name": "XGD",
                "sku": "1940121",
                "product_id": 5729,
                "product": {
                    "id": 5729,
                    "name": "Paquete Boxer Corto",
                    "images": [
                        {
                            "id": 25489,
                            "created_at": "2018-04-25 19:04:25",
                            "updated_at": "2018-04-25 19:04:25",
                            "file": "325ae0d138af48c.jpg",
                            "product_id": 5729,
                            "order": 1,
                            "color_id": 0
                        },
                        {
                            "id": 25490,
                            "created_at": "2018-04-25 19:04:26",
                            "updated_at": "2018-04-25 19:04:26",
                            "file": "325ae0d139ed215.jpg",
                            "product_id": 5729,
                            "order": 2,
                            "color_id": 0
                        },
                        {
                            "id": 25491,
                            "created_at": "2018-04-25 19:04:28",
                            "updated_at": "2018-04-25 19:04:28",
                            "file": "325ae0d13b45cc4.jpg",
                            "product_id": 5729,
                            "order": 3,
                            "color_id": 0
                        },
                        {
                            "id": 25492,
                            "created_at": "2018-04-25 19:04:29",
                            "updated_at": "2018-04-25 19:04:29",
                            "file": "325ae0d13c7b533.jpg",
                            "product_id": 5729,
                            "order": 4,
                            "color_id": 0
                        }
                    ]
                }
            },
            "warehouselocation": {
                "id": 1,
                "mapped_string": "R1-A1-N1",
                "warehouse_id": 1
            }
        },
        ...
    ],
}
```

### HTTP Request
`GET /api/v1/locationvariation/getitemsinlocation`

Obtiene los items posicionados en determinada ubicacion.

<!-- END_6535521a9edba040fd8ddc11f6c03c7e -->

<!-- START_b96feadb8f9229dfd6196e44954067d1 -->
## /warehouses/store

> Example request:

```bash
curl -X POST "/api/v1/warehouses/store?name=bodega1&store_id=1" 
```

```javascript
const url = new URL("/api/v1/warehouses/store&store_id=1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST /api/v1/warehouses/store`

Crea una bodega con el nombre y el identificador de tienda a la que pertenece.
<!-- END_b96feadb8f9229dfd6196e44954067d1 -->

<!-- START_07e4dafef70ffd2ec75eb8cf2253e5b9 -->
## /warehouselocations/maplocations

> Example request:

```bash
curl -X POST "/api/v1/warehouselocations/maplocations?blocks=3&levels=4&sides=2&warehouse_id=1" 
```

```javascript
const url = new URL("/api/v1/warehouselocations/maplocations?blocks=3&levels=4&sides=2&warehouse_id=1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST /api/v1/warehouselocations/maplocations`

Mapea ubicaciones (crea un nuevo rack) en la bodega y con las propiedades declaradas.

<!-- END_07e4dafef70ffd2ec75eb8cf2253e5b9 -->

<!-- START_9adc1c75574cb4ca031eb54b75d0ee28 -->
## /locationvariation/locateitemscan

> Example request:

```bash
curl -X POST "/api/v1/locationvariation/locateitemscan?warehouse_id=1&sku=1861705&mapped_string&R1-A1-N1" 
```

```javascript
const url = new URL("/api/v1/locationvariation/locateitemscan?warehouse_id=1&sku=1861705&mapped_string&R1-A1-N1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST /api/v1/locationvariation/locateitemscan`

Posiciona el item en la ubicacion determinada utilizando scanner.
<!-- END_9adc1c75574cb4ca031eb54b75d0ee28 -->

<!-- START_c93a7b2f6b31220f030c3ac2f45968c3 -->
## /locationvariation/moveitemscan

> Example request:

```bash
curl -X POST "/api/v1/locationvariation/moveitemscan?warehouse_id_to=1&warehouse_id_from=1&sku=1861705&mapped_string_from=R4-A2-N4&mapped_string_to=R1-A2-N1" 
```

```javascript
const url = new URL("/api/v1/locationvariation/moveitemscan?warehouse_id_to=1&warehouse_id_from=1&sku=1861705&mapped_string_from=R4-A2-N4&mapped_string_to=R1-A2-N1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST /api/v1/locationvariation/moveitemscan`

Mueve el item de la ubicacion FROM a la ubicacion TO 
<!-- END_c93a7b2f6b31220f030c3ac2f45968c3 -->

<!-- START_6ff4980ddbaa6b169ce2b6550a730c0e -->
## Ubica el item en la determinada ubicacion utilizando la web.

> Example request:

```bash
curl -X POST "/api/v1/locationvariation/locateitemweb" 
```

```javascript
const url = new URL("/api/v1/locationvariation/locateitemweb");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST /api/v1/locationvariation/locateitemweb`


<!-- END_6ff4980ddbaa6b169ce2b6550a730c0e -->

<!-- START_a377d0f77ba54be5a2d292836fbadc12 -->
## Mueve el item de ubicacion utilizando la web.

> Example request:

```bash
curl -X POST "/api/v1/locationvariation/moveitemweb" 
```

```javascript
const url = new URL("/api/v1/locationvariation/moveitemweb");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST /api/v1/locationvariation/moveitemweb`


<!-- END_a377d0f77ba54be5a2d292836fbadc12 -->

<!-- START_a5b388545b4728f2dc015bdcd90eea65 -->
## Authorize a client to access the user&#039;s account.

> Example request:

```bash
curl -X GET -G "/oauth/authorize" 
```

```javascript
const url = new URL("/oauth/authorize");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET /oauth/authorize`


<!-- END_a5b388545b4728f2dc015bdcd90eea65 -->

<!-- START_c2d8dbfa8015e84d618061d6d7841095 -->
## Approve the authorization request.

> Example request:

```bash
curl -X POST "/oauth/authorize" 
```

```javascript
const url = new URL("/oauth/authorize");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST /oauth/authorize`


<!-- END_c2d8dbfa8015e84d618061d6d7841095 -->

<!-- START_b0ad08a7d291b53ff4dc8106378488c5 -->
## Deny the authorization request.

> Example request:

```bash
curl -X DELETE "/oauth/authorize" 
```

```javascript
const url = new URL("/oauth/authorize");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE /oauth/authorize`


<!-- END_b0ad08a7d291b53ff4dc8106378488c5 -->

<!-- START_9a4925a3d6314fb381b0093b9d14a6ef -->
## Authorize a client to access the user&#039;s account.

> Example request:

```bash
curl -X POST "/oauth/token" 
```

```javascript
const url = new URL("/oauth/token");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST /oauth/token`


<!-- END_9a4925a3d6314fb381b0093b9d14a6ef -->

<!-- START_d54562e90bf04151dc7d95837e558b77 -->
## Get all of the authorized tokens for the authenticated user.

> Example request:

```bash
curl -X GET -G "/oauth/tokens" 
```

```javascript
const url = new URL("/oauth/tokens");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET /oauth/tokens`


<!-- END_d54562e90bf04151dc7d95837e558b77 -->

<!-- START_abfd09e36c4951d11ca1c7d8d30d5c4d -->
## Delete the given token.

> Example request:

```bash
curl -X DELETE "/oauth/tokens/1" 
```

```javascript
const url = new URL("/oauth/tokens/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE /oauth/tokens/{token_id}`


<!-- END_abfd09e36c4951d11ca1c7d8d30d5c4d -->

<!-- START_22ac64c785e062e9fb762f3d39a3618a -->
## Get a fresh transient token cookie for the authenticated user.

> Example request:

```bash
curl -X POST "/oauth/token/refresh" 
```

```javascript
const url = new URL("/oauth/token/refresh");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST /oauth/token/refresh`


<!-- END_22ac64c785e062e9fb762f3d39a3618a -->

<!-- START_e68f5945e5b3115f409c97d70ce109c1 -->
## Get all of the clients for the authenticated user.

> Example request:

```bash
curl -X GET -G "/oauth/clients" 
```

```javascript
const url = new URL("/oauth/clients");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET /oauth/clients`


<!-- END_e68f5945e5b3115f409c97d70ce109c1 -->

<!-- START_cc62a849d5b8a0f6c7fe5bf8f37dca37 -->
## Store a new client.

> Example request:

```bash
curl -X POST "/oauth/clients" 
```

```javascript
const url = new URL("/oauth/clients");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST /oauth/clients`


<!-- END_cc62a849d5b8a0f6c7fe5bf8f37dca37 -->

<!-- START_68765e4a9497ffd5ca366e85432e1b8d -->
## Update the given client.

> Example request:

```bash
curl -X PUT "/oauth/clients/1" 
```

```javascript
const url = new URL("/oauth/clients/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`PUT /oauth/clients/{client_id}`


<!-- END_68765e4a9497ffd5ca366e85432e1b8d -->

<!-- START_d37a14b237044eea20249e407316f2b5 -->
## Delete the given client.

> Example request:

```bash
curl -X DELETE "/oauth/clients/1" 
```

```javascript
const url = new URL("/oauth/clients/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE /oauth/clients/{client_id}`


<!-- END_d37a14b237044eea20249e407316f2b5 -->

<!-- START_3999012556e9b9cfd7380bbd3e5f4b3f -->
## Get all of the available scopes for the application.

> Example request:

```bash
curl -X GET -G "/oauth/scopes" 
```

```javascript
const url = new URL("/oauth/scopes");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET /oauth/scopes`


<!-- END_3999012556e9b9cfd7380bbd3e5f4b3f -->

<!-- START_f424300762915d20d5a642e271ac4364 -->
## Get all of the personal access tokens for the authenticated user.

> Example request:

```bash
curl -X GET -G "/oauth/personal-access-tokens" 
```

```javascript
const url = new URL("/oauth/personal-access-tokens");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


> Example response:

```json
null
```

### HTTP Request
`GET /oauth/personal-access-tokens`


<!-- END_f424300762915d20d5a642e271ac4364 -->

<!-- START_fd958d9a4d7d5e2efa1a8de529cdccf4 -->
## Create a new personal access token for the user.

> Example request:

```bash
curl -X POST "/oauth/personal-access-tokens" 
```

```javascript
const url = new URL("/oauth/personal-access-tokens");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`POST /oauth/personal-access-tokens`


<!-- END_fd958d9a4d7d5e2efa1a8de529cdccf4 -->

<!-- START_5df9f91254ca5b0217f0cd72e341e39b -->
## Delete the given token.

> Example request:

```bash
curl -X DELETE "/oauth/personal-access-tokens/1" 
```

```javascript
const url = new URL("/oauth/personal-access-tokens/1");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```



### HTTP Request
`DELETE /oauth/personal-access-tokens/{token_id}`


<!-- END_5df9f91254ca5b0217f0cd72e341e39b -->


