pipeline {
    agent {
        docker {
            image 'php:8.2'
            args '-u root'
        }
    }

    environment {
        COMPOSER_CACHE_DIR = '/var/jenkins_home/composer_cache'
        DB_CONTAINER_NAME = 'mariadb-1'
        DB_ROOT_PASSWORD = 'root'
        DB_NAME = 'sf_testing'
    }

    stages {
        stage('Attendre la Base de Données') {
            steps {
                script {
                    echo "------------------- Étape: Attendre la Base de Données"
                    def isRunning = sh(script: "docker ps --filter name=${DB_CONTAINER_NAME} --filter status=running --format '{{.Names}}'", returnStdout: true).trim()

                    if (isRunning != '') {
                        echo "------------------- Succès: Le conteneur MariaDB est en cours d'exécution."
                    } else {
                        error "------------------- Erreur: Le conteneur MariaDB n'est pas en cours d'exécution."
                    }
                }
            }
        }


        stage('Obtenir l\'Adresse IP de la Base de Données') {
            steps {
                script {
                    echo "------------------- Étape: Obtenir l'Adresse IP de la Base de Données"
                    def dbIpAddress = sh(script: "docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' ${DB_CONTAINER_NAME}", returnStdout: true).trim()
                    env.DB_IP_ADDRESS = dbIpAddress
                    echo "Adresse IP du conteneur MariaDB : ${env.DB_IP_ADDRESS}"
                    echo "------------------- Succès: Adresse IP obtenue."
                }
            }
        }

        stage('Tester la Connexion à la Base de Données') {
            steps {
                script {
                    echo "------------------- Étape: Tester la Connexion à la Base de Données"
                    sh """
                        mysql -h ${env.DB_IP_ADDRESS} -P 3306 --protocol=tcp -uroot -p${DB_ROOT_PASSWORD} -e "SHOW DATABASES;"
                    """
                    echo "------------------- Succès: Connexion à la base de données testée."
                    sh 'mysql --version'
                }
            }
        }

        stage('Install Dependencies') {
            steps {
                sh '''
                apt-get update && \
                apt-get install -y git unzip default-mysql-client wget && \
                docker-php-ext-install pdo pdo_mysql && \
                curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
                wget https://github.com/jwilder/dockerize/releases/download/v0.6.1/dockerize-linux-amd64-v0.6.1.tar.gz && \
                tar -C /usr/local/bin -xzvf dockerize-linux-amd64-v0.6.1.tar.gz && \
                composer install --prefer-dist --no-interaction
                '''
            }
        }

        stage('Create .env.test.local') {
            steps {
                script {
                    def dbUrl = "mysql://root:${DB_ROOT_PASSWORD}@${env.DB_IP_ADDRESS}:3306/${DB_NAME}?serverVersion=11.3.2-MariaDB&charset=utf8mb4"
                    writeFile file: '.env.test.local', text: "DATABASE_URL=\"${dbUrl}\"\n"
                }
            }
        }

        stage('Run Linter') {
            steps {
                sh './vendor/bin/php-cs-fixer fix --dry-run --diff'
            }
        }

        stage('Run Tests') {
            steps {
                sh 'APP_ENV=test ./vendor/bin/phpunit'
            }
        }
    }

    post {
        always {
            script {
                echo "------------------- Pipeline terminé."
            }
        }
    }
}
