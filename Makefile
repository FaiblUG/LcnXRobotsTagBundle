install:
	composer install

update:
	composer update

phpunit:
	vendor/bin/phpunit

phpunit-coverage:
	vendor/bin/phpunit --coverage-html=build/coverage --coverage-text
