#!/usr/bin/make
.DEFAULT_GOAL := test

.PHONY: test
test:
	./vendor/bin/pest
