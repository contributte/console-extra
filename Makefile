.PHONY: install qa lint cs csf phpstan tests coverage-clover coverage-html

install:
	composer update

qa: lint phpstan cs

lint:
	vendor/bin/linter src tests

cs:
	vendor/bin/codesniffer src tests

csf:
	vendor/bin/codefixer src tests

phpstan:
	vendor/bin/phpstan analyse -l max -c phpstan.neon --memory-limit=512M src

tests:
	vendor/bin/tester -s -p php --colors 1 -C tests/Cases

coverage-clover:
	vendor/bin/tester -s -p phpdbg --colors 1 -C --coverage ./coverage.xml --coverage-src ./src tests/Cases

coverage-html:
	vendor/bin/tester -s -p phpdbg --colors 1 -C --coverage ./coverage.html --coverage-src ./src tests/Cases
