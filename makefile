# run php tests
test:
	@phpunit
commit:
	@git add .
	@git commit -am"$(message) `date`"
push: commit
	@git push origin master --tags
deploy:
	@git push heroku master
test-angular:
	@karma start
# start php server
start:
	@php -S localhost:3000 -t web web/index.php &
.PHONY: commit deploy test-angular push test start