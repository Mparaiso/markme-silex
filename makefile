push:
	@git add .
	@git commit -am"$(message) `date`"
deploy:
	@git push heroku master
.PHONY: push deploy