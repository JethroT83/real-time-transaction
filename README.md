# Real Time Transactions

## Start Up the App

This will do the following:
- start the docker containers
- create the .env file from the example file
- install composer dependencies
- run the migrations seeding the database
- create the cache in the app

```aiignore
php artisan app:setup
```

## Login

Test User:

```aiignore
Email: test@example.com
Password: password
```

Admin User:

```aiignore
Email: admin@example.com
Password: password
```

## Apis

- This is built on an inertia/react framework
- The controllers will handle both the frontend and supports APIs
- Open postman
- Import RealTimeTransactions.postman_collection.json

### Generate a token

In order to use the postman APIs you need to generate a token. This can be done by sending a post request to the Authentication.GenerateToken endpoint.  In the body add the parameters of email and password listed above.
```aiignore
{
    "email": "test@example.com",
    "password": "password",
    "device_name": "postman"
}
```

After you send the request, it will return a token.

```aiignore
{
    "token": "1|DOLF10xSLYQ6lr9YGrBBBS3xqcwQrerkDRhwDW1aa089a7dd",
    "user": {
        "id": 2,
        "name": "Test User",
        "email": "test@example.com"
    }
}
```
Once you have the token, you can use it in the Authorization header of the requests using the Auth Type of Bearer.

### Transactions

#### Store
A pre-request script generates random values in order to store as a transaction.
```json
{
    "amount": {{amount}},
    "description": "{{description}}",
    "accountType": "{{accountType}}"
}
```
#### List

This will list out all the transactions in descending order of when the transactions were created.  You can filter what transactions you see from two parameters:

- page
- accountType

```{{base_url}}/api/transactions?page=1&accountType=checking```

```json
{
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "user": "Josianne Langosh",
                "amount": "-2,321.78",
                "description": "Investment - rerum qui",
                "accountType": "checking",
                "created_at": "2025-08-07 05:01:30"
            },
            // ...

            {
                "id": 31,
                "user": "Josianne Langosh",
                "amount": "2,708.53",
                "description": "Subscription payment - quasi fuga",
                "accountType": "checking",
                "created_at": "2025-08-07 05:01:30"
            }
        ],
        "first_page_url": "http://localhost:8000/api/transactions?page=1",
        "from": 1,
        "last_page": 34,
        "last_page_url": "http://localhost:8000/api/transactions?page=34",
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            // ...
            {
                "url": "http://localhost:8000/api/transactions?page=2",
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "next_page_url": "http://localhost:8000/api/transactions?page=2",
        "path": "http://localhost:8000/api/transactions",
        "per_page": 10,
        "prev_page_url": null,
        "to": 10,
        "total": 340
    },
    "meta": {
        "current_page": 1,
        "last_page": 34,
        "per_page": 10,
        "total": 340
    }
}
```

#### Show

This will show a single listing given the primary key value of the transaction.

```{{base_url}}/api/transactions/1```

```json
{
    "data": {
        "id": 1,
        "user": "Josianne Langosh",
        "amount": "-2,321.78",
        "description": "Investment - rerum qui",
        "accountType": "checking",
        "created_at": "2025-08-07 05:01:30",
        "updated_at": "2025-08-07 05:01:30"
    }
}
```

## Transactions

### Front-end
Once you log in, you will see a dashboard with a button which says transactions.  Here you can see all the transactions in a paginated table.

### Adding New Transactions
For a one day exercise, the form page to create a new transaction is not implemented.  However, you can add a new transaction by sending a post request to the transactions.store endpoint.  You will notice you add each transaction, it will added to the table in real time.  This uses a proxy server, a queue work, where it broadcasts to the page with it having to reload.

## Future Enhancements

### Transformers

What is lacking is here is a scalable way to transform the data from the database into the format that the front-end/api needs.  This is where transformers come in. What I would implement is a package called laravel fractal https://fractal.thephpleague.com/transformers.  Using syntax rich parameters, like what graphQL uses, you can define a sort, filter, or include relations. 

For example,
```aiignore
?query={
  users(
    filter: { status: "active" },
    sort: "name",
    include: ["posts", "profile"]
  ) {
    id
    name
    posts { title }
    profile { bio }
  }
}
```
This would encode to

```aiignore
?query=%7B%0A%20%20users(%0A%20%20%20%20filter%3A%20%7B%20status%3A%20%22active%22%20%7D%2C%0A%20%20%20%20sort%3A%20%22name%22%2C%0A%20%20%20%20include%3A%20%5B%22posts%22%2C%20%22profile%22%5D%0A%20%20)%
```
### GraphQL

Depending on the needs and scope of the application, using graphQL is a possibility.  I would use the library lighthouse https://lighthouse-php.com/ in that situation.  Then using reflection classes with a pipeline architecture, you can scalably generate graphQL schemas.

### Permissions

Currently, the users do not have any permissions.  This is something that I would implement in the future.  I would use the package spatie/laravel-permission https://spatie.be/docs/laravel-permission/v5/introduction.

### Unit Testing

While I have some unit tests, I typically like to cover all models, routes, and permissions.  My coverage is not 100%, however I have manually tested what is here.

### Manual Testing

I believe in manual testing.  I did write down a procedure for manual testing as perhaps video for this exercise may suffice.  However, I like tools like zephyr, x-ray, testrail, qmetry, or testmo.  There area manual testing tools which integrate with jira.







#
#


<p align="center"><a href="https://laravel.com"target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
