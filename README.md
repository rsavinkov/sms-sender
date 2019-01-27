# sms-sender

Small application to send SMS-messages by MessageBird API

## Dev-environment

### Installation

1) `docker-compose up -d`
2) `docker-compose exec php-fpm composer install`
3) `docker-compose exec php-fpm cp /application/config/config.dist.php /application/config/config.php`

### Available Endpoints

`POST http://localhost:4200/message`
```
{
	"recipient":79635064065,
	"originator":"Test",
	"message":"test message"
}

```
### Tests
`docker-compose exec php-fpm composer test`
