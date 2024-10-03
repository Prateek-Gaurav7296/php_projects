This project provides a Dockerized environment for running a PHP application with a MySQL database. It includes a PHP container running Apache and the MySQLi extension, along with a MySQL database container.

Prerequisites

	•	Docker installed on your system.
	•	Docker Compose installed.

Setup Instructions

Follow the steps below to get the environment up and running:

*1. Build the Docker Image*


Before starting the application, you need to build the Docker image for the PHP container. Run the following command:
docker build -t php-mysql .

This will build the image using the Dockerfile (if present) or inline build instructions from docker-compose.yml.

*2. Start the Docker Containers*


Once the image is built, you can start the services (PHP and MySQL containers) by running:
docker-compose up -d

This will start the containers in detached mode (running in the background). You can now access the PHP application at http://localhost:8080 (or the configured port in docker-compose.yml).

*3. Stop the Docker Containers*


To stop the running containers, use the following command:
docker-compose down
This stops and removes the containers but keeps the MySQL data volume intact.

*4. Re-initialize the Database (Optional)*


If you want to completely remove the containers along with the database volume (to reset everything, including data), you can use the -v flag:
docker-compose down -v

This will remove both the containers and the associated volumes, including the MySQL database data.

Commands Recap:

1.Build the Docker Image:
  docker build -t php-mysql .
  
2.Start the Containers:
  docker-compose up -d
  
3.Stop the Containers:
  docker-compose down
  
4.Re-initialize the Database (Optional):
  docker-compose down -v

    
├── docker-compose.yml      # Docker Compose configuration
├── Dockerfile              # Dockerfile to build the PHP container (if needed)
├── src/                    # PHP application files
└── README.md               # This readme file

	•	PHP Application: Open your browser and visit http://localhost:8080 to view the running PHP application.
	•	MySQL Database: The MySQL service runs on the default port 3306, and the default credentials can be set in the docker-compose.yml file under the db service.
