# CPF Blacklist

PHP application to record and check if an **CPF** number is blacklisted.

## Quickstart

Developed on debian-based linux distro.

```bash
docker-compose build
docker-compose up
```

## Endpoints

Endpoints use either a HTTP `GET`, `POST` or `DELETE` to access, update, or delete data, respectively.

**URL**: `/check?cpf={:id}`

**Method**: `GET`

---

**URL**: `/status`

**Method**: `GET`

---

**URL**: `/`

**Method**: `POST`

**Parameters**:

| Name | Content Type | Type | Required |
| --- | --- | --- | --- |
| cpf | form-data | String | No |

---

**URL**: `/`

**Method**: `DELETE`

**Parameters**:

| Name | Content Type | Type | Required |
| --- | --- | --- | --- |
| cpf | x-www-form-urlencoded | String | No |

## Libraries

| Name | Version |
| --- | --- |
| jQuery | 1.11.1 |
| jQuery Mask | 1.14.10 |
| Bootstrap | 3.3.0 |