# Minimal Makefile for common tasks

.PHONY: help install start stop fix-static

help:
	@echo "Available targets: start, stop, fix-static"

start:
	./start.sh

stop:
	docker-compose down

fix-static:
	@echo "Running post-static symlink fixes..."
	@./scripts/fix-static-symlinks.sh
