# Variables
DOCKER_COMPOSE = docker compose

# Default target
.DEFAULT_GOAL := help

# Help target
help: ## Display this help message
	@echo "Usage: make [target]"
	@echo ""
	@echo "Targets:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  %-15s %s\n", $$1, $$2}'

# Docker targets
build: ## Build the Docker containers
	$(DOCKER_COMPOSE) build

up: ## Start the Docker containers
	$(DOCKER_COMPOSE) up -d

down: ## Stop the Docker containers
	$(DOCKER_COMPOSE) down

# Test targets
test: ## Run PHPUnit tests
	$(DOCKER_COMPOSE) run --rm app vendor/bin/phpunit tests