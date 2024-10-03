# PHP Projects: URL Shortener and Chatbot

This repository contains two PHP projects:

1. **URL Shortener** (`/url_shortener`):  
   An application for shortening URLs.

2. **Chatbot** (`/chatbot`):  
   A Wikipedia chatbot.

The repository includes a PHP container running Apache and the MySQLi extension, along with a MySQL database container.

---

## Prerequisites

To run these projects, you need to have the following installed on your system:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

---

## Setup Instructions

Follow the steps below to get the environment up and running:

### 1. Build the Docker Image

Before starting the application, you need to build the Docker image for the PHP container. Run the following command:
```sh
docker build -t php-mysql .
```
This will build the image using the Dockerfile (if present) or inline build instructions from docker-compose.yml.

### 2. Start the Docker Containers

Once the image is built, you can start the services (PHP and MySQL containers) by running:
```sh
docker-compose up -d
```
This will start the containers in detached mode (running in the background). You can now access the PHP application at http://localhost:8080 (or the configured port in docker-compose.yml).

### 3. Stop the Docker Containers

To stop the running containers, use the following command:
```sh
docker-compose down
```
This stops and removes the containers but keeps the MySQL data volume intact.

### 4. Re-initialize the Database (Optional)

If you want to completely remove the containers along with the database volume (to reset everything, including data), you can use the -v flag:
```sh
docker-compose down -v
```
This will remove both the containers and the associated volumes, including the MySQL database data.
