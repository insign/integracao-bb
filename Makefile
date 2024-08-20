#!/usr/bin/make
.DEFAULT_GOAL := test

.PHONY: test publish
test:
	composer test

publish:
	git push --follow-tags
