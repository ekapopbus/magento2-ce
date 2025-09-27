How to create an admin/integration token for MFTF

This file documents a recommended, secure approach to provide a non-interactive admin/integration token
to the Magento Functional Testing Framework (MFTF) when your Magento instance enforces admin 2FA.

1) Create an integration (Admin UI)
   - Go to Magento Admin -> System -> Extensions -> Integrations
   - Click "Add New Integration"
   - Fill name (e.g. "MFTF Integration"), add an email, and in "API" give it the required permissions
     (at minimum: System -> Store -> Settings, and other read-only endpoints used in your environment).
   - Save, then click "Activate" for the new integration and note the access token shown.

2) Add token to your local MFTF credentials
   - Edit dev/tests/acceptance/.credentials (local file, not committed)
   - Add a single line:
     magento/MAGENTO_ADMIN_TOKEN=YOUR_TOKEN_HERE
   - Ensure .credentials is gitignored and file permissions are tight (chmod 600).

3) Re-run test generation (normal path)
   - From the project root:
     vendor/bin/mftf generate:tests -r
   - This should now allow ModuleResolver to query /rest/V1/modules and include modules correctly.

4) Notes and security
   - Treat the token like a secret. Do not commit it to source control.
   - For CI, create a dedicated integration for the CI user and store the token in the pipeline secret store.
   - If you can't create an integration, the quick workaround is to set MFTF_FORCE_GENERATE=1 when generating tests,
     but this is only a temporary workaround and should not be used in CI unless you understand its consequences.
