# Minimal Makefile for common tasks

.PHONY: help install start stop fix-static

help:
	@echo "Available targets: start, stop, fix-static"

start:
	docker-compose up -d

stop:
	docker-compose down

fix-static:
	@echo "Running post-static symlink fixes..."
	@./scripts/fix-static-symlinks.sh
