
# Define o último patch da versão atual do semver utilizando tags Git
CURRENT_VERSION=$(shell git describe --tags --abbrev=0)
NEXT_PATCH_VERSION=$(shell echo $(CURRENT_VERSION) | awk -F. -v OFS=. '{$$NF++; print}')

test:
	composer test

# Faz git push e envia a nova tag
push:
	@git tag $(NEXT_PATCH_VERSION)
	@echo "Nova tag criada: $(NEXT_PATCH_VERSION)"
	@git push origin $(NEXT_PATCH_VERSION)
	@echo "Tag $(NEXT_PATCH_VERSION) enviada ao repositório remoto."
.PHONY: test tag push
