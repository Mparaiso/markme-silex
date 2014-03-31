push:
	@git add .
	@git commit -am"$(message) `date`"
.PHONY: push