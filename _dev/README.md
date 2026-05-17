# Docker Development Environment

This folder contains the files to run a local PrestaShop instance via Docker for testing the `hhcspheaders` module.

## Quick start

```bash
cd _dev
cp .env.example .env
make up      # start the containers
make setup   # wait for PS to be ready, install the module, clear cache
make test-e2e
```

PrestaShop will be available at the URL shown in the terminal (default: `http://localhost:8090`).

> `make setup` may take a few minutes while PrestaShop completes its automatic installation.

## Available commands

| Command | Description |
|---|---|
| `make up` | Start the PrestaShop environment |
| `make down` | Stop the environment |
| `make restart` | Restart services |
| `make logs` | Show PrestaShop container logs |
| `make shell` | Open a shell in the PrestaShop container |
| `make install` | Install Composer dependencies |
| `make wait` | Wait until PrestaShop is ready |
| `make setup` | Run `wait` + `install-module` + `cache-clear` |
| `make install-module` | Install the module in PrestaShop |
| `make cache-clear` | Clear the PrestaShop cache |
| `make fix-permissions` | Fix `var/` directory permissions |
| `make test` | Run PHP CS Fixer in dry-run mode |
| `make test-unit` | Run PHPUnit unit tests |
| `make test-e2e` | Run Playwright e2e tests (headless) |
| `make test-e2e-headed` | Run Playwright e2e tests with visible browser |
| `make test-e2e-debug` | Run Playwright e2e tests in debug mode |
| `make clean` | Remove the environment and all data |

## Switching PrestaShop versions

```bash
make switch-1.7.8
make switch-8.1
make switch-9
make switch-nightly
make switch-9.1.1-php83
make switch-9.1.1-php84
make switch-9.1.1-php85
```

Then run `make up` to start the new version.

## Running multiple versions in parallel

```bash
make multi
```

| Version | PrestaShop | phpMyAdmin |
|---|---|---|
| 1.7.8 | http://localhost:8090 | http://localhost:8091 |
| 8.1 | http://localhost:8092 | http://localhost:8093 |
| 9 | http://localhost:8094 | http://localhost:8095 |
| nightly | http://localhost:8096 | http://localhost:8097 |

```bash
make multi-down  # stop all versions
```

## Playwright e2e tests

Tests are located in `tests/functionnal/`. Before running them, make sure:

1. PrestaShop is started (`make up`) and fully installed
2. The module is installed (`make install-module`)
3. The `WEBSITE_URL` variable in `.env` points to your instance

```bash
make test-e2e
```

## Services

| Service | URL | Credentials |
|---|---|---|
| PrestaShop front | `http://localhost:${PS_PORT}` | — |
| PrestaShop admin | `http://localhost:${PS_PORT}/admin` | demo@prestashop.com / prestashop_demo |
| phpMyAdmin | `http://localhost:${PMA_PORT}` | prestashop / prestashop |
| MySQL | `localhost:${MYSQL_PORT}` | prestashop / prestashop |

## Xdebug

The PrestaShop container is pre-configured with Xdebug in `debug` mode on port `9003`. Configure your IDE to listen on that port.
