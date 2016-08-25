requestum/user-single-session-bundle
================

DESCRIPTION
-----------

RequestumUserSingleSessionBundle logout user if under his account entered again.

RequestumUserSingleSessionBundle is storage agnostic, meaning it can work with several storage mechanisms.
Bundle works with User entity. You may use a Custom implementation of User class, or one based on the FOSUserBundle.

INSTALLATION
------------

Use composer to manage your dependencies and download RequestumUserSingleSessionBundle.

Add this code to you `composer.json` file:

    "require": {
            ...
            "requestum/user-single-session-bundle": "dev-master"
    }

And add this code to repository section:

    "repositories": [
            ...
            {
                "type": "git",
                "url": "git@gitlab.requestum.com:yadviha.khoshtaria/single-session-bundle.git"
            }
    ]

Then only run `composer update` or `php composer.phar update` command in root of your project.

CONFIGURATION
-------------

YAML:
        
        requestum_user_single_session:
            storage: ???
            failure_action:
                type: ???
                template: ???
                
DOCUMENTATION
-------------

Option `storage:`
-----------------

Option `storage` is configure what storage you want use for storing tokens.
By default allow two values: `memcached` and `entity`. If option had not set by default using `entity` value.

        requestum_user_single_session:
            storage: ???
            ...
            
If you chose `entity` storage. You User entity must implements `SingleSessionUserInterface`

If you chose `memcached` storage. You must add to you `parameters.yml` next code:

    # Default host and port for memcached
    
    memcached.servers:
            ...
            - { host: 127.0.0.1, port: 11211 }
            ...

If you want using you own storage? you only need create service that is implementation of `TokenIdManagerInterface` 
and write its id in `storage:` option.

Option `failure_action:`
------------------------

Option `failure_action` is configure what must doing bundle if user account re-authorized.
Option `type` is required and can takes `logout` or `view` values.

    requestum_user_single_session:
                failure_action:
                
                        # Required option
                        type:
                 
                        # Required if choose 'view' type
                        template:
                                               
Option `template:` required if choose `type: view` and accepts a template address.

If you chose `logout` type, you must have `logout` route in you application.