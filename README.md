INSTALLATION

Use composer to manage your dependencies and download RequestumUserSingleSessionBundle.

...

DESCRIPTION

This bundle solves the next task:

...

RequestumUserSingleSessionBundle is storage agnostic, meaning it can work with several storage mechanisms.

...
    
Bundle works with User entity. You may use a Custom implementation of User class, or one based on the FOSUserBundle.
To let RequestumUserSingleSessionBundle know which class to use configuration must contain a special parameter.
Get more details in the CONFIGURATION section.

CONFIGURATION

YAML:
        
        requestum_user_single_session.example:
            storage: ???
            user_class: UserBundle\Model\TestUser