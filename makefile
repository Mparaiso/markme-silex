commit:
	@git add .
	@git commit -am"$(message) `date`"
push: commit
	@git push origin master --tags
deploy:
	@git push heroku master
test-angular:
	@karma start
.PHONY: commit deploy test-angular push