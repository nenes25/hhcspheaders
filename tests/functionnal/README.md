# Functional Tests for hhcspheaders Module

This directory contains Playwright-based functional tests for the hhcspheaders PrestaShop module.

## Test Coverage

The tests cover all the main features of the module:

### 1. CSP Headers (`csp-headers.spec.ts`)
- CSP Front-office enable/disable
- CSP Mode: REPORT-ONLY, BLOCK, BOTH
- Individual CSP directives:
  - default-src
  - script-src
  - style-src
  - img-src
  - font-src
  - connect-src
  - frame-src
  - object-src
  - media-src
- Complete CSP policies with multiple directives

### 2. X-Frame-Options and X-Content-Type-Options (`xframe-xcontent.spec.ts`)
- X-Frame-Options disabled
- X-Frame-Options with DENY value
- X-Frame-Options with SAMEORIGIN value
- X-Content-Type-Options enabled (nosniff)
- Combined X-Frame-Options and X-Content-Type-Options

### 3. Referrer-Policy (`referrer-policy.spec.ts`)
- Referrer-Policy disabled
- All Referrer-Policy values:
  - no-referrer
  - no-referrer-when-downgrade
  - origin
  - origin-when-cross-origin
  - same-origin
  - strict-origin
  - strict-origin-when-cross-origin
  - unsafe-url

## Prerequisites

1. Node.js and npm installed
2. A running PrestaShop instance
3. The hhcspheaders module installed and configured

## Setup

1. Install dependencies:
```bash
npm install
```

2. Configure your environment by creating/updating the `.env` file:
```bash
WEBSITE_URL=http://your-prestashop-url.local
```

2. Edit path of the file
```bash
WEBSITE_URL=http://your-prestashop-url.local
```

## Running Tests

### Run all tests
```bash
npm test
```

### Run tests in headed mode (with browser UI)
```bash
npm run test:headed
```

### Run tests in debug mode
```bash
npm run test:debug
```

### Run specific test suites
```bash
# CSP headers tests only
npm run test:csp

# X-Frame and X-Content tests only
npm run test:xframe

# Referrer-Policy tests only
npm run test:referrer
```

### View test report
After running tests, view the HTML report:
```bash
npm run test:report
```

## Test Configuration

The tests use the `apply_case.php` script to configure the module for each test scenario. This ensures:
- Consistent test environment
- Isolation between tests
- Reproducible results

## CI/CD Integration

The tests are configured to run in CI environments with:
- Automatic retries on failure (2 retries in CI)
- Single worker mode in CI for stability
- HTML reporter for easy result viewing

## Adding New Tests

1. Add test configuration in `apply_case.php`
2. Create test cases in the appropriate spec file
3. Follow the existing pattern for consistency
4. Run tests locally to verify

## Troubleshooting

- If tests fail, check the `playwright-report` directory for screenshots and traces
- Ensure your PrestaShop instance is accessible at the URL specified in `.env`
- Verify the module is properly installed and active
- Check that the `apply_case.php` script has proper permissions
