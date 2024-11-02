
# Define o último patch da versão atual do semver utilizando tags Git
CURRENT_VERSION=$(shell git describe --tags --abbrev=0)
NEXT_PATCH_VERSION=$(shell echo $(CURRENT_VERSION) | awk -F. -v OFS=. '{$$NF++; print}')

test:
	composer test

# Faz git push e envia a nova tag
push:
	@echo "Você está prestes a criar e enviar a tag $(NEXT_PATCH_VERSION). Continuar? [y/N]"
	@read -r response; \
	if [ "$$response" = "y" ] || [ "$$response" = "Y" ]; then \
		git push; \
		git tag -a $(NEXT_PATCH_VERSION) -m "$(NEXT_PATCH_VERSION)"; \
		echo "Nova tag criada: $(NEXT_PATCH_VERSION)"; \
		git push --follow-tags; \
		echo "Tag $(NEXT_PATCH_VERSION) enviada ao repositório remoto."; \
	else \
		echo "Operação cancelada."; \
	fi

.PHONY: test push
