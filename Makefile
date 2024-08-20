#!/usr/bin/make
.DEFAULT_GOAL := test

.PHONY: test publish
test:
	./vendor/bin/pest

publish:
	git push --follow-tags
