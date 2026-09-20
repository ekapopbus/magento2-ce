Disabling AWS S3 usage for local MFTF/Codeception runs

If you want to prevent MFTF or Codeception from contacting AWS services during local development or CI, you have two safe options:

1) Preferred (env var): disable S3 helpers at runtime

- Set this environment variable when running MFTF/Codeception to make S3 helpers fall back to a local filesystem driver and avoid constructing AWS SDK clients:

  REMOTE_STORAGE_AWSS3_DISABLE=1 vendor/bin/codecept run functional -c dev/tests/acceptance/codeception.yml

- This is reversible and does not require changing system files.

2) Alternative (hosts block): block AWS hostnames via /etc/hosts

- Use `dev/tests/acceptance/hosts-block-aws.txt` as a template. Example (run as root):

  sudo cp /etc/hosts /etc/hosts.mftf-block-aws.bak
  sudo tee -a /etc/hosts < dev/tests/acceptance/hosts-block-aws.txt

- To revert:

  sudo cp /etc/hosts.mftf-block-aws.bak /etc/hosts

Notes
- Disabling S3 is useful for unit/local functional runs where remote storage isn't required. For realistic remote-storage tests, either provide valid S3 env variables or use a local AWS stub such as localstack and configure endpoints accordingly.
- REMOTE_STORAGE_AWSS3_DISABLE affects only the test helper fallback implemented in the repository; if you add custom code that always constructs S3 clients you may still need env vars or local stubs.
