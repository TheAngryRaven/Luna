# [Lunar Messaging](https://lunarmessaging.net)
This is an attempt at an extremely secure web based encrypted messenger.

hosted at https://lunarmessaging.net

### Setup
Install laravel, then use composer to add phpseclib, everything else is here in the repo.

    $ composer create-project laravel/laravel Luna
    $ cd Luna
    $ composer require phpseclib/phpseclib

then use the sql in 'sql-structure-backups' to create the database

remember to edit your .env to include an email account as well
    
### ToDo
- [x] First level of security SSL
- [x] Second level of security using JS RSA and AES
- [ ] Create basic message drop system
- [ ] Encrypt 'post' data in a similar way
- [ ] Create user login and register system

#### License
   Licensed under the Apache License, Version 2.0

       http://www.apache.org/licenses/LICENSE-2.0

