# api-centreon-notification-helper

Helper functions for the integration of Centreon with 3rd party alerting solution

It provides notably methods to:
- ack / unack services and hosts
- check whether a service or host is acked
- check whether a service has associated metrics or not

## Implementation details

It relies on [Transatel/lib-eloquent-centreon](https://github.com/Transatel/lib-eloquent-centreon) for accessing Centreon's databases and calling its [internal](https://github.com/centreon/centreon/tree/master/www/include/common/webServices/rest) REST API.

## Configuration

Copy the [.env.example](.env.example) file into a new `.env` file.

There are are the keys you might want to edit (if you changed default values).

After modifying them, one might want to do a `php artisan config:clear` to ensure older cached values are purged.

### Centreon Internal REST API

| Key                                | Description                        |
| --                                 | --                                 |
| CENTREON\_INTERNAL\_REST\_API\_URL | URL of Centreon internal REST API  |
| CENTREON\_REST\_API\_USERNAME      | Username for the internal REST API |
| CENTREON\_REST\_API\_PASSWORD      | Password for the internal REST API |

### Centreon DB schema (configuration)

| Key                    | Description                                          |
| --                     | --                                                   |
| DB\_HOST\_CENTREON     | Domain Name or IP address to connect to the database |
| DB\_PORT\_CENTREON     | Port to connect to the database                      |
| DB\_DATABASE\_CENTREON | Name of the schema                                   |
| DB\_USERNAME\_CENTREON | Username to connect to the database                  |
| DB\_PASSWORD\_CENTREON | Password to connect to the database                  |

### Centreon Storage DB schema (volatile data)

| Key                             | Description                                          |
| --                              | --                                                   |
| DB\_HOST\_CENTREON\_STORAGE     | Domain Name or IP address to connect to the database |
| DB\_PORT\_CENTREON\_STORAGE     | Port to connect to the database                      |
| DB\_DATABASE\_CENTREON\_STORAGE | Name of the schema                                   |
| DB\_USERNAME\_CENTREON\_STORAGE | Username to connect to the database                  |
| DB\_PASSWORD\_CENTREON\_STORAGE | Password to connect to the database                  |

## Usage

### Retrieve dependencies

	$ composer update

### Launch

#### Development mode

	$ php -S 0.0.0.0:8000 -t public

#### Example Apache configuration

```
<VirtualHost *:8000>
  ServerName api-centreon-notification-helper
  DocumentRoot "/opt/api-centreon-notification-helper/public"
  <Directory "/opt/api-centreon-notification-helper/public/">
    Options Indexes FollowSymLinks
    AllowOverride all
    Require all granted
  </Directory>
</VirtualHost>
```
