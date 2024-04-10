<p align="center"><img src="https://raw.githubusercontent.com/dewanakl/Kamu/main/public/kamu.png" width="200" alt="kamu"></p>

<p align="center">
<a href="https://php.net"><img src="https://img.shields.io/packagist/dependency-v/kamu/framework/php.svg" alt="PHP Programming Language"></a>
<a href="https://packagist.org/packages/kamu/framework"><img src="https://img.shields.io/packagist/dt/kamu/framework" alt="Total Downloads"></a>
<a href="https://github.com/dewanakl/framework"><img src="https://img.shields.io/github/repo-size/dewanakl/framework" alt="repo size"></a>
<a href="https://packagist.org/packages/kamu/framework"><img src="https://img.shields.io/packagist/v/kamu/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/kamu/framework"><img src="https://img.shields.io/packagist/l/kamu/framework" alt="License"></a>
</p>

## About Kamu

"Kamu" merupakan PHP framework yang sangat simpel, memberikan pengalaman seolah-olah berada di localhost meskipun dalam mode production. Dibantu dengan "Saya" konsol yang membantu pengembangan aplikasi secara efisien.

## Api Spec

See in postman collection
```url
https://www.postman.com/dewanakl/workspace/undangan/collection/20716209-a29ef362-b4dc-4c02-8904-d90749a40842?action=share
```

## Run without docker

- Create file env

    ```bash
    cp .env.example .env
    ```

- Install package

    ```bash
    composer install
    ```

- Create key application

    ```bash
    php saya key
    ```

- Execute migration database

    ```bash
    php saya migrasi --gen
    ```

- Run in development server

    ```bash
    php saya coba
    ```

## Run with docker

- Create file env

    ```bash
    cp .env.example .env
    ```

- Change and customize env file

    ```text
    BASEURL=https://your.domain.or.ipaddress:8080/

    DB_DRIV=pgsql
    DB_HOST=db
    DB_PORT=5432
    DB_NAME=undangan
    DB_USER=root
    DB_PASS=12345678

    JWT_KEY=valueIsSecure
    ```

- Build and run image

    ```bash
    docker compose up --build -d
    ```

- Execute migration

    > **_NOTE:_** Wait until the database is ready.

    ```bash
    docker exec undangan-app php saya migrasi --gen
    ```

## Deployment on vercel

- Clone or download this repository

    ```bash
    git clone https://github.com/dewanakl/undangan-api.git
    ```

- Install package

    ```bash
    composer install
    ```

- Create .env file

    ```bash
    cp .env.example .env
    ```

- Execute migration database

    ```bash
    php saya migrasi --gen
    ```

- Create key application

    ```bash
    php saya key
    ```

- Push on your github.
- Create new project in vercel.
- Import from your repository.
- Change environment variables in your project on vercel.
- Add this :
  - DB_HOST (your host cloud dbms)
  - DB_PASS (your password cloud dbms)
  - DB_USER (your username cloud dbms)
  - DB_NAME (your name of database cloud dbms)
  - DB_PORT (your port cloud dbms)
  - DB_DRIV (type cloud dbms [ex. mysql or pgsql])
  - JWT_KEY [ex. 123]
  - HTTPS [true]
  - DEBUG [false]
  - LOG [false]
  - APP_KEY [copy from your local env]
- Click deployments tab in vercel project.
- Click the most recent deploy.
- Click dot three and redeploy.
- Done.

## Get Started Project

- Create a project with composer

    ```bash
    composer create-project kamu/kamu coba-app
    ```

- Move the folder

    ```bash
    cd coba-app
    ```

- Run in development server

    ```bash
    php saya coba
    ```

## Contributing

I'm very open to those of you who want to contribute to Kamu framework!

## Security Vulnerabilities

If you find a security vulnerability in this Kamu, please email DKL via [dewanakretarta29@gmail.com](mailto:dewanakretarta29@gmail.com).

## License

Kamu framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
