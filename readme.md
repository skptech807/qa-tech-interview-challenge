# 🚀 Quick Start

This project uses **Docker Compose** to run a local service on port `8888`.

## 📋 Steps to Run

### 1. Install Docker and Docker Compose

Make sure you have the following installed:

* [Docker](https://docs.docker.com/get-docker/)
* [Docker Compose](https://docs.docker.com/compose/install/)

Check the installation:

```bash
docker --version
docker compose version
```

---

### 2. Ensure Port 8888 Is Free

Make sure port `8888` is not being used by another application.

**Linux/macOS:**

```bash
lsof -i :8888
```

**Windows (PowerShell):**

```powershell
netstat -ano | findstr :8888
```

If the port is in use, stop the conflicting application or change the port in `docker-compose.yml`.

---

### 3. Start the Service

Run the following command from the project root:

```bash
docker compose up -d
```

This will start the services in detached mode (in the background).

---

### 4. Check Availability

Open your browser and go to:

👉 [http://localhost:8888/](http://localhost:8888/)

You should see the application interface or a success message.

---

## 🛑 Stopping the Service

To stop and remove the containers, networks, and volumes defined in `docker-compose.yml`:

```bash
docker compose down
```


# API Specification Summary

## POST `/auth/login`

**Request:**

```json
{
  "username": "string",
  "password": "string"
}
```

**Response:**

```json
{
  "token": "string",
  "expiresIn": "integer"
}
```

**Status Codes:**

- `200 OK` – Successful authentication
- `401 Unauthorized` – Invalid credentials

---

## GET `/accounts/{accountId}`

**Headers:**

```
Authorization: Bearer {token}
```

**Response:**

```json
{
  "accountId": "string",
  "balance": "number",
  "currency": "string"
}
```

**Status Codes:**

- `200 OK` – Account found and balance retrieved
- `401 Unauthorized` – Missing or invalid token
- `404 Not Found` – Account ID not found

---

## POST `/transfers`

**Headers:**

```
Authorization: Bearer {token}
```

**Request:**

```json
{
  "fromAccount": "string",
  "toAccount": "string",
  "amount": "string",
  "currency": "string",
  "description": "string"
}
```

**Response:**

```json
{
  "transferId": "integer",
  "status": "string",
  "timestamp": "string"
}
```

**Status Codes:**

- `200 OK` – Transfer completed successfully
- `400 Bad Request` – Validation errors (e.g. malformed fields)
- `401 Unauthorized` – Missing or invalid token
- `402 Payment Required` – Insufficient funds
- `404 Not Found` – Account not found

---

## GET `/accounts/{accountId}/transactions`

**Headers:**

```
Authorization: Bearer {token}
```

**Query Parameters:**

- `from` (ISO date)
- `to` (ISO date)
- `limit` (number)

**Response:**

```json
{
  "transactions": [
    {
      "id": "integer",
      "type": "string",
      "amount": "number",
      "currency": "string",
      "otherParty": "string",
      "description": "string",
      "timestamp": "string"
    }
  ]
}
```

**Status Codes:**

- `200 OK` – Transaction history returned
- `401 Unauthorized` – Invalid or missing token
- `404 Not Found` – Account not found
