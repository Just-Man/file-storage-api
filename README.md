# Task description

Create a V3S3 (Very Very Very Simple Storage System) . It should be implemented as a
(REST) API similar to Amazon’s S3, but with far less functionality.
The API should be able to:
- Store an object (image, json data or anything else) with a specified name, through a
PUT request
- Return the same object when requested by the same name, through a GET request
- Delete an object by name, through a DELETE request. Objects should never actually
be deleted, but the object should not be returned in subsequent GET requests.
- Versioning history is required. If an object exists (even if it is marked as deleted) and
a new one is later stored with the same name, GET requests should receive the new
object but the old one should still be kept in storage and associated with that name.
- Object names should have the same constraints and requirements as in S3
The REST protocol details, like endpoints, states, headers, etc., are up to you. 

##### Requirements
###### Design
> 1. You can do it exactly like S3 
> 2. Completely different, but you have to provide at least basic documentation on how to use the API.

###### The server implementation should be in either
> 1. PHP 
> 2. NodeJS. 
###### Database
> - use any kind of database or none at all and store the objects however you like.

##### It is important to provide written instructions on how to setup and run the project.

# Develpment
 I choose [Lumen](https://lumen.laravel.com/docs/5.4) is Laravel micro-framework (just for test new framework)

## Getting Started

1. Download project 

```bash
$ git clone https://github.com/Just-Man/file-storage-api.git
```
or download as zip file from [here](https://github.com/Just-Man/file-storage-api/archive/develop.zip)

2. After downloading project you must enter in project root folder with 
```bash
$ cd <project-path>
``` 
3. install composer dependencies with

```bash
$ composer install
```
- if you haven't installed composer on local machine check:
[Composer instalation](https://getcomposer.org/doc/00-intro.md)

4. Add new host name `<project-hostname.extesnion>`
5. Add new vhost for host name`<project-hostname.conf>`
6. Restart web server
7. Create Database for project
8. setup your .env file 
> - for example see .env.example in project root directory
9. from project root you must execute 

```bash
$ php artisan migrate
```
> - this command will create all database table for project

## Available Routes
> Account
>> Create user account
> - /users/create
>> - method POST
>> Get user account
> - /users/{user_id}
>> - method GET
>> Update user account
> - /users/{user_id}/edit
>> - method PUT 
>> - for using PUT method you must send POST request with first field: "_method" and value "PUT"
>> Delete user account
> - /users/{user_id}/delete
>> - method DELETE 
>> - for using DELETE method you must send POST request with first field: "_method" and value "DELETE"

> User configurations.
>> get account configurations
> - /configurations/{configuration_id}
>> - method GET 
>> Update account configuration
> - /configurations/{configuration_id}/edit
>> - method PUT 
>> - for using PUT method you must send POST request with first field: "_method" and value "PUT"

> Files.
>> get account files
> - /files/{id}
>> - method GET 
>> get current files
> - /files/{id}/{fileName}
>> Add/Update current account file
> - /files/{id}/upload
>> - method PUT 
>> - for using PUT method you must send POST request with first field: "_method" and value "PUT"
>> Delete current file
> - /files/{id}/delete            
>> - method DELETE 
>> - for using DELETE method you must send POST request with first field: "_method" and value "DELETE"
