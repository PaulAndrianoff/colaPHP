# colaPHP Framework

colaPHP is a lightweight PHP framework designed to provide a simple structure for web applications. It features a routing system, controllers, views, models, and a database connection using PDO. Additionally, it includes a command-line interface (CLI) for generating routes, controllers, and views.

## Features

- Simple and intuitive routing system
- MVC architecture with controllers, models, and views
- Database connection using PDO
- Debugger mode for easy development and debugging
- CLI commands for generating routes, controllers, and views

## Installation

1. Clone the repository:

    ```sh
    git clone https://github.com/yourusername/colaPHP.git
    cd colaPHP
    ```

2. Configure your database and application settings in `config.php`:

    ```php
    return [
        'db' => [
            'host' => '127.0.0.1',
            'dbname' => 'your_database_name',
            'user' => 'your_database_user',
            'pass' => 'your_database_password',
        ],
        'debug' => true, // Set to false in production
    ];
    ```

3. Set up your web server to point to `index.php` as the entry point for your application.

## Usage

### Routing

Define your routes in `index.php`:

```php
$router->get('/', 'HomeController@index');
$router->get('/user/{id}', 'UserController@show');
$router->get('/users', 'UserController@index');
$router->apiGet('/api/user/{id}', 'ApiUserController@show');

$router->run();
```

### Controllers
Create controllers in the app/Controllers directory. Controllers should extend the base Controller class.

Example: app/Controllers/HomeController.php

```php

<?php

class HomeController extends Controller {
    public function index() {
        $this->view('home', ['message' => 'Welcome to colaPHP!']);
    }
}
```

### Views
Create views in the app/Views directory. Views are simple PHP files that can display data passed from controllers.

Example: app/Views/home.php

```php

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h1><?= $message ?></h1>
</body>
</html>
```

### Models
Create models in the app/Models directory. Models should extend the base BaseModel class and provide methods for interacting with the database.

Example: app/Models/User.php

```php
<?php

require_once __DIR__ . '/../core/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';

    public function getUserById($id) {
        return $this->fetch("SELECT * FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }

    public function getAllUsers() {
        return $this->fetchAll("SELECT * FROM {$this->table}");
    }

    public function createUser($data) {
        $sql = "INSERT INTO {$this->table} (name, email) VALUES (:name, :email)";
        $this->query($sql, $data);
        return $this->lastInsertId();
    }
}
```

### Debugging
Enable or disable debug mode in config.php:

```php
return [
    'debug' => true, // Set to false in production
];
```

When debug mode is enabled, detailed error messages and debugging output will be displayed.

## CLI Commands

### Create a Route with a Controller and a View
To create a new route along with its controller and view:

```sh
php cli.php create:route <route> <controller> <view>

## Example:

php cli.php create:route /about AboutController about
```
This command will:

Create a new controller named AboutController.php in the app/Controllers directory.
Create a new view named about.php in the app/Views directory.
Add a new route to index.php that maps /about to AboutController@index.

### Migrate Database
To run all migrations:

```sh
php cli.php db:migrate
```
This command will run all the migration files in the migrations directory.

### Rollback Database
To roll back all migrations:

```sh
php cli.php rollback
```
This command will roll back all the migration files in the migrations directory in reverse order.

### Create a Migration Based on a Model
To create a migration based on a given model:

```sh
php cli.php db:prepare <model>

## Example:

php cli.php db:prepare User
```
This command will:

Parse the User model for properties with comments indicating column definitions and the table name.
Generate a migration file based on the parsed properties.
Command Syntax
Each command class includes a getSyntax method that returns the expected command syntax. If a command is used incorrectly, the correct usage will be displayed.

## License
This project is licensed under the MIT License. See the LICENSE file for details.

### Summary

This README provides an overview of the features, installation steps, and usage instructions for your colaPHP framework. It includes sections on routing, controllers, views, models, debugging, and CLI commands, ensuring that users can easily understand and utilize the framework.