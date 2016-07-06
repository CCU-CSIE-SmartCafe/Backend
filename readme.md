## Smart Cafe Backend
[![Build Status](https://travis-ci.org/CCU-CSIE-SmartCafe/Backend.svg?branch=master)](https://travis-ci.org/CCU-CSIE-SmartCafe/Backend)

This is the prototype project for CCU Smart Cafe project backend System made by [Laravel](https://laravel.com). 

## Usage

1. Get the *Feature Path*.
2. Use OPTIONS http method to get api information.
 
## Example

Path: `auth/register`

Method: `OPTIONS`

```json
{
  "description": "Register a new user.",
  "allow": [
    "POST"
  ],
  "methods": {
    "POST": {
      "email": {
        "description": "User's email.",
        "required": true,
        "type": "string"
      },
      "name": {
        "description": "User's name.",
        "required": true,
        "type": "string"
      },
      "password": {
        "description": "User's password.",
        "required": true,
        "type": "string"
      }
    }
  },
  "returns": {
    "status": {
      "description": "Is this request successfully?",
      "type": "boolean"
    },
    "message": {
      "description": "Request message.",
      "type": "array/string"
    }
  }
}
```

PATH: `auth/register`

Method: `POST`

Parameters: `email`, `name`, `password`

```json
{
  "status": true,
  "message": "Register successfully."
}
```

## LICENSE

The Smart Cafe Backend is open-sourced software licensed under the MIT license.